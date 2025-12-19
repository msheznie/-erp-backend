<?php

namespace Tests;

use App\Rules\UniqueRequestCodePerBudgetPlanning;
use App\Models\DepartmentBudgetPlanning;
use App\Models\CompanyBudgetPlanning;
use App\Models\DeptBudgetPlanningTimeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueRequestCodePerBudgetPlanningTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_passes_when_request_code_is_unique()
    {
        // Create test data
        $companyBudgetPlanning = CompanyBudgetPlanning::factory()->create([
            'companySystemID' => 1
        ]);
        
        $departmentBudgetPlanning = DepartmentBudgetPlanning::factory()->create([
            'companyBudgetPlanningID' => $companyBudgetPlanning->id,
            'departmentID' => 1
        ]);

        // Create validation rule
        $rule = new UniqueRequestCodePerBudgetPlanning($departmentBudgetPlanning->id);

        // Test with unique request code
        $this->assertTrue($rule->passes('requestCode', 'RQ000001'));
    }

    public function test_validation_fails_when_request_code_already_exists()
    {
        // Create test data
        $companyBudgetPlanning = CompanyBudgetPlanning::factory()->create([
            'companySystemID' => 1
        ]);
        
        $departmentBudgetPlanning = DepartmentBudgetPlanning::factory()->create([
            'companyBudgetPlanningID' => $companyBudgetPlanning->id,
            'departmentID' => 1
        ]);

        // Create existing time request with the same request code for the same budget planning
        DeptBudgetPlanningTimeRequest::create([
            'department_budget_planning_id' => $departmentBudgetPlanning->id,
            'request_code' => 'RQ000001',
            'current_submission_date' => now(),
            'date_of_request' => now()->addDay(),
            'reason_for_extension' => 'Test reason',
            'status' => 1
        ]);

        // Create validation rule
        $rule = new UniqueRequestCodePerBudgetPlanning($departmentBudgetPlanning->id);

        // Test with existing request code for the same budget planning
        $this->assertFalse($rule->passes('requestCode', 'RQ000001'));
    }

    public function test_validation_passes_for_different_budget_planning()
    {
        // Create test data for first budget planning
        $companyBudgetPlanning = CompanyBudgetPlanning::factory()->create([
            'companySystemID' => 1
        ]);
        
        $departmentBudgetPlanning1 = DepartmentBudgetPlanning::factory()->create([
            'companyBudgetPlanningID' => $companyBudgetPlanning->id,
            'departmentID' => 1
        ]);

        $departmentBudgetPlanning2 = DepartmentBudgetPlanning::factory()->create([
            'companyBudgetPlanningID' => $companyBudgetPlanning->id,
            'departmentID' => 1
        ]);

        // Create existing time request for first budget planning
        DeptBudgetPlanningTimeRequest::create([
            'department_budget_planning_id' => $departmentBudgetPlanning1->id,
            'request_code' => 'RQ000001',
            'current_submission_date' => now(),
            'date_of_request' => now()->addDay(),
            'reason_for_extension' => 'Test reason',
            'status' => 1
        ]);

        // Create validation rule for second budget planning
        $rule = new UniqueRequestCodePerBudgetPlanning($departmentBudgetPlanning2->id);

        // Test with same request code but different budget planning
        $this->assertTrue($rule->passes('requestCode', 'RQ000001'));
    }

    public function test_validation_passes_for_different_budget_planning_same_department()
    {
        // Create test data for first budget planning
        $companyBudgetPlanning = CompanyBudgetPlanning::factory()->create([
            'companySystemID' => 1
        ]);
        
        $departmentBudgetPlanning1 = DepartmentBudgetPlanning::factory()->create([
            'companyBudgetPlanningID' => $companyBudgetPlanning->id,
            'departmentID' => 1
        ]);

        $departmentBudgetPlanning2 = DepartmentBudgetPlanning::factory()->create([
            'companyBudgetPlanningID' => $companyBudgetPlanning->id,
            'departmentID' => 1
        ]);

        // Create existing time request for first budget planning
        DeptBudgetPlanningTimeRequest::create([
            'department_budget_planning_id' => $departmentBudgetPlanning1->id,
            'request_code' => 'RQ000001',
            'current_submission_date' => now(),
            'date_of_request' => now()->addDay(),
            'reason_for_extension' => 'Test reason',
            'status' => 1
        ]);

        // Create validation rule for second budget planning
        $rule = new UniqueRequestCodePerBudgetPlanning($departmentBudgetPlanning2->id);

        // Test with same request code but different budget planning (even same department)
        $this->assertTrue($rule->passes('requestCode', 'RQ000001'));
    }

    public function test_validation_returns_correct_error_message()
    {
        $rule = new UniqueRequestCodePerBudgetPlanning(1);
        $this->assertEquals('The request code has already been taken for this budget planning.', $rule->message());
    }
}

