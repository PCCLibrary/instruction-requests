<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Http\Requests\UpdateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\Instructor;
use App\Models\InstructionRequests;
use App\Models\User;
use App\Notifications\RequestAcceptedNotification;
use App\Notifications\RequestAssignedNotification;
use App\Notifications\RequestReceivedNotification;
use App\Notifications\RequestRejectedNotification;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InstructionRequestController extends AppBaseController
{
    public function __construct(
        private readonly InstructionRequestService $instructionRequestService,
        private readonly DepartmentService $departmentService,
    ) {}

    /**
     * Display a listing of the InstructionRequests with Livewire PowerTable.
     *
     * @return View
     */
    public function index(): View
    {
        return view('instruction-requests.index');
    }

    /**
     * Show the form for creating a new InstructionRequests.
     *
     * @return View
     */
    public function create(): View
    {
        return view('instruction-requests.create')
            ->with([
                'instructionRequest' => null,
                'librarians' => User::where('is_admin', false)->get(),
                'campuses' => Campus::all(),
                'instructors' => Instructor::all(),
                'departments' => $this->departmentService->getAllDepartments()
            ]);
    }

    /**
     * Store a newly created InstructionRequests in storage.
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

            // Send notifications
            if ($instructionRequest->instructor) {
                $instructionRequest->instructor->notify(new RequestReceivedNotification($instructionRequest));
            }

            // Notify campus librarians
            if ($instructionRequest->campus && !empty($instructionRequest->campus->librarian_ids)) {
                Log::debug('Notifying campus librarians', [
                    'campus' => $instructionRequest->campus->name,
                    'librarian_ids' => $instructionRequest->campus->librarian_ids
                ]);

                User::whereIn('id', $instructionRequest->campus->librarian_ids)
                    ->each(function($librarian) use ($instructionRequest) {
                        $librarian->notify(new RequestReceivedNotification($instructionRequest));
                    });
            }

            session()->flash('success', 'Instruction Request saved successfully.');
            return redirect(route('instructionRequests.index'));
        } catch (\Exception $e) {
            session()->flash('error', 'Instruction Request not saved.');
            return redirect(route('instructionRequests.index'))
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified InstructionRequests.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        return view('instruction-requests.show')->with([
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
     * Show the form for editing the specified InstructionRequests.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        return view('instruction-requests.edit')->with([
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
     * Update the specified InstructionRequests in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestRequest $request
     * @return RedirectResponse
     */
    public function update(int $id, UpdateInstructionRequestRequest $request): RedirectResponse
    {
        Log::info('Controller received update request', [
            'id' => $id,
            'raw_input' => $request->all(),
            'validated_data' => $request->validated()
        ]);

        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            Log::warning('Instruction request not found', ['id' => $id]);
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        Log::info('Found instruction request', [
            'id' => $id,
            'current_status' => $instructionRequest->status,
            'has_details' => $instructionRequest->detail ? 'yes' : 'no'
        ]);

        try {
            $validatedData = $request->validated();
            Log::debug('Validated data before service call:', $validatedData);

            $oldStatus = $instructionRequest->status;
            $updated = $this->instructionRequestService->updateInstructionRequest($validatedData, $id);

            // Check if status changed to assigned
            if ($oldStatus !== $updated->status && $updated->status === 'assigned' && $updated->detail?->assigned_librarian_id) {
                $librarian = User::find($updated->detail->assigned_librarian_id);
                if ($librarian) {
                    $librarian->notify(new RequestAssignedNotification($updated));
                }
            }

            Log::info('Update completed', [
                'id' => $id,
                'updated_status' => $updated->status,
                'has_details' => $updated->detail ? 'yes' : 'no'
            ]);

            session()->flash('success', 'Instruction Request updated successfully.');
            return redirect(route('instructionRequests.edit', $id));

        } catch (\Exception $e) {
            Log::error('Error updating instruction request', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error updating Instruction Request: ' . $e->getMessage());
            return redirect(route('instructionRequests.index'))
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified InstructionRequests from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->instructionRequestService->deleteInstructionRequest($id);
            session()->flash('success', 'Instruction Request deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting Instruction Request: ' . $e->getMessage());
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
        $originalRequest = InstructionRequests::with('detail')->find($id);

        if (!$originalRequest) {
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        $newRequest = $originalRequest->replicate();
        $newRequest->status = 'copied';
        $newRequest->push();

        $newDetails = $originalRequest->detail->replicate();
        $newDetails->instruction_requests_id = $newRequest->id;
        $newDetails->push();

        session()->flash('success', 'Instruction Request copied successfully.');
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
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        $this->instructionRequestService->acceptRequest($id, auth()->id());

        if ($instructionRequest->instructor) {
            $instructionRequest->instructor->notify(new RequestAcceptedNotification($instructionRequest));
        }

        session()->flash('success', 'Instruction Request accepted.');
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
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        $this->instructionRequestService->rejectRequest($id);

        if ($instructionRequest->instructor) {
            $instructionRequest->instructor->notify(new RequestRejectedNotification($instructionRequest));
        }

        session()->flash('info', 'Instruction Request rejected.');
        return redirect(route('instructionRequests.edit', $id));
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
