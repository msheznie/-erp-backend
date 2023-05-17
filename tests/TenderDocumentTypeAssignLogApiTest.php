<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderDocumentTypeAssignLog;

class TenderDocumentTypeAssignLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_document_type_assign_logs', $tenderDocumentTypeAssignLog
        );

        $this->assertApiResponse($tenderDocumentTypeAssignLog);
    }

    /**
     * @test
     */
    public function test_read_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_document_type_assign_logs/'.$tenderDocumentTypeAssignLog->id
        );

        $this->assertApiResponse($tenderDocumentTypeAssignLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->create();
        $editedTenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_document_type_assign_logs/'.$tenderDocumentTypeAssignLog->id,
            $editedTenderDocumentTypeAssignLog
        );

        $this->assertApiResponse($editedTenderDocumentTypeAssignLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_document_type_assign_logs/'.$tenderDocumentTypeAssignLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_document_type_assign_logs/'.$tenderDocumentTypeAssignLog->id
        );

        $this->response->assertStatus(404);
    }
}
