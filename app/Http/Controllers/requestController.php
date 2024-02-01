<?php

namespace App\Http\Controllers;

use App\DataTables\requestDataTable;
use App\Http\Requests;
use App\Http\Requests\CreaterequestRequest;
use App\Http\Requests\UpdaterequestRequest;
use App\Repositories\requestRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class requestController extends AppBaseController
{
    /** @var requestRepository $requestRepository*/
    private $requestRepository;

    public function __construct(requestRepository $requestRepo)
    {
        $this->requestRepository = $requestRepo;
    }

    /**
     * Display a listing of the request.
     *
     * @param requestDataTable $requestDataTable
     *
     * @return Response
     */
    public function index(requestDataTable $requestDataTable)
    {
        return $requestDataTable->render('requests.index');
    }

    /**
     * Show the form for creating a new request.
     *
     * @return Response
     */
    public function create()
    {
        return view('requests.create');
    }

    /**
     * Store a newly created request in storage.
     *
     * @param CreaterequestRequest $request
     *
     * @return Response
     */
    public function store(CreaterequestRequest $request)
    {
        $input = $request->all();

        $request = $this->requestRepository->create($input);

        Flash::success('Request saved successfully.');

        return redirect(route('requests.index'));
    }

    /**
     * Display the specified request.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $request = $this->requestRepository->find($id);

        if (empty($request)) {
            Flash::error('Request not found');

            return redirect(route('requests.index'));
        }

        return view('requests.show')->with('request', $request);
    }

    /**
     * Show the form for editing the specified request.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $request = $this->requestRepository->find($id);

        if (empty($request)) {
            Flash::error('Request not found');

            return redirect(route('requests.index'));
        }

        return view('requests.edit')->with('request', $request);
    }

    /**
     * Update the specified request in storage.
     *
     * @param int $id
     * @param UpdaterequestRequest $request
     *
     * @return Response
     */
    public function update($id, UpdaterequestRequest $request)
    {
        $request = $this->requestRepository->find($id);

        if (empty($request)) {
            Flash::error('Request not found');

            return redirect(route('requests.index'));
        }

        $request = $this->requestRepository->update($request->all(), $id);

        Flash::success('Request updated successfully.');

        return redirect(route('requests.index'));
    }

    /**
     * Remove the specified request from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $request = $this->requestRepository->find($id);

        if (empty($request)) {
            Flash::error('Request not found');

            return redirect(route('requests.index'));
        }

        $this->requestRepository->delete($id);

        Flash::success('Request deleted successfully.');

        return redirect(route('requests.index'));
    }
}
