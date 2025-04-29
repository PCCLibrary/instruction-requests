<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\InstructionRequests;
use App\Models\Instructor;
use App\Models\User;
use App\Notifications\RequestReceivedNotification;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use App\Services\TokenValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Controller for handling public instruction request form.
 */
class PublicInstructionRequestController extends Controller
{
    /** @var InstructionRequestService $instructionRequestService */
    private InstructionRequestService $instructionRequestService;

    /** @var DepartmentService $departmentService */
    private DepartmentService $departmentService;

    /** @var TokenValidationService $tokenValidationService */
    private TokenValidationService $tokenValidationService;

    /**
     * Build the class and inject the services.
     *
     * @param InstructionRequestService $instructionRequestService
     * @param DepartmentService $departmentService
     * @param TokenValidationService $tokenValidationService
     */
    public function __construct(
        InstructionRequestService $instructionRequestService,
        DepartmentService $departmentService,
        TokenValidationService $tokenValidationService,
    ) {
        $this->instructionRequestService = $instructionRequestService;
        $this->departmentService = $departmentService;
        $this->tokenValidationService = $tokenValidationService;
    }

    /**
     * Display the public form to create instruction requests.
     *
     * @return View
     */
    public function create(): View
    {
        $campuses = Campus::where('code', '!=', 'OL')->pluck('name', 'id');
        $departments = $this->departmentService->getAllDepartments();
//        $librarians = User::where('is_admin', false)->get();
        $librarians =  User::orderedLibrariansScope()->get();

        return view('index', compact('librarians', 'campuses', 'departments'));
    }

    /**
     * Store the submitted instruction request from the public form.
     *
     * @param CreateInstructionRequestRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(CreateInstructionRequestRequest $request): RedirectResponse
    {
        try {
            // Check if request is coming from Svelte application
            $isSvelteRequest = $request->header('X-Form-Source') === 'svelte';

            // For Svelte requests, validate the stateless token
            if ($isSvelteRequest) {
                $token = $request->input('_token');

                Log::info('Processing Svelte form submission', [
                    'has_token' => !empty($token),
                    'request_method' => $request->method(),
                    'headers' => $request->headers->all()
                ]);

                // Validate token
                if (!$this->tokenValidationService->validateToken($token)) {
                    Log::warning('Invalid token in Svelte form submission', [
                        'token_start' => !empty($token) ? substr($token, 0, 10) . '...' : 'none'
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid form token',
                        'errors' => ['form' => ['The form session has expired or is invalid. Please refresh and try again.']]
                    ], 419);
                }

                Log::info('Svelte form token validation successful');
            }

            // Validate and prepare input data
            $input = $request->except(['class_syllabus', 'instructor_attachments']);
            $uploadToken = $request->input('upload_token');

            Log::info('Processing instruction request submission', [
                'is_svelte' => $isSvelteRequest,
                'has_upload_token' => !empty($uploadToken),
                'upload_token' => $uploadToken,
                'form_data' => array_keys($input),
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Create instruction request with files
            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $request);

            if (empty($instructionRequest)) {
                throw new \Exception('Failed to create instruction request');
            }

            Log::info('Instruction request created', [
                'request_id' => $instructionRequest->id,
                'status' => $instructionRequest->status
            ]);

            // Associate uploaded files with the instruction request if a token was provided
            if ($uploadToken) {
                Log::info('Calling associateFiles with token', [
                    'token' => $uploadToken,
                    'request_id' => $instructionRequest->id
                ]);

                try {
                    $fileAssociationResult = app(\App\Http\Controllers\MediaController::class)->associateFiles(
                        $uploadToken,
                        $instructionRequest->id
                    );

                    Log::info('File association completed', [
                        'success' => $fileAssociationResult ? 'true' : 'false',
                        'token' => $uploadToken,
                        'request_id' => $instructionRequest->id
                    ]);

                    // Check if any files exist after association
                    $mediaCount = $instructionRequest->getMedia('materials')->count();
                    Log::info('Media count after association', [
                        'request_id' => $instructionRequest->id,
                        'media_count' => $mediaCount,
                        'collections' => [
                            'materials' => $instructionRequest->getMedia('materials')->count(),
                            'syllabus' => $instructionRequest->getMedia('syllabus')->count(),
                            'instructor_attachments' => $instructionRequest->getMedia('instructor_attachments')->count(),
                        ]
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error during file association', [
                        'token' => $uploadToken,
                        'request_id' => $instructionRequest->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('No upload token provided for file association');
            }

            // Fetch related data and append
            $instructionRequest = $this->appendAdditionalData($instructionRequest);

            Log::info('Store operation completed', [
                'request_id' => $instructionRequest->id,
                'has_details' => !is_null($instructionRequest->detail),
                'instructor' => $instructionRequest->instructor_name ?? 'unknown',
                'class' => $instructionRequest->course_name ?? 'unknown',
                'had_token' => !empty($uploadToken),
                'is_svelte' => $isSvelteRequest
            ]);

            // Return appropriate response based on request source
            if ($isSvelteRequest) {
                return response()->json([
                    'success' => true,
                    'message' => 'Instruction request submitted successfully.',
                    'request_id' => $instructionRequest->id
                ]);
            } else {
                // For standard Laravel form submissions, use redirect response
                return redirect('/')
                    ->with('success', 'Instruction request submitted successfully.')
                    ->withInput($input);
            }

        } catch (Throwable $e) {
            Log::error('Store operation failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $input ?? null,
                'is_svelte' => $isSvelteRequest ?? false
            ]);

            // Return appropriate error response based on request source
            if (isset($isSvelteRequest) && $isSvelteRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit the instruction request.',
                    'errors' => ['error' => $e->getMessage()]
                ], 422);
            } else {
                // For standard Laravel form submissions, use redirect response
                return redirect('/')
                    ->with('error', 'Failed to submit the instruction request.')
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
        }
    }

    /**
     * Fetch related data for the instruction request and append to the object.
     *
     * @param InstructionRequests $instructionRequest
     * @return InstructionRequests
     */
    protected function appendAdditionalData(InstructionRequests $instructionRequest): InstructionRequests
    {
        $instructor = Instructor::find($instructionRequest->instructor_id);
        $class = Classes::find($instructionRequest->class_id);
        $campus = Campus::find($instructionRequest->campus_id);
        $librarian = User::find($instructionRequest->librarian_id);

        $instructionRequest->instructor_name = $instructor?->display_name;
        $instructionRequest->course_name = $class?->course_name;
        $instructionRequest->campus_name = $campus?->name;
        $instructionRequest->librarian_name = $librarian?->display_name;

        return $instructionRequest;
    }
}
