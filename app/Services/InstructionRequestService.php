<?php

namespace App\Services;

use App\Contracts\InstructionRequestServiceInterface;
use App\Models\Classes;
use App\Models\InstructionRequest;
use App\Models\Instructor;
use App\Repositories\InstructionRequestRepository;
use App\Contracts\InstructionRequestDetailsServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InstructionRequestService implements InstructionRequestServiceInterface
{
    private InstructionRequestRepository $repository;
    private InstructionRequestDetailsServiceInterface $detailsService;

    public function __construct(
        InstructionRequestRepository $repository,
        InstructionRequestDetailsServiceInterface $detailsService
    ) {
        $this->repository = $repository;
        $this->detailsService = $detailsService;
    }

    public function createNewInstructionRequest(array $data, Request $request): InstructionRequest
    {
        return DB::transaction(function () use ($data, $request) {
            $instructor = $this->findOrCreateInstructor($data);
            $classes = $this->findOrCreateClasses($data);

            $data['instructor_id'] = $instructor->id;
            $data['class_id'] = $classes->id;
            $data['status'] = 'received';
            $data['created_by'] = $this->getCreatedBy($data);

            $instructionRequest = $this->repository->create($data);

            // Create associated details
            $instructionRequest->detail()->create([
                'instruction_datetime' => $data['preferred_datetime'],
                'assigned_librarian_id' => $data['librarian_id'],
                'instruction_request_id' => $instructionRequest->id,
                'instruction_duration' => $data['duration'],
                'created_by' => $data['created_by'],
                'last_updated_by' => $data['created_by'],
            ]);

            // Handle file uploads
            $this->processFileUploads($request, $instructionRequest);

            return $instructionRequest->load(['detail', 'instructor', 'classes']);
        });
    }

    public function updateInstructionRequest(array $data, int $id): InstructionRequest
    {
        return DB::transaction(function () use ($data, $id) {
            $instructionRequest = $this->findInstructionRequestById($id);

            if (!$instructionRequest) {
                throw new \Exception('Instruction request not found');
            }

            if ($details = $instructionRequest->detail) {
                $this->detailsService->updateInstructionRequestDetails($data, $id);
            }

            $this->repository->update($data, $id);

            return $instructionRequest->fresh(['detail', 'instructor', 'classes']);
        });
    }

    public function findInstructionRequestById(int $id): ?InstructionRequest
    {
        return $this->repository->find($id);
    }

    public function deleteInstructionRequest(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $request = $this->findInstructionRequestById($id);

            if (!$request) {
                return false;
            }

            return $this->repository->delete($id);
        });
    }

    public function acceptRequest(int $id, int $userId): void
    {
        DB::transaction(function () use ($id, $userId) {
            $request = $this->findInstructionRequestById($id);

            if ($request && $request->status === 'assigned' &&
                $request->detail->assigned_librarian_id === $userId) {
                $request->update(['status' => 'accepted']);
                $request->detail->update(['assigned_librarian_id' => $userId]);
            }
        });
    }

    public function rejectRequest(int $id): void
    {
        DB::transaction(function () use ($id) {
            $request = $this->findInstructionRequestById($id);

            if ($request && $request->status === 'assigned') {
                $request->update(['status' => 'received']);
                $request->detail->update(['assigned_librarian_id' => null]);
            }
        });
    }

    public function getRequestsByStatus(string $status, ?int $quantity = null): Collection
    {
        $query = $this->repository->model()::with(['instructor', 'detail', 'classes', 'campus'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc');

        return $quantity ? $query->take($quantity)->get() : $query->get();
    }

    public function handleFileUploads(
        Request $request,
        string $fieldName,
        string $collectionName,
        InstructionRequest $instructionRequest
    ): void {
        if ($request->hasFile($fieldName)) {
            foreach ($request->file($fieldName) as $file) {
                $instructionRequest->addMedia($file)->toMediaCollection($collectionName);
            }
        }
    }

    private function processFileUploads(Request $request, InstructionRequest $instructionRequest): void
    {
        $fileTypes = [
            'class_syllabus' => 'syllabus',
            'instructor_attachments' => 'instructor_attachments',
            'materials' => 'materials',
            'assessments' => 'assessments'
        ];

        foreach ($fileTypes as $field => $collection) {
            $this->handleFileUploads($request, $field, $collection, $instructionRequest);
        }
    }

    private function getCreatedBy(array $input): string
    {
        return Auth::check() ? Auth::user()->display_name : ($input['name'] ?? 'Unknown');
    }

    private function findOrCreateInstructor(array $data): Instructor
    {
        return Instructor::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'display_name' => $data['display_name'] ?? $data['name'],
                'pronouns' => $data['pronouns'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]
        );
    }

    private function findOrCreateClasses(array $data): Classes
    {
        return Classes::updateOrCreate(
            [
                'department_code' => $data['department'],
                'course_number' => $data['course_number'],
                'course_crn' => $data['course_crn']
            ],
            ['course_name' => $data['class_title'] ?? "{$data['department']} - {$data['course_number']}"]
        );
    }
}
