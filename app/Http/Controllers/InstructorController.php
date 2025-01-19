<?php

namespace App\Http\Controllers;

use App\Repositories\IRinstructorRepository; // Injected repository
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    /**
     * The instructor repository instance.
     */
    protected IRinstructorRepository $repository;

    /**
     * Constructor to inject the repository.
     */
    public function __construct(IRinstructorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the instructors.
     */
    public function index(Request $request)
    {
        // Use pagination via the repository with optional filters
        $filters = $request->only($this->repository->getFieldsSearchable());
        $instructors = $this->repository->paginate(10, $filters);

        return view('instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new instructor.
     */
    public function create()
    {
        return view('instructors.create');
    }

    /**
     * Store a new instructor in the database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $this->repository->create($validatedData);

        return redirect()->route('instructors.index')->with('success', 'Instructor created successfully.');
    }

    /**
     * Show the form for editing the specified instructor.
     */
    public function edit($id)
    {
        $instructor = $this->repository->find($id);

        return view('instructors.edit', compact('instructor'));
    }

    /**
     * Update a specific instructor.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors,email,' . $id,
            'phone' => 'nullable|string|max:20',
        ]);

        $this->repository->update($validatedData, $id);

        return redirect()->route('instructors.index')->with('success', 'Instructor updated successfully.');
    }

    /**
     * Remove the specified instructor.
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return redirect()->route('instructors.index')->with('success', 'Instructor deleted successfully.');
    }
}
