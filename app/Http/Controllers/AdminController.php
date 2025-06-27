<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $adminUsers = User::where('is_admin', true)->orderBy('display_name')->get();
        return view('admin.index', compact('adminUsers'));
    }

    public function toggleAssignmentAvailability(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        // Only allow toggling admin users
        if (!$user->is_admin) {
            return redirect()->route('admin.index')->with('error', 'Can only toggle assignment availability for admin users.');
        }

        $user->available_for_assignment = !$user->available_for_assignment;
        $user->save();

        $status = $user->available_for_assignment ? 'enabled' : 'disabled';

        return redirect()->route('admin.index')->with('success', "Assignment availability {$status} for {$user->display_name}.");
    }
}
