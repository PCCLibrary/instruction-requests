<?php

namespace App\Http\Controllers;

use App\DataTables\InstructionRequestDataTable;
use App\Http\Requests\CreateInstructionRequestRequest;
use App\Http\Requests\UpdateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\Instructor;
use App\Models\InstructionRequest;
use App\Models\User;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use App\Services\NotificationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Throwable;
Use Exception;

/**
 *
 */
class InstructionRequestController extends AppBaseController
{
    /** @var InstructionRequestService $instructionRequestService */
    private $instructionRequestService;

    /** @var DepartmentService $departmentService */
    private $departmentService;

    /** @var NotificationService $notificationService */
    private $notificationService;

    /**
     * Build the class and inject the services
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
     * Display a listing of the InstructionRequests.
     *
     * @param InstructionRequestDataTable $instructionRequestDataTable
     *
     * @return Response
     */
    public function index(InstructionRequestDataTable $instructionRequestDataTable)
    {
        return $instructionRequestDataTable->render('instruction_requests.index');
    }

    /**
     * Show the form for creating a new InstructionRequest.
     *
     * @return View
     */
    public function create()
    {
        $departments = $this->departmentService->getAllDepartments();
        $librarians = User::where('is_admin', false)->get();
        $campuses = Campus::all(); // Reflecting change to 'campuses' for clarity
        $instructors = Instructor::all(); // grab instructors

//        Log::debug('Departments: ' . json_encode($departments->toArray()));
//        Log::debug('Librarians: ' . json_encode($librarians->toArray()));
//        Log::debug('Campuses: ' . json_encode($campuses->toArray()));
//        Log::debug('Instructors: ' . json_encode($instructors->toArray()));

        return view('instruction_requests.create')
            ->with('instructionRequest', null)
            ->with('librarians', $librarians)
            ->with('campuses', $campuses)
            ->with('instructors', $instructors)
            ->with('departments', $departments);
    }

    /**
     * Store a newly created InstructionRequest in storage.
     *
     * @param CreateInstructionRequestRequest $request
     *
     * @return Application|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function store(CreateInstructionRequestRequest $request)
    {
        // set the context for selecting the rules
        $context = 'create';

        try {

            $input = $request->except(['class_syllabus', 'instructor_attachments']); // Prepare input excluding files

            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $request);

            // Notify librarians
            $this->notificationService->librarianNotification($instructionRequest);

//            Log::debug('received request: ' . json_encode($instructionRequest));

            // Flash a success message to the session
            Flash::success('Instruction Request saved successfully.');

            return redirect(route('instructionRequests.index'))
                ->withInput();

        } catch (\Exception $e) {
            // Flash an error message and input data to the session

            Flash::error('Instruction Request not saved.');

            return redirect(route('instructionRequests.index'))
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }

    }

    /**
     * Display the specified InstructionRequest.
     *
     * @param int $id
     *
     * @return View
     */
    public function show($id)
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        $departments = $this->departmentService->getAllDepartments();
        $librarians = User::where('is_admin', false)->get();
        $campuses = Campus::all(); // Reflecting change to 'campuses' for clarity
        $instructors = Instructor::all(); // grab instructors


        $syllabus = $instructionRequest->getMedia('syllabus');
        $instructorAttachments = $instructionRequest->getMedia('instructor_attachments');
        $assessments = $instructionRequest->getMedia('assessments');
        $materials = $instructionRequest->getMedia('materials');

        if (empty($instructionRequest)) {
            Flash::error('Instruction Request not found');

            return redirect(route('instructionRequests.index'));
        }

        return view('instruction_requests.show')
            ->with('instructorAttachments', $instructorAttachments)
            ->with('syllabus', $syllabus)
            ->with('assessments', $assessments)
            ->with('materials', $materials)
            ->with('instructionRequest', $instructionRequest)
            ->with('librarians', $librarians)
            ->with('campuses', $campuses)
            ->with('instructors', $instructors)
            ->with('departments', $departments);
    }

    /**
     * Show the form for editing the specified InstructionRequest.
     *
     * @param int $id
     *
     * @return View | RedirectResponse
     */
    public function edit($id)
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);
        $departments = $this->departmentService->getAllDepartments();
        $librarians = User::where('is_admin', false)->get();
        $campuses = Campus::all();
        $instructors = Instructor::all();


        // set the context for selecting the rules
        $context = 'edit';

