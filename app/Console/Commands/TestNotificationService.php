<?php

namespace App\Console\Commands;

use App\Models\InstructionRequests;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestNotificationService extends Command
{
    protected $signature = 'test:notification-service {request_id=1}';
    protected $description = 'Test NotificationService package generation and subject lines';

    public function handle(NotificationService $notificationService): int
    {
        $requestId = (int) $this->argument('request_id');

        $this->info("Testing NotificationService with request ID: {$requestId}");

        try {
            // Test package generation
            $package = $notificationService->prepareReceivedNotificationPackage($requestId);

            $this->info("✅ Package generated successfully");
            $this->info("📧 Instructor Subject: " . $package->instructorSubject);
            $this->info("📚 Librarian Subject: " . $package->librarianSubject);
            $this->info("🔗 Dashboard URL: " . $package->dashboardUrl);

            // Test template data structure
            $templateData = $package->templateData;
            $this->info("📋 Template Data Keys: " . implode(', ', array_keys($templateData)));

            // Show key data for validation
            $this->newLine();
            $this->info("Key Template Data:");
            $this->info("  ID: " . ($templateData['id'] ?? 'missing'));
            $this->info("  Type: " . ($templateData['instruction_type'] ?? 'missing'));
            $this->info("  Department: " . ($templateData['course_department'] ?? 'missing'));
            $this->info("  Course #: " . ($templateData['course_number'] ?? 'missing'));
            $this->info("  Campus: " . ($templateData['campus_name'] ?? 'missing'));
            $this->info("  Instructor: " . ($templateData['instructor_name'] ?? 'missing'));
            $this->info("  Datetime: " . ($templateData['preferred_datetime'] ?? 'missing'));
            $this->info("  Async Date: " . ($templateData['asynchronous_instruction_ready_date'] ?? 'missing'));

            // Test subject line formatting for different types
            $this->newLine();
            $this->info("Subject Line Validation:");

            // Check instruction type mapping
            $type = $templateData['instruction_type'] ?? '';
            $expectedType = match($type) {
                'on-campus' => 'In-person',
                'remote' => 'Remote',
                'asynchronous' => 'Asynchronous',
                default => $type
            };

            $this->info("  Type Mapping: {$type} → {$expectedType}");

            // Check date formatting
            if ($type === 'asynchronous') {
                $date = $templateData['asynchronous_instruction_ready_date'] ?? '';
                $this->info("  Async Date: {$date}");
            } else {
                $datetime = $templateData['preferred_datetime'] ?? '';
                $this->info("  Preferred DateTime: {$datetime}");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error testing NotificationService: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
