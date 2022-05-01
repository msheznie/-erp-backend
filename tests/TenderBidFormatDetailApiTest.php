<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderBidFormatDetail;

class TenderBidFormatDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_bid_format_details', $tenderBidFormatDetail
        );

        $this->assertApiResponse($tenderBidFormatDetail);
    }

    /**
     * @test
     */
    public function test_read_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_bid_format_details/'.$tenderBidFormatDetail->id
        );

        $this->assertApiResponse($tenderBidFormatDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->create();
        $editedTenderBidFormatDetail = factory(TenderBidFormatDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_bid_format_details/'.$tenderBidFormatDetail->id,
            $editedTenderBidFormatDetail
        );

        $this->assertApiResponse($editedTenderBidFormatDetail);
    }

    /**
     * @test
     */
    public function test_delete_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_bid_format_details/'.$tenderBidFormatDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_bid_format_details/'.$tenderBidFormatDetail->id
        );

        $this->response->assertStatus(404);
    }
}
