<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetDetailComment;

class BudgetDetailCommentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_detail_comments', $budgetDetailComment
        );

        $this->assertApiResponse($budgetDetailComment);
    }

    /**
     * @test
     */
    public function test_read_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_detail_comments/'.$budgetDetailComment->id
        );

        $this->assertApiResponse($budgetDetailComment->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->create();
        $editedBudgetDetailComment = factory(BudgetDetailComment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_detail_comments/'.$budgetDetailComment->id,
            $editedBudgetDetailComment
        );

        $this->assertApiResponse($editedBudgetDetailComment);
    }

    /**
     * @test
     */
    public function test_delete_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_detail_comments/'.$budgetDetailComment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_detail_comments/'.$budgetDetailComment->id
        );

        $this->response->assertStatus(404);
    }
}
