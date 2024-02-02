<?php

namespace App\Http\Controllers;

use App\DataTables\CampusDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateCampusRequest;
use App\Http\Requests\UpdateCampusRequest;
use App\Repositories\CampusRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class CampusController extends AppBaseController
{
    /** @var CampusRepository $campusRepository*/
    private $campusRepository;

    public function __construct(CampusRepository $campusRepo)
    {
        $this->campusRepository = $campusRepo;
    }

    /**
     * Display a listing of the Campus.
     *
     * @param CampusDataTable $campusDataTable
     *
     * @return Response
     */
    public function index(CampusDataTable $campusDataTable)
    {
        return $campusDataTable->render('campuses.index');
    }

    /**
     * Show the form for creating a new Campus.
     *
     * @return Response
     */
    public function create()
    {
        return view('campuses.create');
    }

    /**
     * Store a newly created Campus in storage.
     *
     * @param CreateCampusRequest $request
     *
     * @return Response
     */
    public function store(CreateCampusRequest $request)
    {
        $input = $request->all();

        $campus = $this->campusRepository->create($input);

        Flash::success('Campus saved successfully.');

        return redirect(route('campuses.index'));
    }

    /**
     * Display the specified Campus.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            Flash::error('Campus not found');

            return redirect(route('campuses.index'));
        }

        return view('campuses.show')->with('campus', $campus);
    }

    /**
     * Show the form for editing the specified Campus.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            Flash::error('Campus not found');

            return redirect(route('campuses.index'));
        }

        return view('campuses.edit')->with('campus', $campus);
    }

    /**
     * Update the specified Campus in storage.
     *
     * @param int $id
     * @param UpdateCampusRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCampusRequest $request)
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            Flash::error('Campus not found');

            return redirect(route('campuses.index'));
        }

        $campus = $this->campusRepository->update($request->all(), $id);

        Flash::success('Campus updated successfully.');

        return redirect(route('campuses.index'));
    }

    /**
     * Remove the specified Campus from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            Flash::error('Campus not found');

            return redirect(route('campuses.index'));
        }

        $this->campusRepository->delete($id);

        Flash::success('Campus deleted successfully.');

        return redirect(route('campuses.index'));
    }
}
