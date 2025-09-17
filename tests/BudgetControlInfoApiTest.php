<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetControlInfo;

class BudgetControlInfoApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_control_infos', $budgetControlInfo
        );

        $this->assertApiResponse($budgetControlInfo);
    }

    /**
     * @test
     */
    public function test_read_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_control_infos/'.$budgetControlInfo->id
        );

        $this->assertApiResponse($budgetControlInfo->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->create();
        $editedBudgetControlInfo = factory(BudgetControlInfo::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_control_infos/'.$budgetControlInfo->id,
            $editedBudgetControlInfo
        );

        $this->assertApiResponse($editedBudgetControlInfo);
    }

    /**
     * @test
     */
    public function test_delete_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_control_infos/'.$budgetControlInfo->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_control_infos/'.$budgetControlInfo->id
        );

        $this->response->assertStatus(404);
    }
}
