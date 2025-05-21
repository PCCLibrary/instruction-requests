<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Http\Requests\UpdateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\Instructor;
use App\Models\InstructionRequests;
use App\Models\User;
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
        private readonly \App\Services\CalendarService $calendarService,
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
                'librarians' => User::orderedLibrariansScope()->get(),
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
//            'librarians' => User::where('is_admin', false)->get(),
            'librarians' => User::orderedLibrariansScope()->get(),
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
        try {
            $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

            if (empty($instructionRequest)) {
                session()->flash('error', 'Instruction Request not found.');
                return redirect(route('instructionRequests.index'));
            }

            // Check if we have a toast flash message from a calendar deletion
            if (session()->has('toast')) {
                // The masmerise/livewire-toaster package will automatically handle this toast
                // No need for additional processing
            }

            // Check if we're returning after a calendar event deletion
            if (request()->has('calendar_deleted') && request()->get('calendar_deleted') === 'true') {
                // Ensure fresh data after deletion
                $instructionRequest->refresh();
            }

            // Log the current lock state for debugging
            Log::debug('Lock state in edit method', [
                'request_id' => $id,
                'locked' => $instructionRequest->isLocked(),
                'locked_by' => $instructionRequest->locked_by,
                'locked_at' => $instructionRequest->locked_at,
                'current_user' => auth()->id(),
            ]);

            // Check for reload scenario (not locked, but locked_by is current user and locked_at is recent)
            $isReloadScenario = !$instructionRequest->isLocked() &&
                               $instructionRequest->locked_by === auth()->id() &&
                               $instructionRequest->locked_at &&
                               now()->diffInMinutes($instructionRequest->locked_at) < 5;

            if ($isReloadScenario) {
                Log::info('Controller detected reload scenario', [
                    'request_id' => $id,
                    'user_id' => auth()->id(),
                    'time_since_locked' => $instructionRequest->locked_at ? now()->diffInSeconds($instructionRequest->locked_at) : null
                ]);
            }

            // Check if locked by someone else
            if ($instructionRequest->isLocked() && $instructionRequest->locked_by !== auth()->id()) {
                $locker = $instructionRequest->lockedBy;
                session()->flash('warning', "{$locker->display_name} is currently editing this request.");
                return redirect(route('instructionRequests.index'));
            } elseif ($instructionRequest->isLocked() && $instructionRequest->locked_by === auth()->id()) {
                // Record is locked by current user - allow them to continue editing
                Log::info('User continuing their own locked edit session', [
                    'request_id' => $id,
                    'user_id' => auth()->id()
                ]);
                // No warning toast needed, proceed with edit
            }

            // Try to lock or reacquire the lock for this user
            try {
                $this->instructionRequestService->lockRequest($id);

                // Log success after locking
                Log::info('Lock acquired successfully in controller', [
                    'request_id' => $id,
                    'user_id' => auth()->id(),
                    'was_reload_scenario' => $isReloadScenario
                ]);
            } catch (\Exception $e) {
                // This should only happen if locked by someone else after our first check
                Log::error('Failed to acquire lock in controller', [
                    'request_id' => $id,
                    'error' => $e->getMessage(),
                    'was_reload_scenario' => $isReloadScenario
                ]);

                session()->flash('warning', $e->getMessage());
                return redirect(route('instructionRequests.index'));
            }

            // Ensure we have the most recent data
            $instructionRequest->refresh();

            // Debug log to verify status
            Log::debug('Instruction request status in edit method', [
                'request_id' => $id,
                'status' => $instructionRequest->status,
                'has_calendar_event' => $instructionRequest->relationLoaded('googleCalendarEvent') ? ($instructionRequest->googleCalendarEvent ? 'yes' : 'no') : 'not loaded'
            ]);

            // Ensure calendar event relationship is loaded
            if (!$instructionRequest->relationLoaded('googleCalendarEvent')) {
                $instructionRequest->load('googleCalendarEvent');
            }

            // Get the materials media collection
            $materials = $instructionRequest->getMedia('materials');

            return view('instruction-requests.edit')->with([
                'instructionRequest' => $instructionRequest,
                'librarians' => User::orderedLibrariansScope()->get(),
                'campuses' => Campus::all(),
                'instructors' => Instructor::all(),
                'departments' => $this->departmentService->getAllDepartments(),
                'materials' => $materials
            ]);

        } catch (\Exception $e) {
            Log::error('Error in edit method', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error opening request for editing: ' . $e->getMessage());
            return redirect(route('instructionRequests.index'));
        }
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
        // Enhanced logging for debugging
        Log::info('Controller received update request', [
            'id' => $id,
            'raw_input' => $request->all(),
            'validated_data' => $request->validated(),
            'assigned_librarian_id' => $request->input('assigned_librarian_id')
        ]);

        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            Log::warning('Instruction request not found', ['id' => $id]);
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        // Check if this user has lock before proceeding
        if ($instructionRequest->isLocked() && $instructionRequest->locked_by !== auth()->id()) {
            $locker = $instructionRequest->lockedBy;
            session()->flash('error', "Cannot save changes: {$locker->display_name} is currently editing this request.");
            return redirect(route('instructionRequests.index'));
        }

        Log::info('Found instruction request', [
            'id' => $id,
            'current_status' => $instructionRequest->status,
            'has_details' => $instructionRequest->detail ? 'yes' : 'no'
        ]);

        try {
            $validatedData = $request->validated();
            // Comprehensive detailed logging of all request data
            Log::debug('FULL REQUEST DATA', $request->all());
            Log::debug('VALIDATED REQUEST DATA', $validatedData);

            // Specially log the assigned_librarian_id
            if (isset($validatedData['assigned_librarian_id'])) {
                Log::info('CRITICAL FIELD CHECK: assigned_librarian_id in validated data', [
                    'value' => $validatedData['assigned_librarian_id'],
                    'type' => gettype($validatedData['assigned_librarian_id']),
                    'raw_request_value' => $request->input('assigned_librarian_id')
                ]);
            } else {
                Log::warning('assigned_librarian_id NOT FOUND in validated data');
            }

            $updated = $this->instructionRequestService->updateInstructionRequest($validatedData, $id);

            Log::info('Update completed', [
                'id' => $id,
                'updated_status' => $updated->status,
                'has_details' => $updated->detail ? 'yes' : 'no',
                'detail_id' => $updated->detail ? $updated->detail->id : 'none',
                'assigned_librarian_id' => $updated->detail ? $updated->detail->assigned_librarian_id : 'none'
            ]);

            // After successful update, release the lock if saveAndClose was provided
            if ($request->has('saveAndClose')) {
                // For save and close actions, fully clear the lock info
                $this->instructionRequestService->unlockRequest($id, false, false);
                session()->flash('success', 'Instruction Request updated and closed successfully.');
                return redirect(route('instructionRequests.index'));
            }

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
            // Check if the record is locked before attempting to delete
            $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

            if (!$instructionRequest) {
                session()->flash('error', 'Instruction Request not found.');
                return redirect(route('instructionRequests.index'));
            }

            // Prevent deletion of records locked by others
            if ($instructionRequest->isLocked() && $instructionRequest->locked_by !== auth()->id()) {
                $locker = $instructionRequest->lockedBy;
                session()->flash('warning', "Cannot delete: This request is currently being edited by {$locker->display_name}.");
                return redirect(route('instructionRequests.index'));
            }

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

        session()->flash('info', 'Instruction Request rejected.');
        return redirect(route('instructionRequests.edit', $id));
    }

    /**
     * Delete a Google Calendar event for an instruction request.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteCalendarEvent(int $id): RedirectResponse
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            session()->flash('error', 'Instruction Request not found.');
            return redirect(route('instructionRequests.index'));
        }

        // Load the calendar event if not already loaded
        if (!$instructionRequest->relationLoaded('googleCalendarEvent')) {
            $instructionRequest->load('googleCalendarEvent');
        }

        if (!$instructionRequest->googleCalendarEvent) {
            session()->flash('error', 'No calendar event found for this request.');
            return redirect(route('instructionRequests.edit', $id));
        }

        // Use calendar service to delete the event
        $result = $this->calendarService->deleteEvent($instructionRequest->googleCalendarEvent);

        if ($result) {
            session()->flash('success', 'Calendar event deleted successfully.');
        } else {
            session()->flash('error', 'Failed to delete calendar event.');
        }

        return redirect(route('instructionRequests.edit', $id));
    }

    /**
     * Unlock an instruction request.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function unlock(int $id): RedirectResponse
    {
        try {
            // For explicit unlock actions, fully clear the lock info (don't preserve)
            $this->instructionRequestService->unlockRequest($id, false, false);
            session()->flash('success', 'Instruction Request unlocked successfully.');
            return redirect(route('instructionRequests.index'));
        } catch (\Exception $e) {
            session()->flash('error', 'Error unlocking request: ' . $e->getMessage());
            return redirect(route('instructionRequests.index'));
        }
    }

    /**
     * Cancel editing and release the lock, then redirect to index.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function cancelUnlock(int $id): RedirectResponse
    {
        try {
            // Fully clear the lock (don't preserve info)
            $this->instructionRequestService->unlockRequest($id, false, false);

            // No need for a success message for cancel operations
            return redirect(route('instructionRequests.index'));
        } catch (\Exception $e) {
            // If there's an error releasing the lock, log it but still redirect
            Log::error('Error canceling and unlocking request: ' . $e->getMessage());
            return redirect(route('instructionRequests.index'));
        }
    }

    /**
     * Refresh the lock on an instruction request.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshLock(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            // Attempt to refresh the lock by getting a new timestamp
            $request = $this->instructionRequestService->lockRequest($id);

            return response()->json([
                'success' => true,
                'message' => 'Lock refreshed successfully',
                'request_id' => $id
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to refresh lock', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request_id' => $id
            ]);
        }
    }

    /**
     * Release the lock on an instruction request.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function releaseLock(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            // For AJAX requests (like beforeunload), preserve lock info to help with reload detection
            $preserveInfo = true;

            // Check if the request includes a specific instruction to fully clear all lock info
            if (request()->has('clear_all') && request()->input('clear_all') === true) {
                $preserveInfo = false;
            }

            $this->instructionRequestService->unlockRequest($id, false, $preserveInfo);

            return response()->json([
                'success' => true,
                'message' => 'Lock released successfully',
                'request_id' => $id
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to release lock', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request_id' => $id
            ]);
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
