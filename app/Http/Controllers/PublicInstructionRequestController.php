<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\InstructionRequest;
use App\Models\User;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
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

    public function __construct(
        InstructionRequestService $instructionRequestService,
        DepartmentService $departmentService
    ) {
        $this->instructionRequestService = $instructionRequestService;
        $this->departmentService = $departmentService;
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
        $campuses = Campus::pluck('name', 'id');
        $departments = $this->departmentService->getAllDepartments();
        $librarians = User::all(); // librarian model

//        Log::debug('Departments: ' . json_encode($departments));
//        Log::debug('Librarians: ' . json_encode($librarians->toArray()));
//        Log::debug('Campuses: ' . json_encode($campuses->toArray()));

        return view('index', compact('librarians', 'campuses', 'departments'));
    }

    /**
     * Store the submitted instruction request from the public form.
     *
     * @param CreateInstructionRequestRequest $request
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(CreateInstructionRequestRequest $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validated();

            // Initialize an empty array for files
            $files = [];

            // Check if the request has file uploads and add them to the files array
            if ($request->hasFile('class_syllabus')) {
                $files['class_syllabus'] = $request->file('class_syllabus');
            }
            if ($request->hasFile('instructor_attachments')) {
                $files['instructor_attachments'] = $request->file('instructor_attachments');
            }

            // Call the service with the validated data and file uploads
            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($validatedData, $files);

            Flash::success('Instruction request submitted successfully.');
            return redirect('/')
                ->with('success', 'Instruction request submitted successfully.')
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('Failed to submit the instruction request: ' . $e->getMessage());
            Flash::error('Failed to submit the instruction request.');
            return redirect('/')
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }


}
