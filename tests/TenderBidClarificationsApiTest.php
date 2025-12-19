<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderBidClarifications;

class TenderBidClarificationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_bid_clarifications', $tenderBidClarifications
        );

        $this->assertApiResponse($tenderBidClarifications);
    }

    /**
     * @test
     */
    public function test_read_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_bid_clarifications/'.$tenderBidClarifications->id
        );

        $this->assertApiResponse($tenderBidClarifications->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->create();
        $editedTenderBidClarifications = factory(TenderBidClarifications::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_bid_clarifications/'.$tenderBidClarifications->id,
            $editedTenderBidClarifications
        );

        $this->assertApiResponse($editedTenderBidClarifications);
    }

    /**
     * @test
     */
    public function test_delete_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_bid_clarifications/'.$tenderBidClarifications->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_bid_clarifications/'.$tenderBidClarifications->id
        );

        $this->response->assertStatus(404);
    }
}
