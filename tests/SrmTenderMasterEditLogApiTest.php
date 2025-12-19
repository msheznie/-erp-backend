<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrmTenderMasterEditLog;

class SrmTenderMasterEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srm_tender_master_edit_logs', $srmTenderMasterEditLog
        );

        $this->assertApiResponse($srmTenderMasterEditLog);
    }

    /**
     * @test
     */
    public function test_read_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srm_tender_master_edit_logs/'.$srmTenderMasterEditLog->id
        );

        $this->assertApiResponse($srmTenderMasterEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->create();
        $editedSrmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srm_tender_master_edit_logs/'.$srmTenderMasterEditLog->id,
            $editedSrmTenderMasterEditLog
        );

        $this->assertApiResponse($editedSrmTenderMasterEditLog);
    }

    /**
     * @test
     */
    public function test_delete_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srm_tender_master_edit_logs/'.$srmTenderMasterEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srm_tender_master_edit_logs/'.$srmTenderMasterEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
