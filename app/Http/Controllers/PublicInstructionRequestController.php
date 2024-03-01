<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstructionRequestRequest;
use App\Models\Campus;
use App\Models\InstructionRequest;
use App\Models\User;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

/**
 * Controller for handling public instruction request form.
 */
class PublicInstructionRequestController extends Controller
{
    /** @var InstructionRequestService $instructionRequestService */
    private $instructionRequestService;

    /** @var DepartmentService $departmentService */
    private $departmentService;

    public function __construct(
        InstructionRequestService $instructionRequestService,
        DepartmentService $departmentService
    ) {
        $this->instructionRequestService = $instructionRequestService;
        $this->departmentService = $departmentService;
    }

    /**
     * Display the public form to create instruction requests.
     *
     * @return View
     */
    public function create()
    {
        $departments = $this->departmentService->getAllDepartments();
//            ->mapWithKeys(function ($item) {
//                // Check if $item is an array
////                if (is_array($item) && isset($item['pcc_code']) && isset($item['pcc_name'])) {
//                    return [$item['pcc_code'] => strtoupper($item['pcc_code']) . ' - '. $item['pcc_name']];
////                } else {
////                    return []; // Return an empty array if $item is not an array or does not have the expected keys
////                }
//            });

        // Retrieve necessary data for form
        $librarians = User::pluck('name', 'id');
        $campuses = Campus::pluck('name', 'id');

        Log::debug('Departments: ' . json_encode($departments));
        Log::debug('Librarians: ' . json_encode($librarians->toArray()));
        Log::debug('Campuses: ' . json_encode($campuses->toArray()));

        return view('index', compact('librarians', 'campuses', 'departments'));
    }

    /**
     * Store the submitted instruction request from the public form.
     *
     * @param CreateInstructionRequestRequest $request
     *
     * @return RedirectResponse
     */
    public function store(CreateInstructionRequestRequest $request)
    {
        try {
            $input = $request->all();

            Log::info($input); // Temporarily log the request data

            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input);

            // Flash a success message to the session
            return redirect('/')->with('success', 'Instruction request submitted successfully.');
        } catch (\Exception $e) {
            // Flash an error message and input data to the session
            return redirect('/')
                ->with('error', 'Failed to submit the instruction request.')
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

}
