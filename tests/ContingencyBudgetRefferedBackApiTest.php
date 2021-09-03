<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ContingencyBudgetRefferedBack;

class ContingencyBudgetRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/contingency_budget_reffered_backs', $contingencyBudgetRefferedBack
        );

        $this->assertApiResponse($contingencyBudgetRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/contingency_budget_reffered_backs/'.$contingencyBudgetRefferedBack->id
        );

        $this->assertApiResponse($contingencyBudgetRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->create();
        $editedContingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/contingency_budget_reffered_backs/'.$contingencyBudgetRefferedBack->id,
            $editedContingencyBudgetRefferedBack
        );

        $this->assertApiResponse($editedContingencyBudgetRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/contingency_budget_reffered_backs/'.$contingencyBudgetRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/contingency_budget_reffered_backs/'.$contingencyBudgetRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}
