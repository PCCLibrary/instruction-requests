<?php

namespace Tests\Unit;

use App\Models\Campus;
use App\Models\Classes;
use App\Models\Instructor;
use App\Models\InstructionRequests;
use App\Models\InstructionRequestsDetail;
use App\Repositories\InstructionRequestRepository;
use App\Services\NotificationService;
use App\ValueObjects\NotificationPackage;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(InstructionRequestRepository::class);
        $this->service = new NotificationService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_generates_correct_instructor_subjects_for_in_person_requests()
    {
        $request = $this->createMockRequest([
            'instruction_type' => 'on-campus',
            'preferred_datetime' => '2025-05-22 12:55:00',
            'department' => 'cs',
            'course_number' => '101'
        ]);

        $this->mockRepository->shouldReceive('find')->with(1)->andReturn($request);

        $package = $this->service->prepareReceivedNotificationPackage(1);

        $expected = 'Confirmation: In-person library instruction request for cs101 on 2025-05-22 12:55pm';
        $this->assertEquals($expected, $package->instructorSubject);
    }

    /** @test */
    public function it_generates_correct_instructor_subjects_for_remote_requests()
    {
        $request = $this->createMockRequest([
            'instruction_type' => 'remote',
            'preferred_datetime' => '2025-05-22 14:30:00',
            'department' => 'ad',
            'course_number' => '111'
        ]);

        $this->mockRepository->shouldReceive('find')->with(1)->andReturn($request);

        $package = $this->service->prepareReceivedNotificationPackage(1);

        $expected = 'Confirmation: Remote library instruction request for ad111 on 2025-05-22 2:30pm';
        $this->assertEquals($expected, $package->instructorSubject);
    }

    /** @test */
    public function it_generates_correct_instructor_subjects_for_asynchronous_requests()
    {
        $request = $this->createMockRequest([
            'instruction_type' => 'asynchronous',
            'asynchronous_instruction_ready_date' => '2025-05-22',
            'department' => 'bio',
            'course_number' => '101'
        ]);

        $this->mockRepository->shouldReceive('find')->with(1)->andReturn($request);

        $package = $this->service->prepareReceivedNotificationPackage(1);

        $expected = 'Confirmation: Asynchronous library instruction request for bio101 by 2025-05-22';
        $this->assertEquals($expected, $package->instructorSubject);
    }

    /** @test */
    public function it_generates_correct_librarian_subjects_for_in_person_requests()
    {
        $request = $this->createMockRequest([
            'instruction_type' => 'on-campus',
            'preferred_datetime' => '2025-05-22 12:55:00',
            'department' => 'cs',
            'course_number' => '101'
        ]);

        $this->mockRepository->shouldReceive('find')->with(1)->andReturn($request);

        $package = $this->service->prepareReceivedNotificationPackage(1);

        $expected = 'Library Instruction Request: In-person, 2025-05-22 12:55pm, Test Campus, cs101, John Doe';
        $this->assertEquals($expected, $package->librarianSubject);
    }

    /** @test */
    public function it_generates_correct_librarian_subjects_for_asynchronous_requests()
    {
        $request = $this->createMockRequest([
            'instruction_type' => 'asynchronous',
            'asynchronous_instruction_ready_date' => '2025-05-22',
            'department' => 'ad',
            'course_number' => '111'
        ]);

        $this->mockRepository->shouldReceive('find')->with(1)->andReturn($request);

        $package = $this->service->prepareReceivedNotificationPackage(1);

        $expected = 'Library Instruction Request: Asynchronous, 2025-05-22, Test Campus, ad111, John Doe';
        $this->assertEquals($expected, $package->librarianSubject);
    }

    /** @test */
    public function notification_package_contains_required_data()
    {
        $request = $this->createMockRequest();
        $this->mockRepository->shouldReceive('find')->with(1)->andReturn($request);

        $package = $this->service->prepareReceivedNotificationPackage(1);

        $this->assertInstanceOf(NotificationPackage::class, $package);
        $this->assertIsArray($package->templateData);
        $this->assertIsString($package->dashboardUrl);
        $this->assertIsString($package->instructorSubject);
        $this->assertIsString($package->librarianSubject);

        // Verify template data contains required fields
        $this->assertArrayHasKey('id', $package->templateData);
        $this->assertArrayHasKey('instruction_type', $package->templateData);
        $this->assertArrayHasKey('course_department', $package->templateData);
        $this->assertArrayHasKey('course_number', $package->templateData);
        $this->assertArrayHasKey('instructor_name', $package->templateData);
        $this->assertArrayHasKey('campus_name', $package->templateData);
    }

    private function createMockRequest(array $overrides = []): InstructionRequests
    {
        $defaults = [
            'id' => 1,
            'instruction_type' => 'on-campus',
            'department' => 'cs',
            'course_number' => '101',
            'course_crn' => '12345',
            'number_of_students' => 25,
            'preferred_datetime' => '2025-05-22 12:55:00',
            'duration' => 50,
            'status' => 'received'
        ];

        $attributes = array_merge($defaults, $overrides);

        $request = Mockery::mock(InstructionRequests::class);

        foreach ($attributes as $key => $value) {
            $request->shouldReceive('getAttribute')->with($key)->andReturn($value);
            $request->{$key} = $value;
        }

        // Mock relationships
        $instructor = Mockery::mock(Instructor::class);
        $instructor->shouldReceive('getAttribute')->with('name')->andReturn('John Doe');
        $instructor->shouldReceive('getAttribute')->with('email')->andReturn('john.doe@pcc.edu');
        $instructor->name = 'John Doe';
        $instructor->email = 'john.doe@pcc.edu';

        $campus = Mockery::mock(Campus::class);
        $campus->shouldReceive('getAttribute')->with('name')->andReturn('Test Campus');
        $campus->name = 'Test Campus';

        $classes = Mockery::mock(Classes::class);
        $classes->shouldReceive('getAttribute')->with('course_name')->andReturn('Test Course');
        $classes->course_name = 'Test Course';

        $detail = Mockery::mock(InstructionRequestsDetail::class);
        $detail->shouldReceive('toArray')->andReturn([]);
        $detail->shouldReceive('getAttribute')->with('assigned_librarian_id')->andReturn(null);

        $request->instructor = $instructor;
        $request->campus = $campus;
        $request->classes = $classes;
        $request->detail = $detail;

        $request->shouldReceive('load')->with(['detail', 'instructor', 'classes', 'campus'])->andReturnSelf();

        return $request;
    }
}
