<?php
// app/Http/Controllers/InstructionRequestDetailsController.php
// app/Http/Controllers/InstructionRequestDetailsController.php

namespace App\Http\Controllers;

use App\DataTables\InstructionRequestDetailsDataTable;
use App\Http\Requests\CreateInstructionRequestDetailsRequest;
use App\Http\Requests\UpdateInstructionRequestDetailsRequest;
use App\Services\DepartmentService;
use App\Services\InstructionRequestDetailsService;
use App\Models\Campus;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class InstructionRequestDetailsController extends AppBaseController
{
    /** @var InstructionRequestDetailsService $instructionRequestDetailsService */
    private $instructionRequestDetailsService;

    public function __construct(
        InstructionRequestDetailsService $instructionRequestDetailsService,
        DepartmentService $departmentService
    ) {
        $this->instructionRequestDetailsService = $instructionRequestDetailsService;
    }

    /**
     * Display a listing of the InstructionRequestDetails.
     *
     * @return Response
     */
    public function index()
    {
        // Assuming you have a DataTable for InstructionRequestDetails
        return (new InstructionRequestDetailsDataTable())->render('instruction_request_details.index');
    }

    /**
     * Show the form for editing the specified InstructionRequestDetails.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsService->getInstructionRequestDetailsById($id);
        $librarians = User::all(); // librarian model

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');
            return redirect(route('instructionRequestDetails.index'));
        }

//        Log::debug('$instructionRequestDetails: '.json_encode($instructionRequestDetails));

        return view('instruction_request_details.edit')
            ->with('instructionRequestDetails', $instructionRequestDetails)
            ->with('librarians', $librarians);
    }

    /**
     * Update the specified InstructionRequestDetails in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestDetailsRequest $request
     *
     * @return Response
     */
    public function update(int $id, UpdateInstructionRequestDetailsRequest $request)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsService->getInstructionRequestDetailsById($id);

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');
            return redirect(route('instructionRequestDetails.index'));
        }

        $data = $request->all();

        // Update details using the service
        $updatedDetails = $this->instructionRequestDetailsService->updateInstructionRequestDetails($data, (int)$instructionRequestDetails->id);

        if ($updatedDetails) {
            Flash::success('Instruction Request Details updated successfully.');
        } else {
            Flash::error('Failed to update Instruction Request Details.');
        }

//        Log::debug('$updatedDetails: '.json_encode($updatedDetails));

        return redirect(route('instructionRequestDetails.index'));
    }

    /**
     * Remove the specified InstructionRequestDetails from storage.
     *
     * @param int $id
     *
     * @throws Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsService->getInstructionRequestDetailsById($id);

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');
            return redirect(route('instructionRequestDetails.index'));
        }

        $this->instructionRequestDetailsService->deleteInstructionRequestDetail($id);

        Flash::success('Instruction Request Details deleted successfully.');

        return redirect(route('instructionRequestDetails.index'));
    }
}
