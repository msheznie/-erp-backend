<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidEvaluationSelection;

class BidEvaluationSelectionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_evaluation_selections', $bidEvaluationSelection
        );

        $this->assertApiResponse($bidEvaluationSelection);
    }

    /**
     * @test
     */
    public function test_read_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_evaluation_selections/'.$bidEvaluationSelection->id
        );

        $this->assertApiResponse($bidEvaluationSelection->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->create();
        $editedBidEvaluationSelection = factory(BidEvaluationSelection::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_evaluation_selections/'.$bidEvaluationSelection->id,
            $editedBidEvaluationSelection
        );

        $this->assertApiResponse($editedBidEvaluationSelection);
    }

    /**
     * @test
     */
    public function test_delete_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_evaluation_selections/'.$bidEvaluationSelection->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_evaluation_selections/'.$bidEvaluationSelection->id
        );

        $this->response->assertStatus(404);
    }
}
