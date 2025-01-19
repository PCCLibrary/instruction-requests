<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInstructionRequestDetailsRequest;
use App\Services\DepartmentService;
use App\Services\InstructionRequestDetailsService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstructionRequestDetailsController extends AppBaseController
{
    public function __construct(
        private readonly InstructionRequestDetailsService $instructionRequestDetailsService,
        private readonly DepartmentService $departmentService
    ) {}

    /**
     * Display a listing of the InstructionRequestDetails with Livewire PowerTable.
     *
     * @return View
     */
    public function index(): View
    {
        return view('instruction_request_details.index');
    }

    /**
     * Show the form for editing the specified InstructionRequestDetails.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $instructionRequestDetails = $this->instructionRequestDetailsService->getInstructionRequestDetailsById($id);

        if (empty($instructionRequestDetails)) {
            flash('Instruction Request Details not found')->error();
            return redirect(route('instructionRequestDetails.index'));
        }

        return view('instruction_request_details.edit')->with([
            'instructionRequestDetails' => $instructionRequestDetails,
            'librarians' => User::all()
        ]);
    }

    /**
     * Update the specified InstructionRequestDetails in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestDetailsRequest $request
     * @return RedirectResponse
     */
    public function update(int $id, UpdateInstructionRequestDetailsRequest $request): RedirectResponse
    {
        $instructionRequestDetails = $this->instructionRequestDetailsService->getInstructionRequestDetailsById($id);

        if (empty($instructionRequestDetails)) {
            flash('Instruction Request Details not found')->error();
            return redirect(route('instructionRequestDetails.index'));
        }

        try {
            $updatedDetails = $this->instructionRequestDetailsService->updateInstructionRequestDetails(
                $request->validated(),
                $instructionRequestDetails->id
            );

            if ($updatedDetails) {
                flash('Instruction Request Details updated successfully.')->success();
            } else {
                flash('Failed to update Instruction Request Details.')->error();
            }
        } catch (\Exception $e) {
            flash('Error updating Instruction Request Details: ' . $e->getMessage())->error();
        }

        return redirect(route('instructionRequestDetails.index'));
    }

    /**
     * Remove the specified InstructionRequestDetails from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $instructionRequestDetails = $this->instructionRequestDetailsService->getInstructionRequestDetailsById($id);

        if (empty($instructionRequestDetails)) {
            flash('Instruction Request Details not found')->error();
            return redirect(route('instructionRequestDetails.index'));
        }

        try {
            $this->instructionRequestDetailsService->deleteInstructionRequestDetail($id);
            flash('Instruction Request Details deleted successfully.')->success();
        } catch (\Exception $e) {
            flash('Error deleting Instruction Request Details: ' . $e->getMessage())->error();
        }

        return redirect(route('instructionRequestDetails.index'));
    }
}
