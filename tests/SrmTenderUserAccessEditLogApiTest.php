<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrmTenderUserAccessEditLog;

class SrmTenderUserAccessEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srm_tender_user_access_edit_logs', $srmTenderUserAccessEditLog
        );

        $this->assertApiResponse($srmTenderUserAccessEditLog);
    }

    /**
     * @test
     */
    public function test_read_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srm_tender_user_access_edit_logs/'.$srmTenderUserAccessEditLog->id
        );

        $this->assertApiResponse($srmTenderUserAccessEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->create();
        $editedSrmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srm_tender_user_access_edit_logs/'.$srmTenderUserAccessEditLog->id,
            $editedSrmTenderUserAccessEditLog
        );

        $this->assertApiResponse($editedSrmTenderUserAccessEditLog);
    }

    /**
     * @test
     */
    public function test_delete_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srm_tender_user_access_edit_logs/'.$srmTenderUserAccessEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srm_tender_user_access_edit_logs/'.$srmTenderUserAccessEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
