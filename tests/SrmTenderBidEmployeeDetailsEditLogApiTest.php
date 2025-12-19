<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;

class SrmTenderBidEmployeeDetailsEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srm_tender_bid_employee_details_edit_logs', $srmTenderBidEmployeeDetailsEditLog
        );

        $this->assertApiResponse($srmTenderBidEmployeeDetailsEditLog);
    }

    /**
     * @test
     */
    public function test_read_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srm_tender_bid_employee_details_edit_logs/'.$srmTenderBidEmployeeDetailsEditLog->id
        );

        $this->assertApiResponse($srmTenderBidEmployeeDetailsEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->create();
        $editedSrmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srm_tender_bid_employee_details_edit_logs/'.$srmTenderBidEmployeeDetailsEditLog->id,
            $editedSrmTenderBidEmployeeDetailsEditLog
        );

        $this->assertApiResponse($editedSrmTenderBidEmployeeDetailsEditLog);
    }

    /**
     * @test
     */
    public function test_delete_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srm_tender_bid_employee_details_edit_logs/'.$srmTenderBidEmployeeDetailsEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srm_tender_bid_employee_details_edit_logs/'.$srmTenderBidEmployeeDetailsEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
