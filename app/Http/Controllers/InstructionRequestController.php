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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class InstructionRequestController extends AppBaseController
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
     * @return Response
     */
    public function create()
    {
        $departments = collect($this->departmentService->getAllDepartments())
            ->mapWithKeys(function ($item) {
                return [$item['pcc_code'] => strtoupper($item['pcc_code']) . ' - '. $item['pcc_name']];
            });

        $librarians = User::all(); // librarian model
        $campuses = Campus::all(); // Reflecting change to 'campuses' for clarity
        $instructors = Instructor::all(); // grab instructors

        Log::debug('Departments: ' . json_encode($departments->toArray()));
        Log::debug('Librarians: ' . json_encode($librarians->toArray()));
        Log::debug('Campuses: ' . json_encode($campuses->toArray()));
        Log::debug('Instructors: ' . json_encode($instructors->toArray()));

        return view('instruction_requests.create')
            ->with('librarians', $librarians)
            ->with('campuses', $campuses)
            ->with('instructors', $instructors)
            ->with('departments', $departments);
    }

    /**
     * Store a newly created InstructionRequest in storage.
     *
     * @param \App\Http\Requests\CreateInstructionRequestRequest $request
     *
     * @return Response
     */
    public function store(CreateInstructionRequestRequest $request)
    {
        $input = $request->all();

        $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input);

        Flash::success('Instruction Request saved successfully.');

        return redirect(route('instructionRequests.index'));
    }

    /**
     * Display the specified InstructionRequest.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            Flash::error('Instruction Request not found');

            return redirect(route('instructionRequests.index'));
        }

        return view('instruction_requests.show')->with('instructionRequest', $instructionRequest);
    }

    /**
     * Show the form for editing the specified InstructionRequest.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);
        $departments = $this->departmentService->getAllDepartments();
        $librarians = User::all();
        $campuses = Campus::all();
        $instructors = Instructor::all();

        Log::debug("departments: ". json_encode($departments));

        if (empty($instructionRequest)) {
            Flash::error('Instruction Request not found');
            return redirect(route('instructionRequests.index'));
        }

        Log::debug('instructionRequest to edit: '. json_encode($instructionRequest));

        return view('instruction_requests.edit')
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
     * @param \App\Http\Requests\UpdateInstructionRequestRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInstructionRequestRequest $request)
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);

        if (empty($instructionRequest)) {
            Flash::error('Instruction Request not found');
            return redirect(route('instructionRequests.index'));
        }

        $instructionRequest = $this->instructionRequestService->updateInstructionRequest($request->all(), $id);

        Flash::success('Instruction Request updated successfully.');

        return redirect(route('instructionRequests.index'));
    }

    /**
     * Remove the specified InstructionRequest from storage.
     *
     * @param int $id
     *
     * @return Response
     *@throws \Exception
     *
     */
    public function destroy($id)
    {
        $this->instructionRequestService->deleteInstructionRequest($id);

        Flash::success('Instruction Request deleted successfully.');

        return redirect(route('instructionRequests.index'));
    }
}
