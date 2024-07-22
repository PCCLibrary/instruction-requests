<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\InstructionRequest;
use App\Models\Instructor;
use App\Models\User;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Throwable;

/**
 * Controller for handling public instruction request form.
 */
class PublicInstructionRequestController extends Controller
{
    /** @var InstructionRequestService $instructionRequestService */
    private $instructionRequestService;

    /** @var DepartmentService $departmentService */
    private $departmentService;

    /** @var NotificationService $notificationService */
    private $notificationService;

    /**
     * Build the class and inject the services.
     *
     * @param InstructionRequestService $instructionRequestService
     * @param DepartmentService $departmentService
     * @param NotificationService $notificationService
     */
    public function __construct(
        InstructionRequestService $instructionRequestService,
        DepartmentService $departmentService,
        NotificationService $notificationService
    ) {
        $this->instructionRequestService = $instructionRequestService;
        $this->departmentService = $departmentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display the public form to create instruction requests.
     *
     * @return View
     */
    public function create()
    {
        $departments = $this->departmentService->getAllDepartments();

        // Retrieve necessary data for form
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
    public function store(CreateInstructionRequestRequest $request)
    {
        try {
            // Validate the request data
            $input = $request->except(['class_syllabus', 'instructor_attachments']); // Prepare input excluding files

            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $request);

            // Fetch related data and append to the instruction request object
            $instructionRequest = $this->appendAdditionalData($instructionRequest);

            // Notify based on the status
            $this->notificationService->notifyBasedOnStatus($instructionRequest);

//            Log::debug('received request: ' . json_encode($instructionRequest));

            // Flash a success message to the session
            Flash::success('Instruction Request saved successfully.');

            return redirect('/')
                ->with('success', 'Instruction request submitted successfully.')
                ->withInput();
        } catch (Throwable $e) {
            // Flash an error message and input data to the session
            Flash::error('Instruction Request not saved.');

            return redirect('/')
                ->with('error', 'Failed to submit the instruction request.')
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
    /**
     * Fetch related data for the instruction request and append to the object.
     *
     * @param InstructionRequest $instructionRequest
     * @return InstructionRequest
     */
    protected function appendAdditionalData($instructionRequest)
    {
        $instructor = Instructor::find($instructionRequest->instructor_id);
        $class = Classes::find($instructionRequest->class_id);
        $campus = Campus::find($instructionRequest->campus_id);
        $librarian = User::find($instructionRequest->librarian_id);

        $instructionRequest->instructor_name = $instructor->display_name ?? null;
        $instructionRequest->course_name = $class->course_name ?? null;
        $instructionRequest->campus_name = $campus->name ?? null;
        $instructionRequest->librarian_name = $librarian->display_name ?? null;

        return $instructionRequest;
    }
}
