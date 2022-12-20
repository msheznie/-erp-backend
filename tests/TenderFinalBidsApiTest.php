<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderFinalBids;

class TenderFinalBidsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_final_bids', $tenderFinalBids
        );

        $this->assertApiResponse($tenderFinalBids);
    }

    /**
     * @test
     */
    public function test_read_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_final_bids/'.$tenderFinalBids->id
        );

        $this->assertApiResponse($tenderFinalBids->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->create();
        $editedTenderFinalBids = factory(TenderFinalBids::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_final_bids/'.$tenderFinalBids->id,
            $editedTenderFinalBids
        );

        $this->assertApiResponse($editedTenderFinalBids);
    }

    /**
     * @test
     */
    public function test_delete_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_final_bids/'.$tenderFinalBids->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_final_bids/'.$tenderFinalBids->id
        );

        $this->response->assertStatus(404);
    }
}
