<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MiBulkUploadErrorLog;

class MiBulkUploadErrorLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mi_bulk_upload_error_logs', $miBulkUploadErrorLog
        );

        $this->assertApiResponse($miBulkUploadErrorLog);
    }

    /**
     * @test
     */
    public function test_read_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mi_bulk_upload_error_logs/'.$miBulkUploadErrorLog->id
        );

        $this->assertApiResponse($miBulkUploadErrorLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->create();
        $editedMiBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mi_bulk_upload_error_logs/'.$miBulkUploadErrorLog->id,
            $editedMiBulkUploadErrorLog
        );

        $this->assertApiResponse($editedMiBulkUploadErrorLog);
    }

    /**
     * @test
     */
    public function test_delete_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mi_bulk_upload_error_logs/'.$miBulkUploadErrorLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mi_bulk_upload_error_logs/'.$miBulkUploadErrorLog->id
        );

        $this->response->assertStatus(404);
    }
}
