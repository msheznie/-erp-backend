<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderEditLogMaster;

class TenderEditLogMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_edit_log_masters', $tenderEditLogMaster
        );

        $this->assertApiResponse($tenderEditLogMaster);
    }

    /**
     * @test
     */
    public function test_read_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_edit_log_masters/'.$tenderEditLogMaster->id
        );

        $this->assertApiResponse($tenderEditLogMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->create();
        $editedTenderEditLogMaster = factory(TenderEditLogMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_edit_log_masters/'.$tenderEditLogMaster->id,
            $editedTenderEditLogMaster
        );

        $this->assertApiResponse($editedTenderEditLogMaster);
    }

    /**
     * @test
     */
    public function test_delete_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_edit_log_masters/'.$tenderEditLogMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_edit_log_masters/'.$tenderEditLogMaster->id
        );

        $this->response->assertStatus(404);
    }
}
