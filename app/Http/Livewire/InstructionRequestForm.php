<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\DepartmentService;
use App\Services\InstructionRequestService;
use Illuminate\Support\Facades\Auth;
use App\Models\InstructionRequest;
use App\Models\Campus;
use App\Models\User;

class InstructionRequestForm extends Component
{
    use WithFileUploads;

    public $formData = [];
    public $campuses, $departments, $librarians;
    // Adjusted validation rules based on the model's rules for creation
    protected $rules = [
        'formData.librarian_id' => 'required|exists:users,id',
        'formData.campus_id' => 'required|exists:campuses,id',
        'formData.instruction_type' => 'required|string',
        'formData.department' => 'nullable|string',
        'formData.course_number' => 'nullable|string',
        'formData.course_crn' => 'nullable|string',
        'formData.number_of_students' => 'nullable|integer',
        'formData.class_syllabus.*' => 'nullable|file|mimes:txt,rtf,pdf,doc,docx|max:20480',
        'formData.class_description' => 'nullable|string',
        'formData.instructor_attachments.*' => 'nullable|file|mimes:txt,rtf,pdf,doc,docx|max:20480',
        'formData.assignment_description' => 'nullable|string',
        'formData.ada_provisions_needed' => 'boolean',
        'formData.ada_provisions_description' => 'nullable|string',
        'formData.preferred_datetime' => 'nullable|date',
        'formData.alternate_datetime' => 'nullable|date',
        'formData.duration' => 'nullable|string',
        'formData.asynchronous_instruction_ready_date' => 'nullable|date',
        'formData.need_extra_time' => 'boolean',
        'formData.extra_time_with_class' => 'nullable|string',
        'formData.received_assignment' => 'boolean',
        'formData.selected_topics' => 'boolean',
        'formData.explored_background' => 'boolean',
        'formData.written_draft' => 'boolean',
        'formData.other_learning_outcome' => 'boolean',
        'formData.other_learning_outcome_description' => 'nullable|string',
        'formData.library_instruction_description' => 'nullable|string',
        // Additional fields for creation
        'formData.name' => 'required|string|max:255',
        'formData.email' => 'required|email|max:255',
        'formData.display_name' => 'nullable|string|max:255',
        'formData.pronouns' => 'nullable|string|max:255',
        'formData.phone' => 'nullable|string|max:255',
    ];
    /**
     * @var mixed
     */
    private $instructionRequestService;

    public function mount(DepartmentService $departmentService)
    {
        // Fetch necessary data for select fields similar to the controller
        $this->campuses = Campus::pluck('name', 'id');
        $this->departments = $departmentService->getAllDepartments();
        $this->librarians = User::all();

//        $this->instructionRequestService = $instructionRequestService;

        Log::debug('Service in mount:', ['service' => $this->instructionRequestService]);

    }

    /**
     * Handle form submission.
     *
     * Validates the form data, handles file uploads, and attempts to create a new instruction request
     * using the provided service. If successful, flashes a success message and resets the form. If an
     * exception occurs, flashes an error message.
     */
    public function submit()
    {

        // Manually resolve the InstructionRequestService from the Laravel service container
        $instructionRequestService = app(InstructionRequestService::class);

        // Validate the form data against the defined rules
        $this->validate();

        try {
            // Prepare data for the service, excluding file inputs for simplicity
            $input = $this->formData;

            // This assumes that the service can handle array data and manage Livewire file uploads as needed.
            $instructionRequest = $this->instructionRequestService->createNewInstructionRequest($input, $this);

            Log::debug('Service in submit:', ['service' => $this->instructionRequestService]);

            // Flash a success message to the session
            Flash::success('Instruction request submitted successfully.');

            // Reset the form fields to their initial state
            $this->reset();
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Failed to submit the instruction request: ' . $e->getMessage());

            // Flash an error message to the session
            Flash::error('Failed to submit the instruction request: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.instruction-requests.form', [
            'campuses' => $this->campuses,
            'departments' => $this->departments,
            'librarians' => $this->librarians,
        ]);
    }
}
