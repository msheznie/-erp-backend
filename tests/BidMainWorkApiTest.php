<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidMainWork;

class BidMainWorkApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_main_works', $bidMainWork
        );

        $this->assertApiResponse($bidMainWork);
    }

    /**
     * @test
     */
    public function test_read_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_main_works/'.$bidMainWork->id
        );

        $this->assertApiResponse($bidMainWork->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->create();
        $editedBidMainWork = factory(BidMainWork::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_main_works/'.$bidMainWork->id,
            $editedBidMainWork
        );

        $this->assertApiResponse($editedBidMainWork);
    }

    /**
     * @test
     */
    public function test_delete_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_main_works/'.$bidMainWork->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_main_works/'.$bidMainWork->id
        );

        $this->response->assertStatus(404);
    }
}
