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

    public function __construct(CampusRepository $campusRepo)
    {
        $this->campusRepository = $campusRepo;
    }

    public function index(): View
    {
        return view('campuses.index');
    }

    public function create(): View
    {
        $librarians = $this->getLibrarianOptions();
        return view('campuses.create', compact('librarians'));
    }

    public function store(CreateCampusRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $this->campusRepository->create($input);

        return redirect()->route('campuses.index')
            ->with('success', 'Campus created successfully.');
    }

    public function show(int $id): View|RedirectResponse
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Campus not found.');
        }

        return view('campuses.show', compact('campus'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Campus not found.');
        }

        $librarians = $this->getLibrarianOptions();
        return view('campuses.edit', compact('campus', 'librarians'));
    }

    public function update(int $id, UpdateCampusRequest $request): RedirectResponse
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Campus not found.');
        }

        $input = $request->validated();
        $this->campusRepository->update($input, $id);

        return redirect()->route('campuses.index')
            ->with('success', 'Campus updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $campus = $this->campusRepository->find($id);

        if (empty($campus)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Campus not found.');
        }

        $this->campusRepository->delete($id);

        return redirect()->route('campuses.index')
            ->with('success', 'Campus deleted successfully.');
    }

    private function getLibrarianOptions(): array
    {
        return User::orderedLibrariansScope()
            ->whereNotIn('id', [2]) // Exclude user ID 2
            ->get()
            ->pluck('display_name', 'id')
            ->toArray();
    }
}
