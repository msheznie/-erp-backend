<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetReviewTransferAddition;

class BudgetReviewTransferAdditionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_review_transfer_additions', $budgetReviewTransferAddition
        );

        $this->assertApiResponse($budgetReviewTransferAddition);
    }

    /**
     * @test
     */
    public function test_read_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_review_transfer_additions/'.$budgetReviewTransferAddition->id
        );

        $this->assertApiResponse($budgetReviewTransferAddition->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->create();
        $editedBudgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_review_transfer_additions/'.$budgetReviewTransferAddition->id,
            $editedBudgetReviewTransferAddition
        );

        $this->assertApiResponse($editedBudgetReviewTransferAddition);
    }

    /**
     * @test
     */
    public function test_delete_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_review_transfer_additions/'.$budgetReviewTransferAddition->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_review_transfer_additions/'.$budgetReviewTransferAddition->id
        );

        $this->response->assertStatus(404);
    }
}
