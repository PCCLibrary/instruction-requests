<?php

namespace App\Http\Controllers;

use App\DataTables\CampusDataTable;
use App\Http\Requests\CreateCampusRequest;
use App\Http\Requests\UpdateCampusRequest;
use App\Repositories\CampusRepository;
use Laracasts\Flash\Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Response;
use App\Models\User;

class CampusController extends AppBaseController
{
    /** @var CampusRepository */
    private $campusRepository;

    /**
     * CampusController constructor.
     * @param CampusRepository $campusRepo
     */
    public function __construct(CampusRepository $campusRepo)
    {
        $this->campusRepository = $campusRepo;
    }

    /**
     * Display a listing of the Campus.
     *
     * @param CampusDataTable $campusDataTable
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
    public function create() : Response
    {
        // Load librarians for select dropdown
        $librarians = User::where('is_admin', false)->pluck('display_name', 'id');

        return view('campuses.create')
            ->with('librarians', $librarians);
    }

    /**
     * Store a newly created Campus in storage.
     *
     * @param CreateCampusRequest $request
     * @return Response
     */
    public function store(CreateCampusRequest $request) : Response
    {
        $input = $request->all();

        // Ensure 'librarian_ids' is correctly encoded as JSON
        if ($request->has('librarian_ids')) {
            $input['librarian_ids'] = json_encode($request->librarian_ids);
        }

        $campus = $this->campusRepository->create($input);

        Flash::success('Campus saved successfully.');

        return redirect(route('campuses.index'));
    }


    /**
     * Update the specified Campus in storage.
     *
     * Serializes the 'librarian_ids' array from the request to JSON before updating.
     *
     * @param int $id
     * @param UpdateCampusRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, UpdateCampusRequest $request)
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            Flash::error('Campus not found');

            return redirect(route('campuses.index'));
        }

        $input = $request->all();

        // Ensure 'librarian_ids' is correctly encoded as JSON
        if ($request->has('librarian_ids')) {
            $input['librarian_ids'] = json_encode($request->librarian_ids);
        }

        $campus = $this->campusRepository->update($input, $id);

        Flash::success('Campus updated successfully.');

        return redirect(route('campuses.index'));
    }


    /**
     * Display the specified Campus.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id) : Response
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
     * @return Response
     */
    public function edit($id)
    {
        $campus = $this->campusRepository->find($id);
        $librarians = User::all()->pluck('display_name', 'id');

        // Assuming 'librarian_ids' is stored as a JSON string in the database
        $campus->librarian_ids = json_decode($campus->librarian_ids, true);

        if (empty($campus)) {
            Flash::error('Campus not found');

            return redirect(route('campuses.index'));
        }

        return view('campuses.edit')
            ->with('campus', $campus)
            ->with('librarians', $librarians);
    }


    /**
     * Remove the specified Campus from storage.
     *
     * @param int $id
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
