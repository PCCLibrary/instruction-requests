<?php

namespace App\Http\Controllers;

use App\DataTables\InstructorDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateInstructorRequest;
use App\Http\Requests\UpdateinstructorRequest;
use App\Repositories\InstructorRepository;
use Laracasts\Flash\Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Response;

class InstructorController extends AppBaseController
{
    /** @var InstructorRepository $InstructorRepository*/
    private $InstructorRepository;

    public function __construct(InstructorRepository $InstructorRepo)
    {
        $this->InstructorRepository = $InstructorRepo;
    }

    /**
     * Display a listing of the Instructor.
     *
     * @param InstructorDataTable $instructorDataTable
     *
     * @return Response
     */
    public function index(InstructorDataTable $instructorDataTable)
    {
        return $instructorDataTable->render('instructors.index');
    }

    /**
     * Show the form for creating a new Instructor.
     *
     * @return Response
     */
    public function create()
    {
        return view('instructors.create');
    }

    /**
     * Store a newly created Instructor in storage.
     *
     * @param CreateInstructorRequest $request
     *
     * @return Response
     */
    public function store(CreateInstructorRequest $request)
    {
        $input = $request->all();

        $instructor = $this->InstructorRepository->create($input);

        Flash::success('Instructor saved successfully.');

        return redirect(route('instructors.index'));
    }

    /**
     * Display the specified Instructor.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $instructor = $this->InstructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        return view('instructors.show')->with('Instructor', $instructor);
    }

    /**
     * Show the form for editing the specified Instructor.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $instructor = $this->InstructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        return view('instructors.edit')->with('Instructor', $instructor);
    }

    /**
     * Update the specified Instructor in storage.
     *
     * @param int $id
     * @param UpdateinstructorRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateinstructorRequest $request)
    {
        $instructor = $this->InstructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        $instructor = $this->InstructorRepository->update($request->all(), $id);

        Flash::success('Instructor updated successfully.');

        return redirect(route('instructors.index'));
    }

    /**
     * Remove the specified Instructor from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $instructor = $this->InstructorRepository->find($id);

        if (empty($instructor)) {
            Flash::error('Instructor not found');

            return redirect(route('instructors.index'));
        }

        $this->InstructorRepository->delete($id);

        Flash::success('Instructor deleted successfully.');

        return redirect(route('instructors.index'));
    }
}
