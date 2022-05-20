<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\JobErrorLog;

class JobErrorLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/job_error_logs', $jobErrorLog
        );

        $this->assertApiResponse($jobErrorLog);
    }

    /**
     * @test
     */
    public function test_read_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/job_error_logs/'.$jobErrorLog->id
        );

        $this->assertApiResponse($jobErrorLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->create();
        $editedJobErrorLog = factory(JobErrorLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/job_error_logs/'.$jobErrorLog->id,
            $editedJobErrorLog
        );

        $this->assertApiResponse($editedJobErrorLog);
    }

    /**
     * @test
     */
    public function test_delete_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/job_error_logs/'.$jobErrorLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/job_error_logs/'.$jobErrorLog->id
        );

        $this->response->assertStatus(404);
    }
}
