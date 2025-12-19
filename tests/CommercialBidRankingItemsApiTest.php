<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CommercialBidRankingItems;

class CommercialBidRankingItemsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/commercial_bid_ranking_items', $commercialBidRankingItems
        );

        $this->assertApiResponse($commercialBidRankingItems);
    }

    /**
     * @test
     */
    public function test_read_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/commercial_bid_ranking_items/'.$commercialBidRankingItems->id
        );

        $this->assertApiResponse($commercialBidRankingItems->toArray());
    }

    /**
     * @test
     */
    public function test_update_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->create();
        $editedCommercialBidRankingItems = factory(CommercialBidRankingItems::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/commercial_bid_ranking_items/'.$commercialBidRankingItems->id,
            $editedCommercialBidRankingItems
        );

        $this->assertApiResponse($editedCommercialBidRankingItems);
    }

    /**
     * @test
     */
    public function test_delete_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/commercial_bid_ranking_items/'.$commercialBidRankingItems->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/commercial_bid_ranking_items/'.$commercialBidRankingItems->id
        );

        $this->response->assertStatus(404);
    }
}
