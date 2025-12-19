<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderSupplierAssigneeEditLog;

class TenderSupplierAssigneeEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_supplier_assignee_edit_logs', $tenderSupplierAssigneeEditLog
        );

        $this->assertApiResponse($tenderSupplierAssigneeEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_supplier_assignee_edit_logs/'.$tenderSupplierAssigneeEditLog->id
        );

        $this->assertApiResponse($tenderSupplierAssigneeEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->create();
        $editedTenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_supplier_assignee_edit_logs/'.$tenderSupplierAssigneeEditLog->id,
            $editedTenderSupplierAssigneeEditLog
        );

        $this->assertApiResponse($editedTenderSupplierAssigneeEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_supplier_assignee_edit_logs/'.$tenderSupplierAssigneeEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_supplier_assignee_edit_logs/'.$tenderSupplierAssigneeEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
