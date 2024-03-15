<?php

// app/Services/InstructionRequestDetailsService.php

namespace App\Services;

use App\Models\InstructionRequestDetails;
use App\Repositories\InstructionRequestDetailsRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling operations related to InstructionRequestDetails.
 */
class InstructionRequestDetailsService
{
    private $instructionRequestDetailsRepository;

    /**
     * @param InstructionRequestDetailsRepository $instructionRequestDetailsRepository
     */
    public function __construct(InstructionRequestDetailsRepository $instructionRequestDetailsRepository)
    {
        $this->instructionRequestDetailsRepository = $instructionRequestDetailsRepository;
    }

    /**
     * Get InstructionRequestDetails by instruction request ID.
     *
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function getDetailsByInstructionRequestId(int $instructionRequestId): ?InstructionRequestDetails
    {
        $detail =  InstructionRequestDetails::where('instruction_request_id', $instructionRequestId)->first();

        Log::debug('getDetailsByInstructionRequestId data:', $detail->toArray());

        return $detail;

    }

    /**
     * Get an InstructionRequestDetails by its ID.
     *
     * @param int $id
     * @return InstructionRequestDetails|null
     */
    public function getInstructionRequestDetailsById(int $id): ?InstructionRequestDetails
    {
        $detail =  $this->instructionRequestDetailsRepository->find($id);

        Log::debug('getInstructionRequestDetailsById data:', $detail->toArray());

        return $detail;

    }
    /**
     * Update InstructionRequestDetails.
     *
     * @param array $data
     *   The data to update the InstructionRequestDetails.
     * @param int $instructionRequestId
     *   The ID of the InstructionRequestDetails to update.
     *
     * @return \App\Models\InstructionRequestDetails|null
     *   The updated InstructionRequestDetails, or null if not found.
     */
    public function updateInstructionRequestDetails(array $data, int $instructionRequestId): ?InstructionRequestDetails
    {
        // Enable query logging
//        DB::enableQueryLog();

        // Get the current details by ID
        $currentDetails = $this->getInstructionRequestDetailsById($instructionRequestId);

        // Check if the details exist
        if ($currentDetails) {
            // Log the current details and data for debugging
//            Log::debug('$currentDetails: ' . json_encode($currentDetails));
//            Log::debug('$data: ' . json_encode($data));

            // Update boolean fields based on checkbox values
            $currentDetails->fill($data);

            // Handle multiple file uploads for materials
//            if (isset($data['materials']) && is_array($data['materials'])) {
//                $materialsPaths = [];
//
//                foreach ($data['materials'] as $material) {
//                    // Logic to handle materials file upload
//                    $path = $material->store('materials');
//                    $materialsPaths[] = $path;
//                }
//
//                $currentDetails->materials = $materialsPaths;
//            }
//
//            // Handle multiple file uploads for assessments
//            if (isset($data['assessments']) && is_array($data['assessments'])) {
//                $assessmentsPaths = [];
//
//                foreach ($data['assessments'] as $assessment) {
//                    // Logic to handle assessments file upload
//                    $path = $assessment->store('assessments');
//                    $assessmentsPaths[] = $path;
//                }
//
//                $currentDetails->assessments = $assessmentsPaths;
//            }

            // Explicitly set timestamps to force an update
            $currentDetails->updated_at = now();

            // Save the changes
            $saveResult = $currentDetails->save();

            // Log save result, updated attributes, and SQL queries for debugging
            Log::debug('Save result: ' . json_encode($saveResult));
//            Log::debug('Updated model attributes: ' . json_encode($currentDetails->getAttributes()));
//            Log::debug('detail SQL queries: ' . json_encode(DB::getQueryLog()));


            // Check if changes were detected
            if ($saveResult && $currentDetails->wasChanged()) {
                Log::debug('Model changes detected.');
            }

//            DB::disableQueryLog();

            return $currentDetails;
        }

        // Return null if details are not found
        return null;
    }




    /**
     * Delete an instruction request details by its ID.
     *
     * @param int $id The ID of the instruction request detail to delete.
     * @return bool True if the instruction request detail was successfully deleted, false otherwise.
     * @throws \Exception
     */
    public function deleteInstructionRequestDetail(int $id): bool
    {
        $instructionRequestDetail = $this->getInstructionRequestDetailsById($id);
        if (!$instructionRequestDetail) {
            return false;
        }

        return $this->instructionRequestDetailsRepository->delete($id);
    }
}
