<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetControlLink;

class BudgetControlLinkApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_control_links', $budgetControlLink
        );

        $this->assertApiResponse($budgetControlLink);
    }

    /**
     * @test
     */
    public function test_read_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_control_links/'.$budgetControlLink->id
        );

        $this->assertApiResponse($budgetControlLink->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->create();
        $editedBudgetControlLink = factory(BudgetControlLink::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_control_links/'.$budgetControlLink->id,
            $editedBudgetControlLink
        );

        $this->assertApiResponse($editedBudgetControlLink);
    }

    /**
     * @test
     */
    public function test_delete_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_control_links/'.$budgetControlLink->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_control_links/'.$budgetControlLink->id
        );

        $this->response->assertStatus(404);
    }
}
