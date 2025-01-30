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

    /**
     * Build the class and inject the services.
     *
     * @param InstructionRequestService $instructionRequestService
     * @param DepartmentService $departmentService
     */
    public function __construct(
        InstructionRequestService $instructionRequestService,
        DepartmentService $departmentService,
    ) {
        $this->instructionRequestService = $instructionRequestService;
        $this->departmentService = $departmentService;
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
        $librarians = User::where('is_admin', false)->get();

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
            // Validate and prepare input data
            $input = $request->except(['class_syllabus', 'instructor_attachments']);

            // Create instruction request with files
            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $request);

            if (empty($instructionRequest)) {
                throw new \Exception('Failed to create instruction request');
            }

            // Fetch related data and append
            $instructionRequest = $this->appendAdditionalData($instructionRequest);

//            Log::debug('Store operation completed', [
//                'request_id' => $instructionRequest->id,
//                'has_details' => !is_null($instructionRequest->detail),
//                'instructor' => $instructionRequest->instructor_name,
//                'class' => $instructionRequest->course_name
//            ]);

            return redirect('/')
                ->with('success', 'Instruction request submitted successfully.')
                ->withInput($input);

        } catch (Throwable $e) {
            Log::error('Store operation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $input ?? null
            ]);

            return redirect('/')
                ->with('error', 'Failed to submit the instruction request.')
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
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
