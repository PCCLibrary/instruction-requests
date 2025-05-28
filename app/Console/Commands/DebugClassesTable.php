<?php

namespace App\Console\Commands;

use App\Models\Classes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugClassesTable extends Command
{
    protected $signature = 'debug:classes-table';
    protected $description = 'Debug the classes table to understand the column issue';

    public function handle()
    {
        $this->info('=== Debugging Classes Table ===');

        // 1. Check if table exists
        $this->info('1. Checking if classes table exists...');
        if (Schema::hasTable('classes')) {
            $this->info('✅ classes table exists');
        } else {
            $this->error('❌ classes table does not exist');
            return;
        }

        // 2. List all columns
        $this->info('2. Listing all columns in classes table:');
        $columns = Schema::getColumnListing('classes');
        foreach ($columns as $column) {
            $this->info("   - $column");
        }

        // 3. Check if course_crn column exists specifically
        $this->info('3. Checking if course_crn column exists specifically...');
        if (Schema::hasColumn('classes', 'course_crn')) {
            $this->info('✅ course_crn column exists');
        } else {
            $this->error('❌ course_crn column does not exist');
        }

        // 4. Try a simple query to see what happens
        $this->info('4. Testing a simple query...');
        try {
            $count = Classes::count();
            $this->info("✅ Simple count query worked: $count records");
        } catch (\Exception $e) {
            $this->error("❌ Simple count query failed: " . $e->getMessage());
        }

        // 5. Try the problematic query with test data
        $this->info('5. Testing the problematic updateOrCreate query...');
        try {
            $testData = [
                'department_code' => 'ab',
                'course_number' => '111',
                'course_crn' => '11111'
            ];

            $this->info('   Using test data: ' . json_encode($testData));

            $result = Classes::query()->updateOrCreate(
                $testData,
                ['course_name' => 'AB-111']
            );

            $this->info('✅ updateOrCreate query worked');
            $this->info('   Result ID: ' . $result->id);

        } catch (\Exception $e) {
            $this->error('❌ updateOrCreate query failed: ' . $e->getMessage());
            $this->error('   Error details: ' . $e->getFile() . ':' . $e->getLine());
        }

        // 6. Show current database connection
        $this->info('6. Current database connection info:');
        $connection = DB::connection();
        $this->info('   Connection name: ' . $connection->getName());
        $this->info('   Database name: ' . $connection->getDatabaseName());

        // 7. Raw SQL test
        $this->info('7. Testing raw SQL query...');
        try {
            $results = DB::select('SELECT * FROM classes LIMIT 1');
            $this->info('✅ Raw SQL query worked');
            if (!empty($results)) {
                $this->info('   Sample record: ' . json_encode($results[0]));
            } else {
                $this->info('   No records in table');
            }
        } catch (\Exception $e) {
            $this->error('❌ Raw SQL query failed: ' . $e->getMessage());
        }
    }
}
