<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Http\Requests\UpdateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\Instructor;
use App\Models\InstructionRequest;
use App\Models\User;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstructionRequestController extends AppBaseController
{
    public function __construct(
        private readonly InstructionRequestService $instructionRequestService,
        private readonly DepartmentService $departmentService,
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Display a listing of the InstructionRequests with Livewire PowerTable.
     *
     * @return View
     */
    public function index(): View
    {
        return view('instruction_requests.index');
    }

    /**
     * Show the form for creating a new InstructionRequest.
     *
     * @return View
     */
    public function create(): View
    {
        return view('instruction_requests.create')
            ->with([
                'instructionRequest' => null,
                'librarians' => User::where('is_admin', false)->get(),
                'campuses' => Campus::all(),
                'instructors' => Instructor::all(),
                'departments' => $this->departmentService->getAllDepartments()
            ]);
    }

    /**
     * Store a newly created InstructionRequest in storage.
     *
     * @param CreateInstructionRequestRequest $request
     * @return RedirectResponse
     */
    public function store(CreateInstructionRequestRequest $request): RedirectResponse
    {
        try {
            $input = $request->except(['class_syllabus', 'instructor_attachments']);
            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $request);

            // Fetch related data and append to the instruction request object
            $instructionRequest = $this->appendAdditionalData($instructionRequest);

            // Notify based on the status
            $this->notificationService->notifyBasedOnStatus($instructionRequest);

            flash('Instruction Request saved successfully.')->success();
            return redirect(route('instructionRequests.index'));
        } catch (\Exception $e) {
            flash('Instruction Request not saved.')->error();
            return redirect(route('instructionRequests.index'))
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified InstructionRequest.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            flash('Instruction Request not found')->error();
            return redirect(route('instructionRequests.index'));
        }

        return view('instruction_requests.show')->with([
            'instructionRequest' => $instructionRequest,
            'librarians' => User::where('is_admin', false)->get(),
            'campuses' => Campus::all(),
            'instructors' => Instructor::all(),
            'departments' => $this->departmentService->getAllDepartments(),
            'syllabus' => $instructionRequest->getMedia('syllabus'),
            'instructorAttachments' => $instructionRequest->getMedia('instructor_attachments'),
            'assessments' => $instructionRequest->getMedia('assessments'),
            'materials' => $instructionRequest->getMedia('materials')
        ]);
    }

    /**
     * Show the form for editing the specified InstructionRequest.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            flash('Instruction Request not found')->error();
            return redirect(route('instructionRequests.index'));
        }

        return view('instruction_requests.edit')->with([
            'instructionRequest' => $instructionRequest,
            'librarians' => User::where('is_admin', false)->get(),
            'campuses' => Campus::all(),
            'instructors' => Instructor::all(),
            'departments' => $this->departmentService->getAllDepartments(),
            'syllabus' => $instructionRequest->getMedia('syllabus'),
            'instructorAttachments' => $instructionRequest->getMedia('instructor_attachments'),
            'assessments' => $instructionRequest->getMedia('assessments'),
            'materials' => $instructionRequest->getMedia('materials')
        ]);
    }

    /**
     * Update the specified InstructionRequest in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestRequest $request
     * @return RedirectResponse
     */
    public function update(int $id, UpdateInstructionRequestRequest $request): RedirectResponse
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            flash('Instruction Request not found')->error();
            return redirect(route('instructionRequests.index'));
        }

        try {
            $this->instructionRequestService->updateInstructionRequest($request->validated(), $id);
            flash('Instruction Request updated successfully.')->success();
        } catch (\Exception $e) {
            flash('Error updating Instruction Request: ' . $e->getMessage())->error();
            return redirect(route('instructionRequests.index'))->withErrors(['error' => $e->getMessage()]);
        }

        return redirect(route('instructionRequests.edit', $id));
    }

    /**
     * Remove the specified InstructionRequest from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->instructionRequestService->deleteInstructionRequest($id);
            flash('Instruction Request deleted successfully.')->success();
        } catch (\Exception $e) {
            flash('Error deleting Instruction Request: ' . $e->getMessage())->error();
        }

        return redirect(route('instructionRequests.index'));
    }

    /**
     * Duplicate the selected instruction request and associated detail.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function copy(int $id): RedirectResponse
    {
        $originalRequest = InstructionRequest::with('detail')->find($id);

        if (!$originalRequest) {
            flash('Instruction Request not found')->error();
            return redirect(route('instructionRequests.index'));
        }

        $newRequest = $originalRequest->replicate();
        $newRequest->status = 'copied';
        $newRequest->push();

        $newDetails = $originalRequest->detail->replicate();
        $newDetails->instruction_request_id = $newRequest->id;
        $newDetails->push();

        flash('Instruction Request copied successfully.')->success();
        return redirect(route('instructionRequests.edit', $newRequest->id));
    }

    /**
     * Accept the instruction request.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function accept(int $id): RedirectResponse
    {
        $this->instructionRequestService->acceptRequest($id, auth()->id());
        flash('Instruction Request accepted.')->success();
        return redirect(route('instructionRequests.edit', $id));
    }

    /**
     * Reject the instruction request.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function reject(int $id): RedirectResponse
    {
        $this->instructionRequestService->rejectRequest($id);
        flash('Instruction Request rejected.')->info();
        return redirect(route('instructionRequests.edit', $id));
    }

    /**
     * Fetch related data for the instruction request and append to the object.
     *
     * @param InstructionRequest $instructionRequest
     * @return InstructionRequest
     */
    protected function appendAdditionalData(InstructionRequest $instructionRequest): InstructionRequest
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
