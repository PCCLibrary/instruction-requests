<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Models\Campus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;


class UserController extends AppBaseController
{
    /** @var UserRepository */
    private UserRepository $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the User with Livewire PowerTable.
     *
     * @return View
     */
    public function index(): View
    {
        // Render the Livewire PowerTable for users
        return view('users.index');
    }

    /**
     * Show the form for creating a new User.
     *
     * @return View
     */
    public function create(): View
    {
        $campuses = Campus::ordered()->pluck('name', 'id');

        return view('users.create')->with('campuses', $campuses);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     * @return RedirectResponse
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        $input = $request->validated();

        $user = $this->userRepository->create($input);

//        flash('User saved successfully.')->success();

        session()->flash('success', 'User created successfully.');

        // Check if "Save & Close" was clicked
        if ($request->has('saveAndClose')) {
            return redirect(route('users.index'));
        }

        // Otherwise, redirect to edit the newly created user
        return redirect(route('users.edit', $user->id));
    }

    /**
     * Display the specified User.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
//            flash('User not found')->error();
            session()->flash('error', 'User not found');
            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
//            flash('User not found')->error();
            session()->flash('error', 'User not found');
            return redirect(route('users.index'));
        }

        $campuses = Campus::ordered()->pluck('name', 'id');

        return view('users.edit')
            ->with('user', $user)
            ->with('campuses', $campuses);
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function update(int $id, UpdateUserRequest $request): RedirectResponse
    {
        $user = $this->userRepository->find($id);

        Log::info('Update Data:', [
            'id' => $id,
            'input' => $request->validated(),
            'user' => $user
        ]);

        if (empty($user)) {
//            flash('User not found')->error();
            session()->flash('error', 'User not found');
            return redirect(route('users.index'));
        }

        $input = $request->validated();

        $this->userRepository->update($input, $id);

        session()->flash('success', 'User updated successfully.');

        // Check if "Save & Close" was clicked
        if ($request->has('saveAndClose')) {
            return redirect(route('users.index'));
        }

        // Otherwise, stay on the edit page
        return redirect(route('users.edit', $id));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
//            flash('User not found')->error();
            session()->flash('error', 'User not found');
            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

//        flash('User deleted successfully.')->success();
        session()->flash('success', 'User deleted successfully.');
        return redirect(route('users.index'));
    }
}
