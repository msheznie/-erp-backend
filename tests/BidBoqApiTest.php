<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidBoq;

class BidBoqApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_boqs', $bidBoq
        );

        $this->assertApiResponse($bidBoq);
    }

    /**
     * @test
     */
    public function test_read_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_boqs/'.$bidBoq->id
        );

        $this->assertApiResponse($bidBoq->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->create();
        $editedBidBoq = factory(BidBoq::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_boqs/'.$bidBoq->id,
            $editedBidBoq
        );

        $this->assertApiResponse($editedBidBoq);
    }

    /**
     * @test
     */
    public function test_delete_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_boqs/'.$bidBoq->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_boqs/'.$bidBoq->id
        );

        $this->response->assertStatus(404);
    }
}
