<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpBudgetAdditionDetail;

class ErpBudgetAdditionDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_budget_addition_details', $erpBudgetAdditionDetail
        );

        $this->assertApiResponse($erpBudgetAdditionDetail);
    }

    /**
     * @test
     */
    public function test_read_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_budget_addition_details/'.$erpBudgetAdditionDetail->id
        );

        $this->assertApiResponse($erpBudgetAdditionDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->create();
        $editedErpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_budget_addition_details/'.$erpBudgetAdditionDetail->id,
            $editedErpBudgetAdditionDetail
        );

        $this->assertApiResponse($editedErpBudgetAdditionDetail);
    }

    /**
     * @test
     */
    public function test_delete_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_budget_addition_details/'.$erpBudgetAdditionDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_budget_addition_details/'.$erpBudgetAdditionDetail->id
        );

        $this->response->assertStatus(404);
    }
}