//        Log::debug("departments: ". json_encode($departments));

        if (empty($instructionRequest)) {
            Flash::error('Instruction Request not found');
            return redirect(route('instructionRequests.index'));
        }

        $syllabus = $instructionRequest->getMedia('syllabus');
        $instructorAttachments = $instructionRequest->getMedia('instructor_attachments');
        $assessments = $instructionRequest->getMedia('assessments');
        $materials = $instructionRequest->getMedia('materials');


//        Log::debug('instructionRequest to edit: '. json_encode($instructionRequest));

        return view('instruction_requests.edit')
            ->with('instructorAttachments', $instructorAttachments)
            ->with('syllabus', $syllabus)
            ->with('assessments', $assessments)
            ->with('materials', $materials)
            ->with('instructionRequest', $instructionRequest)
            ->with('librarians', $librarians)
            ->with('campuses', $campuses)
            ->with('instructors', $instructors)
            ->with('departments', $departments);
    }

    /**
     * Update the specified InstructionRequest in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestRequest $request
     *
     * @return RedirectResponse
     */
    public function update(int $id, UpdateInstructionRequestRequest $request)
    {
        // Retrieve the existing InstructionRequest by ID
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        // Check if the InstructionRequest was found
        if (empty($instructionRequest)) {
            Flash::error('Instruction Request not found');
            return redirect(route('instructionRequests.index'))->with('error', 'Instruction Request not found.');
        }

        try {
            // Update the InstructionRequest using the service
            $this->instructionRequestService->updateInstructionRequest($request->all(), $id);
//            Flash::success('Instruction Request updated successfully.');
        } catch (\Exception $e) {
            Flash::error('Error updating Instruction Request: ' . $e->getMessage());
            return redirect(route('instructionRequests.index'))->withErrors(['error' => $e->getMessage()]);
        }

        // Redirect back to the edit route with success message
        return redirect(route('instructionRequests.edit', $id))->with('success', 'Instruction Request updated.');
    }



    /**
     * Duplicate the selected instruction request and associated detail
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function copy(int $id)
    {
        $originalRequest = InstructionRequest::with('detail')->find($id);

        if (!$originalRequest) {
            abort(404, 'Instruction Request not found');
        }

        // Duplicate the instruction request
        $newRequest = $originalRequest->replicate();
        $newRequest->status = 'copied'; // Optionally update any fields
        $newRequest->push();

        // Duplicate the associated details
        $newDetails = $originalRequest->detail->replicate();
        $newDetails->instruction_request_id = $newRequest->id;
        $newDetails->push();

        Flash::success('Instruction Request copied successfully.');

        // Redirect to the edit view of the duplicated instruction request
        return redirect()->route('instructionRequests.edit', $newRequest->id)->with('success', 'Instruction Request duplicated successfully');
    }

    /**
     * Accept the instruction request.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function accept($id)
    {
        $this->instructionRequestService->acceptRequest($id, auth()->user()->id);
        Flash::success('Instruction Request accepted.');

        return redirect()->route('instructionRequests.edit', $id)->with('status', 'Request accepted.');
    }

    /**
     * Reject the instruction request.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function reject(int $id)
    {
        $this->instructionRequestService->rejectRequest($id);
        Flash::info('Instruction Request rejected.');
        return redirect()->route('instructionRequests.edit', $id)->with('status', 'Request rejected.');
    }

    /**
     * Remove the specified InstructionRequest from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     *@throws Exception
     *
     */
    public function destroy(int $id)
    {
        $this->instructionRequestService->deleteInstructionRequest($id);

        Flash::success('Instruction Request deleted successfully.');

        return redirect(route('instructionRequests.index'));
    }
}
