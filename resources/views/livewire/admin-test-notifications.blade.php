<div class="min-w-0">
    <div class="mb-4">
        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">
            Test Notifications
        </h4>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 min-w-0">
            <div class="space-y-4">
                <div>
                    <label for="request-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Request
                    </label>
                    <select
                        wire:model.live="selectedRequestId"
                        id="request-select"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select a Request --</option>
                        @foreach($recentRequests as $request)
                            <option value="{{ $request['id'] }}">{{ $request['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notification-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notification Type
                    </label>
                    <select
                        wire:model.live="notificationType"
                        id="notification-type"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="received">Received (New Request)</option>
                        <option value="assigned">Assigned</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div>
                    <label for="email-address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email Address
                    </label>
                    <input
                        type="email"
                        wire:model="emailAddress"
                        id="email-address"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                        placeholder="test@pcc.edu">
                </div>

                <div class="pt-2">
                    <button
                        wire:click="sendTestEmail"
                        @if(!$selectedRequestId || !$notificationType || !$emailAddress) disabled @endif
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <x-heroicon-o-paper-airplane class="w-4 h-4 mr-2" />
                        Send Test Email
                    </button>
                </div>

                @if($sendSuccess)
                    <div class="p-3 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
                        <p class="text-sm text-green-800 dark:text-green-200">
                            ✓ Test email sent successfully to {{ $emailAddress }}
                        </p>
                    </div>
                @endif

                @if($sendError)
                    <div class="p-3 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-md">
                        <p class="text-sm text-red-800 dark:text-red-200">
                            ✗ {{ $sendError }}
                        </p>
                    </div>
                @endif
            </div>

            @if($previewSubject && $previewHtml)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Preview</h5>

                    <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Subject:</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ $previewSubject }}</p>
                    </div>

                    <div class="border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden">
                        <div class="bg-white dark:bg-gray-800 p-4 max-h-96 overflow-auto" style="max-width: 100%;">
                            <div class="break-words">
                                {!! $previewHtml !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            Preview and test email notifications using recent requests.
        </p>
    </div>
</div>
