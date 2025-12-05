<div class="min-w-0">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        Test Notifications
    </h3>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <!-- 2-column grid: controls on left, preview on right -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Left Column: Controls -->
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
                        Email Address (Your Account)
                    </label>
                    <input
                        type="email"
                        wire:model="emailAddress"
                        id="email-address"
                        readonly
                        disabled
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                        placeholder="test@pcc.edu">
                </div>

                @if($sendSuccess)
                    <div class="p-3 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
                        <p class="text-sm text-green-800 dark:text-green-200">
                            ✓ Test email sent successfully
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

            <!-- Right Column: Preview (Always Rendered) -->
            <div class="flex flex-col min-h-0">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Preview</h4>

                @if($previewSubject && $previewHtml)
                    <!-- Subject Line -->
                    <div class="flex-shrink-0 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 border-b-0 rounded-t-md p-3 mb-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Subject:</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ $previewSubject }}</p>
                    </div>

                    <!-- Email Preview in iframe (Isolated) -->
                    <div class="border border-gray-300 dark:border-gray-600 rounded-b-md overflow-hidden flex-grow mb-4">
                        <iframe
                            srcdoc="{{ $previewHtml }}"
                            class="w-full h-full border-0"
                            style="min-height: 500px;"
                        ></iframe>
                    </div>

                    <!-- Send Button Below Preview -->
                    <button
                        wire:click="sendTestEmail"
                        @if(!$selectedRequestId || !$notificationType) disabled @endif
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <x-heroicon-o-paper-airplane class="w-4 h-4 mr-2" />
                        Send Test Email
                    </button>
                @else
                    <!-- Empty State -->
                    <div class="flex-grow flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8">
                        <div class="text-center">
                            <x-heroicon-o-envelope class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Select a request and notification type to preview
                            </p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
        Preview and test email notifications using recent requests.
    </p>
</div>
