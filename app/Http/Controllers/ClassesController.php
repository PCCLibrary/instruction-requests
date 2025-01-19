<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClassesRequest;
use App\Http\Requests\UpdateClassesRequest;
use App\Repositories\ClassesRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ClassesController extends AppBaseController
{
    /** @var ClassesRepository */
    private ClassesRepository $classesRepository;

    /**
     * ClassesController constructor.
     *
     * @param ClassesRepository $classesRepo
     */
    public function __construct(ClassesRepository $classesRepo)
    {
        $this->classesRepository = $classesRepo;
    }

    /**
     * Display a listing of the Classes with Livewire PowerTable.
     *
     * @return View
     */
    public function index(): View
    {
        // Render the Livewire PowerTable for classes
        return view('classes.index');
    }

    /**
     * Show the form for creating a new Classes.
     *
     * @return View
     */
    public function create(): View
    {
        return view('classes.create');
    }

    /**
     * Store a newly created Classes in storage.
     *
     * @param CreateClassesRequest $request
     * @return RedirectResponse
     */
    public function store(CreateClassesRequest $request): RedirectResponse
    {
        $input = $request->validated();

        $this->classesRepository->create($input);

//        flash('Class saved successfully.')->success();

        return redirect(route('classes.index'));
    }

    /**
     * Display the specified Classes.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $classes = $this->classesRepository->find($id);

        if (empty($classes)) {
//            flash('Class not found.')->error();

            return redirect(route('classes.index'));
        }

        return view('classes.show')->with('classes', $classes);
    }

    /**
     * Show the form for editing the specified Classes.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $classes = $this->classesRepository->find($id);

        if (empty($classes)) {
//            flash('Class not found.')->error();

            return redirect(route('classes.index'));
        }

        return view('classes.edit')->with('classes', $classes);
    }

    /**
     * Update the specified Classes in storage.
     *
     * @param int $id
     * @param UpdateClassesRequest $request
     * @return RedirectResponse
     */
    public function update(int $id, UpdateClassesRequest $request): RedirectResponse
    {
        $classes = $this->classesRepository->find($id);

        if (empty($classes)) {
//            flash('Class not found.')->error();

            return redirect(route('classes.index'));
        }

        $input = $request->validated();

        $this->classesRepository->update($input, $id);

//        flash('Class updated successfully.')->success();

        return redirect(route('classes.index'));
    }

    /**
     * Remove the specified Classes from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $classes = $this->classesRepository->find($id);

        if (empty($classes)) {
//            flash('Class not found.')->error();

            return redirect(route('classes.index'));
        }

        $this->classesRepository->delete($id);

//        flash('Class deleted successfully.')->success();

        return redirect(route('classes.index'));
    }
}
