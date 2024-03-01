<?php

namespace App\Services;

use App\Contracts\InstructionRequestInterface;
use App\Models\Classes;
use App\Models\InstructionRequest;
use App\Models\InstructionRequestDetails;
use App\Models\Instructor;
use App\Repositories\InstructionRequestRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * @param InstructionRequestRepository $instructionRequestRepository
     */
    public function __construct(InstructionRequestRepository $instructionRequestRepository)
    {
        $this->instructionRequestRepository = $instructionRequestRepository;
    }
    /**
     * Create a new InstructionRequest along with associated entities like Instructor, Classes, and InstructionRequestDetails.
     *
     * This method handles the business logic of creating a new instruction request. It ensures that an Instructor and class
     * are either found or created based on the provided data, then proceeds to create an instruction request and its
     * associated details. If certain optional fields like 'pronouns' are not provided, a default value is assigned.
     *
     * @param array $data Data necessary for creating the instruction request and its associated entities.
     * @return InstructionRequest The newly created InstructionRequest object.
     * @throws \Throwable
     */
    public function createNewInstructionRequest(array $data): InstructionRequest
    {
        return DB::transaction(function () use ($data) {
            // Find or create the Instructor and class first
            $instructor = $this->findOrCreateInstructor($data);
            $classes = $this->findOrCreateClasses($data);

//            Log::debug('returned from findOrCreateInstructor: '. json_encode($Instructor));
//            Log::debug('returned from findOrCreateClasses: '. json_encode($classes));


            // Update $data with necessary IDs
            $data['instructor_id'] = $instructor->id;
            $data['class_id'] = $classes->id;
            $data['status'] = 'pending';

            $data['created_by'] = $this->getCreatedBy($data);


            Log::debug('Final data for creation: ' . json_encode($data));


            // Create the InstructionRequest
            $instructionRequest = $this->instructionRequestRepository->create($data);

            Log::debug('$instructionRequest: '. json_encode($instructionRequest));

            // After successfully creating InstructionRequest, create details
            $instructionRequestDetailData = [
                'librarian_id' => $data['librarian_id'],  // Add the librarian_id from the original data
                'instruction_request_id' => $instructionRequest->id,
                'created_by' => $data['created_by'],  // Add the created_by from the original data
                'last_updated_by' => $data['created_by'],  // Add the created_by from the original data

            ];

            Log::debug('Data for creating InstructionRequestDetails: ' . json_encode($instructionRequestDetailData));

            $instructionRequest->detail()->create($instructionRequestDetailData);


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
        return DB::transaction(function () use ($id, $data) {
            $instructionRequest = $this->findInstructionRequestById($id);

            if (!$instructionRequest) {
                throw new \Exception("InstructionRequest not found");
            }

            // Update logic here
            return $this->instructionRequestRepository->update($data, $id);
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
    private function getCreatedBy(array $input)
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
     * Find an instruction request by its ID.
     *
     * @param int $id The ID of the instruction request to find.
     * @return InstructionRequest|null The found InstructionRequest object, or null if not found.
     */
    public function findInstructionRequestById(int $id): InstructionRequest
    {
        return $this->instructionRequestRepository->find($id);
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
    protected function findOrCreateInstructor(array $data)
    {
        $searchCriteria = ['email' => $data['email']];
        $additionalData = [
            'name' => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'pronouns' => $data['pronouns'] ?? 'None Provided',
            'phone' => $data['phone'] ?? null,
        ];

        Log::debug('findOrCreateInstructor - Search Criteria: ' . json_encode($searchCriteria));
        Log::debug('findOrCreateInstructor - Additional Data: ' . json_encode($additionalData));

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
    protected function findOrCreateClasses(array $data)
    {
        $searchCriteria = [
            'department_code' => $data['department'],
            'course_number' => $data['course_number']
        ];

        // Default course_name is department - course number unless explicitly provided
        $courseName = $data['class_title'] ?? "{$data['department']} - {$data['course_number']}";

        $attributes = [
            'course_name' => $courseName,
        ];

        Log::debug('findOrCreateClasses - Search Criteria: ' . json_encode($searchCriteria));
        Log::debug('findOrCreateClasses - Attributes: ' . json_encode($attributes));

        return Classes::firstOrCreate($searchCriteria, $attributes);
    }

    /**
     * get complete instruction request and eager-load the associated entries
     * @param string $status
     * @return array|null
     */
    public function getRequestsByStatus(string $status)
    {
        return InstructionRequest::with(['instructor', 'classes'])
            ->where('status', $status)
            ->get();
    }

}
