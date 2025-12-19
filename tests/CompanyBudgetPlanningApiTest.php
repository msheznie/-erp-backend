<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CompanyBudgetPlanning;

class CompanyBudgetPlanningApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/company_budget_plannings', $companyBudgetPlanning
        );

        $this->assertApiResponse($companyBudgetPlanning);
    }

    /**
     * @test
     */
    public function test_read_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/company_budget_plannings/'.$companyBudgetPlanning->id
        );

        $this->assertApiResponse($companyBudgetPlanning->toArray());
    }

    /**
     * @test
     */
    public function test_update_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->create();
        $editedCompanyBudgetPlanning = factory(CompanyBudgetPlanning::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/company_budget_plannings/'.$companyBudgetPlanning->id,
            $editedCompanyBudgetPlanning
        );

        $this->assertApiResponse($editedCompanyBudgetPlanning);
    }

    /**
     * @test
     */
    public function test_delete_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/company_budget_plannings/'.$companyBudgetPlanning->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/company_budget_plannings/'.$companyBudgetPlanning->id
        );

        $this->response->assertStatus(404);
    }
}
