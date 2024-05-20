<?php

namespace App\Services;

use App\Contracts\InstructionRequestInterface;
use App\Http\Controllers\InstructionRequestDetailsController;
use App\Models\Classes;
use App\Models\InstructionRequest;
use App\Models\InstructionRequestDetails;
use App\Models\Instructor;
use App\Repositories\InstructionRequestDetailsRepository;
use App\Repositories\InstructionRequestRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use http\Exception;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use function Psy\debug;


/**
 *
 */
class InstructionRequestService implements InstructionRequestInterface
{
    /**
     * @var InstructionRequestRepository
     */
    protected $instructionRequestRepository;

    /**
     * @var InstructionRequestDetailsService
     */
    protected $instructionRequestDetailsService;


    /**
     * @param InstructionRequestRepository $instructionRequestRepository
     * @param InstructionRequestDetailsService $instructionRequestDetailsService
     */
    public function __construct(
        InstructionRequestRepository $instructionRequestRepository,
        InstructionRequestDetailsService $instructionRequestDetailsService)
    {
        $this->instructionRequestRepository = $instructionRequestRepository;
        $this->instructionRequestDetailsService = $instructionRequestDetailsService;

    }

    /**
     * Create a new InstructionRequest along with associated entities like Instructor, Classes,
     * and InstructionRequestDetails. Now also handles file uploads for 'syllabus' and
     * 'instructor_attachments'.
     *
     * @param array $data Data necessary for creating the instruction request and its associated entities.
     * @param Request $request
     * @return InstructionRequest The newly created InstructionRequest object.
     * @throws \Throwable
     */
    public function createNewInstructionRequest(array $data, Request $request): InstructionRequest
    {
//        Log::info('Request data:', $request->all());

        return DB::transaction(function () use ($data, $request) {
            // Existing logic for creating instructor and class
            $instructor = $this->findOrCreateInstructor($data);
            $classes = $this->findOrCreateClasses($data);

            // Prepare data with the necessary IDs and default values
            $data['instructor_id'] = $instructor->id;
            $data['class_id'] = $classes->id;
            $data['status'] = 'received';
            $data['created_by'] = $this->getCreatedBy($data);

            // Create the InstructionRequest
            $instructionRequest = $this->instructionRequestRepository->create($data);

            // Create InstructionRequestDetails
            $instructionRequestDetailData = [
                'instruction_datetime' => $data['preferred_datetime'],
                'assigned_librarian_id' => $data['librarian_id'],
                'instruction_request_id' => $instructionRequest->id,
                'instruction_duration' => $data['duration'],
                'created_by' => $data['created_by'],
                'last_updated_by' => $data['created_by'],
            ];

            $instructionRequest->detail()->create($instructionRequestDetailData);

            // Handle 'class_syllabus' uploads
            $this->handleFileUploads($request, 'class_syllabus', 'syllabus', $instructionRequest);

            // Handle 'class_syllabus' uploads
            $this->handleFileUploads($request, 'instructor_attachments', 'instructor_attachments', $instructionRequest);

            // Handle 'materials' uploads
            $this->handleFileUploads($request, 'materials', 'materials', $instructionRequest);

            // Handle 'assessments' uploads
            $this->handleFileUploads($request, 'assessments', 'assessments', $instructionRequest);

            return $instructionRequest;
        });
    }

    /**
     * Update an instruction request and its associated entities by ID.
     *
     * @param int $id The ID of the instruction request to update.
     * @param array $data Data to update the instruction request and associated entities.
     * @return InstructionRequest Updated InstructionRequest object.
     */
    public function updateInstructionRequest(array $data, int $id): InstructionRequest
    {
        // Enable query logging for debugging
//        DB::enableQueryLog();

        return DB::transaction(function () use ($id, $data) {

            $instructionRequest = $this->findInstructionRequestById($id);

            if (!$instructionRequest) {
//                Flash::error('Could not update Instruction Request.');

                throw new \Exception("InstructionRequest not found");
            }

            $currentDetails = $this->instructionRequestDetailsService->getDetailsByInstructionRequestId($instructionRequest->id);

            if ($currentDetails) {
                // Update the details using the service
                $this->instructionRequestDetailsService->updateInstructionRequestDetails($data, $currentDetails->id);
            }

            // Update logic here
            $this->instructionRequestRepository->update($data, $id);

            Log::debug('instruction request SQL queries: ' . json_encode(DB::getQueryLog()));

            Flash::success('Instruction Request updated successfully.');

            DB::disableQueryLog();

            return $instructionRequest;
        });
    }


