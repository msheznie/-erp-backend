<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpBudgetAddition;

class ErpBudgetAdditionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_budget_additions', $erpBudgetAddition
        );

        $this->assertApiResponse($erpBudgetAddition);
    }

    /**
     * @test
     */
    public function test_read_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_budget_additions/'.$erpBudgetAddition->id
        );

        $this->assertApiResponse($erpBudgetAddition->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->create();
        $editedErpBudgetAddition = factory(ErpBudgetAddition::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_budget_additions/'.$erpBudgetAddition->id,
            $editedErpBudgetAddition
        );

        $this->assertApiResponse($editedErpBudgetAddition);
    }

    /**
     * @test
     */
    public function test_delete_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_budget_additions/'.$erpBudgetAddition->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_budget_additions/'.$erpBudgetAddition->id
        );

        $this->response->assertStatus(404);
    }
}
