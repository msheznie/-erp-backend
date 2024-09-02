<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PoBulkUploadErrorLog;

class PoBulkUploadErrorLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/po_bulk_upload_error_logs', $poBulkUploadErrorLog
        );

        $this->assertApiResponse($poBulkUploadErrorLog);
    }

    /**
     * @test
     */
    public function test_read_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/po_bulk_upload_error_logs/'.$poBulkUploadErrorLog->id
        );

        $this->assertApiResponse($poBulkUploadErrorLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->create();
        $editedPoBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/po_bulk_upload_error_logs/'.$poBulkUploadErrorLog->id,
            $editedPoBulkUploadErrorLog
        );

        $this->assertApiResponse($editedPoBulkUploadErrorLog);
    }

    /**
     * @test
     */
    public function test_delete_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/po_bulk_upload_error_logs/'.$poBulkUploadErrorLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/po_bulk_upload_error_logs/'.$poBulkUploadErrorLog->id
        );

        $this->response->assertStatus(404);
    }
}
