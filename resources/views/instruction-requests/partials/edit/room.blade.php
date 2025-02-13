@if($instructionRequest->campus->name)
    <div class="flex items-start space-x-4 mb-6">  <div class="flex-1"> <h4 class="text-sm font-medium text-gray-900">Campus:</h4>
            <p class="mt-1 text-sm text-gray-600">{{ $instructionRequest->campus->name }}</p>
        </div>

        <div class="flex-1"> <label for="room" class="block text-sm font-medium text-gray-700">Instruction Classroom</label>
            <input type="text" id="room" name="room"
                   value="{{ old('room', $instructionRequest->detail->room ) }}"
                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

    </div>
@endif
