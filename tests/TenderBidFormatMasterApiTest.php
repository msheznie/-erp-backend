<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderBidFormatMaster;

class TenderBidFormatMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_bid_format_masters', $tenderBidFormatMaster
        );

        $this->assertApiResponse($tenderBidFormatMaster);
    }

    /**
     * @test
     */
    public function test_read_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_bid_format_masters/'.$tenderBidFormatMaster->id
        );

        $this->assertApiResponse($tenderBidFormatMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->create();
        $editedTenderBidFormatMaster = factory(TenderBidFormatMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_bid_format_masters/'.$tenderBidFormatMaster->id,
            $editedTenderBidFormatMaster
        );

        $this->assertApiResponse($editedTenderBidFormatMaster);
    }

    /**
     * @test
     */
    public function test_delete_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_bid_format_masters/'.$tenderBidFormatMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_bid_format_masters/'.$tenderBidFormatMaster->id
        );

        $this->response->assertStatus(404);
    }
}
