<?php

namespace App\Http\Controllers;

use App\DataTables\InstructionRequestsDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateInstructionRequestsRequest;
use App\Http\Requests\UpdateInstructionRequestsRequest;
use App\Models\Campus;
use App\Models\User;
use App\Repositories\InstructionRequestsRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class InstructionRequestsController extends AppBaseController
{
    /** @var InstructionRequestsRepository $instructionRequestsRepository*/
    private $instructionRequestsRepository;

    public function __construct(InstructionRequestsRepository $instructionRequestsRepo)
    {
        $this->instructionRequestsRepository = $instructionRequestsRepo;
    }

    /**
     * Display a listing of the InstructionRequests.
     *
     * @param InstructionRequestsDataTable $instructionRequestsDataTable
     *
     * @return Response
     */
    public function index(InstructionRequestsDataTable $instructionRequestsDataTable)
    {
        return $instructionRequestsDataTable->render('instruction_requests.index');
    }

    /**
     * Show the form for creating a new InstructionRequests.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function create()
    {
//        return view('instruction_requests.create');
        $librarians = User::all(); // Assuming User model is your librarian model
        $classLocations = Campus::all(); // Assuming Campus model represents class locations

        return view('instruction_requests.create')
            ->with('librarians', $librarians)
            ->with('classLocations', $classLocations);
    }

    /**
     * Store a newly created InstructionRequests in storage.
     *
     * @param CreateInstructionRequestsRequest $request
     *
     * @return Response
     */
    public function store(CreateInstructionRequestsRequest $request)
    {
        $input = $request->all();

        $instructionRequests = $this->instructionRequestsRepository->create($input);

        Flash::success('Instruction Requests saved successfully.');

        return redirect(route('instructionRequests.index'));
    }

    /**
     * Display the specified InstructionRequests.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $instructionRequests = $this->instructionRequestsRepository->find($id);

        if (empty($instructionRequests)) {
            Flash::error('Instruction Requests not found');

            return redirect(route('instructionRequests.index'));
        }

        return view('instruction_requests.show')->with('instructionRequests', $instructionRequests);
    }

    /**
     * Show the form for editing the specified InstructionRequests.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function edit($id)
    {
        $instructionRequests = $this->instructionRequestsRepository->find($id);
        $librarians = User::all(); // Retrieving librarians
        $classLocations = Campus::all(); // Retrieving class locations

        if (empty($instructionRequests)) {
            Flash::error('Instruction Requests not found');
            return redirect(route('instructionRequests.index'));
        }

        return view('instruction_requests.edit')
            ->with('instructionRequests', $instructionRequests)
            ->with('librarians', $librarians)
            ->with('classLocations', $classLocations);
    }
//    public function edit($id)
//    {
//        $instructionRequests = $this->instructionRequestsRepository->find($id);
//
//        if (empty($instructionRequests)) {
//            Flash::error('Instruction Requests not found');
//
//            return redirect(route('instructionRequests.index'));
//        }
//
//        return view('instruction_requests.edit')->with('instructionRequests', $instructionRequests);
//    }

    /**
     * Update the specified InstructionRequests in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInstructionRequestsRequest $request)
    {
        $instructionRequests = $this->instructionRequestsRepository->find($id);

        if (empty($instructionRequests)) {
            Flash::error('Instruction Requests not found');

            return redirect(route('instructionRequests.index'));
        }

        $instructionRequests = $this->instructionRequestsRepository->update($request->all(), $id);

        Flash::success('Instruction Requests updated successfully.');

        return redirect(route('instructionRequests.index'));
    }

    /**
     * Remove the specified InstructionRequests from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $instructionRequests = $this->instructionRequestsRepository->find($id);

        if (empty($instructionRequests)) {
            Flash::error('Instruction Requests not found');

            return redirect(route('instructionRequests.index'));
        }

        $this->instructionRequestsRepository->delete($id);

        Flash::success('Instruction Requests deleted successfully.');

        return redirect(route('instructionRequests.index'));
    }
}
