<?php

namespace App\Services;

use App\Contracts\InstructionRequestServiceInterface;
use App\Models\Classes;
use App\Models\InstructionRequests;
use App\Models\Instructor;
use App\Repositories\InstructionRequestRepository;
use App\Contracts\InstructionRequestDetailsServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructionRequestService implements InstructionRequestServiceInterface
{
    /**
     * @var InstructionRequestRepository
     */
    private InstructionRequestRepository $repository;
    /**
     * @var InstructionRequestDetailsServiceInterface
     */
    private InstructionRequestDetailsServiceInterface $detailsService;

    /**
     * @param InstructionRequestRepository $repository
     * @param InstructionRequestDetailsServiceInterface $detailsService
     */
    public function __construct(
        InstructionRequestRepository $repository,
        InstructionRequestDetailsServiceInterface $detailsService
    ) {
        $this->repository = $repository;
        $this->detailsService = $detailsService;
    }

    /**
     * @param array $data
     * @param Request $request
     * @return InstructionRequests
     */
    public function createNewInstructionRequest(array $data, Request $request): InstructionRequests
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
                'instruction_requests_id' => $instructionRequest->id,
                'instruction_duration' => $data['duration'],
                'created_by' => $data['created_by'],
                'last_updated_by' => $data['created_by'],
            ]);

            // Handle file uploads
            $this->processFileUploads($request, $instructionRequest);

            return $instructionRequest->load(['detail', 'instructor', 'classes']);
        });
    }

    /**
     * @param array $data
     * @param int $id
     * @return InstructionRequests
     */

    public function updateInstructionRequest(array $data, int $id): InstructionRequests
    {
        Log::info('Service received update data', [
            'id' => $id,
            'raw_data' => $data,
            'assigned_librarian_id' => $data['assigned_librarian_id'] ?? 'not set'
        ]);

        return DB::transaction(function () use ($data, $id) {
            $instructionRequest = $this->findInstructionRequestById($id);

            if (!$instructionRequest) {
                Log::error('Instruction request not found in service', ['id' => $id]);
                throw new \Exception('Instruction request not found');
            }

            // Separate main request data and details data
            $mainRequestData = array_intersect_key($data, array_flip([
                'campus_id',
                'instruction_type',
                'status', // Added status to details data
                'department',
                'course_number',
                'course_crn',
                'number_of_students',
                'class_description',
                'assignment_description',
                'ada_provisions_needed',
                'ada_provisions_description',
                'preferred_datetime',
                'alternate_datetime',
                'duration',
                'asynchronous_instruction_ready_date',
                'extra_time_with_class',
                'learning_outcomes',
                'received_assignment',
                'selected_topics',
                'explored_background',
                'written_draft',
                'other_learning_outcome',
                'other_learning_outcome_description',
                'library_instruction_description',
                'desired_student_outcomes',
                'genai_discussion_interest',
                'other_notes',
            ]));

            $detailsData = array_intersect_key($data, array_flip([
                'instruction_duration',
                'instruction_datetime',
                'class_notes',
                'video',
                'non_video',
                'modified_tutorial',
                'embedded',
                'research_guide',
                'handout',
                'developed_assignment',
                'other_materials',
                'other_describe',
                'materials',
                'assessment_notes',
                'assessments',
                'assigned_librarian_id',  // Add this to the details fields
            ]));

            // Handle librarian assignment in details (using assigned_librarian_id)
            if (isset($data['assigned_librarian_id'])) {
                Log::info('Preparing librarian assignment', [
                    'current_assigned_librarian' => $instructionRequest->detail->assigned_librarian_id ?? null,
                    'new_assigned_librarian' => $data['assigned_librarian_id']
                ]);
            }

            // Update main request (explicitly excluding librarian_id)
            if (!empty($mainRequestData)) {
                $this->repository->update($mainRequestData, $id);
            }

            // Update details if they exist and if we have details data
            if ($instructionRequest->detail && (!empty($detailsData))) {
                Log::info('Before details update', [
                    'current_assigned_librarian' => $instructionRequest->detail->assigned_librarian_id,
                    'new_assigned_librarian' => $detailsData['assigned_librarian_id'] ?? null,
                    'details_data' => $detailsData
                ]);

                $detailsData['last_updated_by'] = auth()->user()?->display_name ?? 'System';
                $updatedDetails = $this->detailsService->updateInstructionRequestDetails($detailsData, $id);

                Log::info('After details update', [
                    'success' => $updatedDetails ? 'yes' : 'no',
                    'final_assigned_librarian' => $updatedDetails?->assigned_librarian_id
                ]);
            }

            // Handle file uploads if request object is available
            if (request()->hasFile('materials') || request()->hasFile('assessments')) {
                $this->processFileUploads(request(), $instructionRequest);
            }

            // Return fresh instance with relationships
            return $instructionRequest->fresh(['detail', 'instructor', 'classes']);
        });
    }
    /**
     * Find an instruction request by ID.
     *
     * @param int $id
     * @return InstructionRequests|null
     */
    public function findInstructionRequestById(int $id): ?InstructionRequests
    {
        /** @var InstructionRequests|null $result */
        $result = $this->repository->find($id);
        return $result;
    }

    /**
     * @param int $id
     * @return bool
     */
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

    /**
     * @param int $id
     * @param int $userId
     * @return void
     */
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

    /**
     * @param int $id
     * @return void
     */
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

    /**
     * Get requests by status.
     *
     * @param string $status
     * @param int|null $quantity
     * @return Collection
     */
    public function getRequestsByStatus(string $status, ?int $quantity = null): Collection
    {
        $modelClass = $this->repository->model();
        $query = (new $modelClass)
            ->with(['instructor', 'detail', 'classes', 'campus'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc');

        return $quantity ? $query->take($quantity)->get() : $query->get();
    }

    /**
     * @param Request $request
     * @param string $fieldName
     * @param string $collectionName
     * @param InstructionRequests $instructionRequest
     * @return void
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function handleFileUploads(
        Request $request,
        string $fieldName,
        string $collectionName,
        InstructionRequests $instructionRequest
    ): void {
        Log::debug("handleFileUploads called", [
            'fieldName' => $fieldName,
            'collectionName' => $collectionName,
            'files' => $request->file($fieldName)
        ]);
        if ($request->hasFile($fieldName)) {
            foreach ($request->file($fieldName) as $file) {
                $instructionRequest->addMedia($file)->toMediaCollection($collectionName);
            }
        }
    }

    /**
     * @param Request $request
     * @param InstructionRequests $instructionRequest
     * @return void
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    private function processFileUploads(Request $request, InstructionRequests $instructionRequest): void
    {

        Log::debug('Starting file upload process', [
            'has_materials' => $request->hasFile('materials'),
            'has_assessments' => $request->hasFile('assessments'),
            'request_files' => $request->allFiles()
        ]);

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

    /**
     * @param array $input
     * @return string
     */
    private function getCreatedBy(array $input): string
    {
        return Auth::check() ? Auth::user()->display_name : ($input['name'] ?? 'Unknown');
    }


    /**
     * Find or create an instructor based on email.
     *
     * @param array $data
     * @return Instructor
     */
    private function findOrCreateInstructor(array $data): Instructor
    {
        return Instructor::query()->updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'display_name' => $data['display_name'] ?? $data['name'],
                'pronouns' => $data['pronouns'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]
        );
    }

    /**
     * Find or create a class based on department code, course number, and CRN.
     *
     * @param array $data
     * @return Classes
     */
    private function findOrCreateClasses(array $data): Classes
    {
        return Classes::query()->updateOrCreate(
            [
                'department_code' => $data['department'],
                'course_number' => $data['course_number'],
                'course_crn' => $data['course_crn']
            ],
            [
                'course_name' => $data['class_title'] ?? "{$data['department']} - {$data['course_number']}"
            ]
        );
    }
}
