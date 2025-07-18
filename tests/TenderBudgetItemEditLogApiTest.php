<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderBudgetItemEditLog;

class TenderBudgetItemEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_budget_item_edit_logs', $tenderBudgetItemEditLog
        );

        $this->assertApiResponse($tenderBudgetItemEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_budget_item_edit_logs/'.$tenderBudgetItemEditLog->id
        );

        $this->assertApiResponse($tenderBudgetItemEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->create();
        $editedTenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_budget_item_edit_logs/'.$tenderBudgetItemEditLog->id,
            $editedTenderBudgetItemEditLog
        );

        $this->assertApiResponse($editedTenderBudgetItemEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_budget_item_edit_logs/'.$tenderBudgetItemEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_budget_item_edit_logs/'.$tenderBudgetItemEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
