<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestDetailsRequest;
use App\Http\Requests\UpdateInstructionRequestDetailsRequest;
use App\Repositories\InstructionRequestDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class InstructionRequestDetailsController extends AppBaseController
{
    /** @var InstructionRequestDetailsRepository $instructionRequestDetailsRepository*/
    private $instructionRequestDetailsRepository;

    public function __construct(InstructionRequestDetailsRepository $instructionRequestDetailsRepo)
    {
        $this->instructionRequestDetailsRepository = $instructionRequestDetailsRepo;
    }

    /**
     * Display a listing of the InstructionRequestDetails.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsRepository->all();

        return view('instruction_request_details.index')
            ->with('instructionRequestDetails', $instructionRequestDetails);
    }

    /**
     * Show the form for creating a new InstructionRequestDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('instruction_request_details.create');
    }

    /**
     * Store a newly created InstructionRequestDetails in storage.
     *
     * @param CreateInstructionRequestDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateInstructionRequestDetailsRequest $request)
    {
        $input = $request->all();

        $instructionRequestDetails = $this->instructionRequestDetailsRepository->create($input);

        Flash::success('Instruction Request Details saved successfully.');

        return redirect(route('instructionRequestDetails.index'));
    }

    /**
     * Display the specified InstructionRequestDetails.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsRepository->find($id);

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');

            return redirect(route('instructionRequestDetails.index'));
        }

        return view('instruction_request_details.show')->with('instructionRequestDetails', $instructionRequestDetails);
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
        $instructionRequestDetails = $this->instructionRequestDetailsRepository->find($id);

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');

            return redirect(route('instructionRequestDetails.index'));
        }

        return view('instruction_request_details.edit')->with('instructionRequestDetails', $instructionRequestDetails);
    }

    /**
     * Update the specified InstructionRequestDetails in storage.
     *
     * @param int $id
     * @param UpdateInstructionRequestDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInstructionRequestDetailsRequest $request)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsRepository->find($id);

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');

            return redirect(route('instructionRequestDetails.index'));
        }

        $instructionRequestDetails = $this->instructionRequestDetailsRepository->update($request->all(), $id);

        Flash::success('Instruction Request Details updated successfully.');

        return redirect(route('instructionRequestDetails.index'));
    }

    /**
     * Remove the specified InstructionRequestDetails from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $instructionRequestDetails = $this->instructionRequestDetailsRepository->find($id);

        if (empty($instructionRequestDetails)) {
            Flash::error('Instruction Request Details not found');

            return redirect(route('instructionRequestDetails.index'));
        }

        $this->instructionRequestDetailsRepository->delete($id);

        Flash::success('Instruction Request Details deleted successfully.');

        return redirect(route('instructionRequestDetails.index'));
    }
}
