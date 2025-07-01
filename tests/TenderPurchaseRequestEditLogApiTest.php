<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderPurchaseRequestEditLog;

class TenderPurchaseRequestEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_purchase_request_edit_logs', $tenderPurchaseRequestEditLog
        );

        $this->assertApiResponse($tenderPurchaseRequestEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_purchase_request_edit_logs/'.$tenderPurchaseRequestEditLog->id
        );

        $this->assertApiResponse($tenderPurchaseRequestEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->create();
        $editedTenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_purchase_request_edit_logs/'.$tenderPurchaseRequestEditLog->id,
            $editedTenderPurchaseRequestEditLog
        );

        $this->assertApiResponse($editedTenderPurchaseRequestEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_purchase_request_edit_logs/'.$tenderPurchaseRequestEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_purchase_request_edit_logs/'.$tenderPurchaseRequestEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
