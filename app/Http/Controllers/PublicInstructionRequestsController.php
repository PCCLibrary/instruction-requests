<?php

/** controller for public instruction request form */

namespace App\Http\Controllers;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\CreateInstructionRequestsRequest; // Use the correct request validation class
use App\Models\InstructionRequests; // Your model

class PublicInstructionRequestsController extends Controller
{


    public function create()
    {
        $librarians = User::pluck('name', 'id'); // Assuming you have name and id columns
        $locations = Campus::pluck('name', 'id'); // Assuming you have name and id columns

        return view('welcome', compact('librarians', 'locations'));
    }


    public function store(Request $request)
    {
        $input = $request->all();

        \Log::info($request->all()); // Temporarily log the request data

        try {
            InstructionRequests::create($input);
            // Flash a success message to the session
            return redirect('/')->with('success', 'Instruction request submitted successfully.');
        } catch (\Exception $e) {
            // Flash an error message to the session
            return redirect('/')->with('error', 'Failed to submit the instruction request.');
        }
    }

}
