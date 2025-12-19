<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderBoqItemsEditLog;

class TenderBoqItemsEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_boq_items_edit_logs', $tenderBoqItemsEditLog
        );

        $this->assertApiResponse($tenderBoqItemsEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_boq_items_edit_logs/'.$tenderBoqItemsEditLog->id
        );

        $this->assertApiResponse($tenderBoqItemsEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->create();
        $editedTenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_boq_items_edit_logs/'.$tenderBoqItemsEditLog->id,
            $editedTenderBoqItemsEditLog
        );

        $this->assertApiResponse($editedTenderBoqItemsEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_boq_items_edit_logs/'.$tenderBoqItemsEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_boq_items_edit_logs/'.$tenderBoqItemsEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
