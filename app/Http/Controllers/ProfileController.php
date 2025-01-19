<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends AppBaseController
{
    /**
     * Display the user's profile.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('profile.index')->with('user', $request->user());
    }

    /**
     * Update the user's profile information.
     *
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            flash('Profile updated successfully.')->success();
            return redirect(route('profile.index'));  // Changed from profile.edit
        } catch (\Exception $e) {
            flash('Error updating profile: ' . $e->getMessage())->error();
            return redirect(route('profile.index'))->withInput();  // Changed from profile.edit
        }
    }

    /**
     * Delete the user's account.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();

            Auth::logout();
            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            flash('Account deleted successfully.')->success();
            return redirect('/');
        } catch (\Exception $e) {
            flash('Error deleting account: ' . $e->getMessage())->error();
            return redirect(route('profile.index'))->withInput();  // Changed from profile.edit
        }
    }
}
