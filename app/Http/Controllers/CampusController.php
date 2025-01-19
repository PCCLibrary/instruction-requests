<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCampusRequest;
use App\Http\Requests\UpdateCampusRequest;
use App\Repositories\CampusRepository;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CampusController extends AppBaseController
{
    private CampusRepository $campusRepository;

    /**
     * CampusController constructor.
     *
     * @param CampusRepository $campusRepo
     */
    public function __construct(CampusRepository $campusRepo)
    {
        $this->campusRepository = $campusRepo;
    }

    /**
     * Display a listing of the Campus with Livewire PowerTable.
     *
     * @return View
     */
    public function index(): View
    {
        // Render the Livewire PowerTable for campuses
        return view('campuses.index');
    }

    /**
     * Show the form for creating a new Campus.
     *
     * @return View
     */
    public function create(): View
    {
        // Fetch librarians for dropdown
        $librarians = $this->getLibrarianOptions();

        return view('campuses.create')->with('librarians', $librarians);
    }

    /**
     * Store a newly created Campus in storage.
     *
     * @param CreateCampusRequest $request
     * @return RedirectResponse
     */
    public function store(CreateCampusRequest $request): RedirectResponse
    {
        $input = $request->validated();

        $this->campusRepository->create($input);
//
//        // Spatie flash message for success
//        flash('Campus saved successfully.')->success();

        return redirect(route('campuses.index'));
    }

    /**
     * Display the specified Campus details.
     *
     * @param int $id
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function show(int $id): \Illuminate\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            flash('Campus not found.')->error();

            return redirect(route('campuses.index'));
        }

        return view('campuses.show')->with('campus', $campus);
    }

    /**
     * Show the form for editing the specified Campus.
     *
     * @param int $id
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function edit(int $id): \Illuminate\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
//            flash('Campus not found.')->error();

            return redirect(route('campuses.index'));
        }

        $librarians = $this->getLibrarianOptions();

        return view('campuses.edit')->with([
            'campus' => $campus,
            'librarians' => $librarians,
        ]);
    }

    /**
     * Update the specified Campus in storage.
     *
     * @param int $id
     * @param UpdateCampusRequest $request
     * @return RedirectResponse
     */
    public function update(int $id, UpdateCampusRequest $request): RedirectResponse
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
//            flash('Campus not found.')->error();

            return redirect(route('campuses.index'));
        }

        $input = $request->validated();

        $this->campusRepository->update($input, $id);

        // Spatie flash message for success
        flash('Campus updated successfully.')->success();

        return redirect(route('campuses.index'));
    }

    /**
     * Remove the specified Campus from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
//            flash('Campus not found.')->error();

            return redirect(route('campuses.index'));
        }

        $this->campusRepository->delete($id);

        // Spatie flash message for success
//        flash('Campus deleted successfully.')->success();

        return redirect(route('campuses.index'));
    }

    /**
     * Get librarian options for forms (non-admin users).
     *
     * @return array
     */
    private function getLibrarianOptions(): array
    {
        return User::where('is_admin', false)
            ->pluck('display_name', 'id')
            ->toArray();
    }
}
