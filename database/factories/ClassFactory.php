<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\DepartmentService;

class ClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $departmentService = app(DepartmentService::class); // Resolve DepartmentService from the service container
        $randomDepartment = $departmentService->getRandomDepartment(); // Get a random department

        $courseNumber = $this->faker->randomNumber(3); // Generate a random course number

        return [
            'department_code' => $randomDepartment['pcc_code'], // Use the pcc_code from the randomly selected department
            'course_number' => $courseNumber,
            'course_name' => $randomDepartment['pcc_code'] . '-' . $courseNumber . ' ' . $this->faker->words(2, true),
        ];
    }
}