    /**
     * Get the value for the 'created_by' field based on the input data.
     *
     * @param array $input The input data from the form.
     *
     * @return string The value for the 'created_by' field.
     *
     * @throws \InvalidArgumentException If the created by value is empty.
     */
    private function getCreatedBy(array $input): string
    {
        if (Auth::check()) {
            $createdBy = Auth::user()->display_name;
        } else {
            // Ensure the 'name' key exists in $input before accessing it
            $createdBy = $input['name'] ?? null;
        }

        if (empty($createdBy)) {
            throw new \InvalidArgumentException("Created by value cannot be empty.");
        }

        return $createdBy;
    }



    /**
     * Find an instruction request by its ID along with eager-loaded relationships.
     *
     * @param int $id The ID of the instruction request to find.
     * @return InstructionRequest|null The found InstructionRequest object, or null if not found.
     */
    public function findInstructionRequestById(int $id): ?InstructionRequest
    {
        return InstructionRequest::with(['instructor', 'campus', 'librarian', 'classes', 'detail'])
            ->find($id);
    }


    /**
     * Delete an instruction request by its ID.
     *
     * @param int $id The ID of the instruction request to delete.
     * @return bool True if the instruction request was successfully deleted, false otherwise.
     */
    public function deleteInstructionRequest(int $id): bool
    {
        $instructionRequest = $this->findInstructionRequestById($id);
        if (!$instructionRequest) {
            return false;
        }

        return $this->instructionRequestRepository->delete($id);
    }

    /**
     * Finds an existing Instructor or creates a new one based on the provided data.
     * This method uses the firstOrCreate method to avoid duplicate entries in the database.
     *
     * @param array $data Data containing Instructor information.
     * @return Instructor The found or created Instructor.
     */
    protected function findOrCreateInstructor(array $data): Instructor
    {
        $searchCriteria = ['email' => $data['email']];
        $additionalData = [
            'name' => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'pronouns' => $data['pronouns'] ?? 'None Provided',
            'phone' => $data['phone'] ?? null,
        ];

//        Log::debug('findOrCreateInstructor - Search Criteria: ' . json_encode($searchCriteria));
//        Log::debug('findOrCreateInstructor - Additional Data: ' . json_encode($additionalData));

        return Instructor::firstOrCreate($searchCriteria, $additionalData);
    }


    /**
     * Finds an existing class or creates a new one based on the provided data.
     * This method uses the firstOrCreate method to efficiently manage class information
     * and reuse it when appropriate, avoiding duplicate entries.
     *
     * @param array $data Data containing class information.
     * @return Classes The found or created class.
     */
    protected function findOrCreateClasses(array $data): Classes
    {
        $searchCriteria = [
            'department_code' => $data['department'],
            'course_number' => $data['course_number'],
            'course_crn' => $data['course_crn']
        ];

        // Default course_name is department - course number unless explicitly provided
        $courseName = $data['class_title'] ?? "{$data['department']} - {$data['course_number']}";

        $attributes = [
            'course_name' => $courseName,
        ];

//        Log::debug('findOrCreateClasses - Search Criteria: ' . json_encode($searchCriteria));
//        Log::debug('findOrCreateClasses - Attributes: ' . json_encode($attributes));

        return Classes::firstOrCreate($searchCriteria, $attributes);
    }

    /**
     * Get complete instruction requests and eager-load the associated entries.
     *
     * @param string $status
     * @param int|string $quantity
     * @return Collection|array|null
     */
    public function getRequestsByStatus(string $status, $quantity = null)
    {
        $query = InstructionRequest::with(['instructor', 'detail', 'classes', 'campus'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc');

        if ($quantity) {
            $query->take($quantity);
        }

        return $query->get();
    }

    /**
     * Handle file uploads for a given field and associate them with a media collection.
     *
     * @param Request $request The current HTTP request instance.
     * @param string $fieldName The name of the form field that contains the file(s).
     * @param string $collectionName The name of the media collection to associate the files with.
     * @param InstructionRequest $instructionRequest The instruction request instance to associate files with.
     * @return void
     */
    public function handleFileUploads(Request $request, string $fieldName, string $collectionName, InstructionRequest $instructionRequest): void
    {
        if ($request->hasFile($fieldName)) {
            Log::debug($fieldName . ' hasFile:', [$request->get($fieldName)]);
            foreach ($request->file($fieldName) as $file) {
                $media = $instructionRequest->addMedia($file)->toMediaCollection($collectionName);
                // Log successful upload
                Log::info(ucfirst($fieldName) . ' file uploaded:', ['file_name' => $media->file_name, 'collection_name' => $media->collection_name]);
            }
        }
    }

}
