<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MrBulkUploadErrorLog;

class MrBulkUploadErrorLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mr_bulk_upload_error_logs', $mrBulkUploadErrorLog
        );

        $this->assertApiResponse($mrBulkUploadErrorLog);
    }

    /**
     * @test
     */
    public function test_read_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mr_bulk_upload_error_logs/'.$mrBulkUploadErrorLog->id
        );

        $this->assertApiResponse($mrBulkUploadErrorLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->create();
        $editedMrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mr_bulk_upload_error_logs/'.$mrBulkUploadErrorLog->id,
            $editedMrBulkUploadErrorLog
        );

        $this->assertApiResponse($editedMrBulkUploadErrorLog);
    }

    /**
     * @test
     */
    public function test_delete_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mr_bulk_upload_error_logs/'.$mrBulkUploadErrorLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mr_bulk_upload_error_logs/'.$mrBulkUploadErrorLog->id
        );

        $this->response->assertStatus(404);
    }
}
