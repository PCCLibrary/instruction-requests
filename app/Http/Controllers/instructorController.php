<?php

namespace App\Http\Controllers;

use App\DataTables\instructorDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateinstructorRequest;
use App\Http\Requests\UpdateinstructorRequest;
use App\Repositories\instructorRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class instructorController extends AppBaseController
{
    /** @var instructorRepository $instructorRepository*/
    private $instructorRepository;

    public function __construct(instructorRepository $instructorRepo)
    {
        $this->instructorRepository = $instructorRepo;
    }

    /**
     * Display a listing of the instructor.
     *
     * @param instructorDataTable $instructorDataTable
     *
     * @return Response
     */
    public function index(instructorDataTable $instructorDataTable)
    {
        return $instructorDataTable->render('instructors.index');
    }

    /**
     * Show the form for creating a new instructor.
     *
     * @return Response
     */
    public function create()
    {
        return view('instructors.create');
    }

    /**
     * Store a newly created instructor in storage.
     *
     * @param CreateinstructorRequest $request
     *
     * @return Response
     */
    public function store(CreateinstructorRequest $request)
    {
        $input = $request->all();

        $instructor = $this->instructorRepository->create($input);

        Flash::success('Instructor saved successfully.');

        return redirect(route('instructors.index'));
    }

    /**
     * Display the specified instructor.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $instructor = $this->instructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        return view('instructors.show')->with('instructor', $instructor);
    }

    /**
     * Show the form for editing the specified instructor.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $instructor = $this->instructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        return view('instructors.edit')->with('instructor', $instructor);
    }

    /**
     * Update the specified instructor in storage.
     *
     * @param int $id
     * @param UpdateinstructorRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateinstructorRequest $request)
    {
        $instructor = $this->instructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        $instructor = $this->instructorRepository->update($request->all(), $id);

        Flash::success('Instructor updated successfully.');

        return redirect(route('instructors.index'));
    }

    /**
     * Remove the specified instructor from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $instructor = $this->instructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        $this->instructorRepository->delete($id);

        Flash::success('Instructor deleted successfully.');

        return redirect(route('instructors.index'));
    }
}
