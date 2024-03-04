<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderMasterReferred;

class TenderMasterReferredApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_master_referreds', $tenderMasterReferred
        );

        $this->assertApiResponse($tenderMasterReferred);
    }

    /**
     * @test
     */
    public function test_read_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_master_referreds/'.$tenderMasterReferred->id
        );

        $this->assertApiResponse($tenderMasterReferred->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->create();
        $editedTenderMasterReferred = factory(TenderMasterReferred::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_master_referreds/'.$tenderMasterReferred->id,
            $editedTenderMasterReferred
        );

        $this->assertApiResponse($editedTenderMasterReferred);
    }

    /**
     * @test
     */
    public function test_delete_tender_master_referred()
    {
        $tenderMasterReferred = factory(TenderMasterReferred::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_master_referreds/'.$tenderMasterReferred->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_master_referreds/'.$tenderMasterReferred->id
        );

        $this->response->assertStatus(404);
    }
}
