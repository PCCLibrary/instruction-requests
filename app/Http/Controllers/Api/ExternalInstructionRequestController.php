<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInstructionRequestRequest;
use App\Services\InstructionRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalInstructionRequestController extends Controller
{
    /**
     * The instruction request service instance.
     */
    private InstructionRequestService $instructionRequestService;

    /**
     * Create a new controller instance.
     *
     * @param InstructionRequestService $instructionRequestService
     */
    public function __construct(InstructionRequestService $instructionRequestService)
    {
        $this->instructionRequestService = $instructionRequestService;
    }

    /**
     * Store a new instruction request from the Svelte application.
     *
     * @param CreateInstructionRequestRequest $request
     * @return JsonResponse
     */
    public function store(CreateInstructionRequestRequest $request): JsonResponse
    {
        try {
            // Validate and prepare input data
            $input = $request->except(['class_syllabus', 'instructor_attachments']);
            $uploadToken = $request->input('upload_token');

            Log::info('Processing external instruction request submission', [
                'has_token' => !empty($uploadToken),
                'token' => $uploadToken,
                'form_data' => array_keys($input),
                'source' => 'external'
            ]);

            // Create instruction request with files
            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $request);

            if (empty($instructionRequest)) {
                throw new \Exception('Failed to create instruction request');
            }

            // Associate uploaded files with the instruction request if a token was provided
            if ($uploadToken) {
                app(\App\Http\Controllers\MediaController::class)->associateFiles(
                    $uploadToken,
                    $instructionRequest->id
                );
            }

            Log::info('External store operation completed', [
                'request_id' => $instructionRequest->id,
                'has_details' => !is_null($instructionRequest->detail),
                'instructor_id' => $instructionRequest->instructor_id,
                'had_token' => !empty($uploadToken)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Instruction request submitted successfully.',
                'request_id' => $instructionRequest->id
            ]);

        } catch (Throwable $e) {
            Log::error('External store operation failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit the instruction request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
