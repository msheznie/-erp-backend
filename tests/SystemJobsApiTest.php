<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SystemJobs;

class SystemJobsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/system_jobs', $systemJobs
        );

        $this->assertApiResponse($systemJobs);
    }

    /**
     * @test
     */
    public function test_read_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/system_jobs/'.$systemJobs->id
        );

        $this->assertApiResponse($systemJobs->toArray());
    }

    /**
     * @test
     */
    public function test_update_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->create();
        $editedSystemJobs = factory(SystemJobs::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/system_jobs/'.$systemJobs->id,
            $editedSystemJobs
        );

        $this->assertApiResponse($editedSystemJobs);
    }

    /**
     * @test
     */
    public function test_delete_system_jobs()
    {
        $systemJobs = factory(SystemJobs::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/system_jobs/'.$systemJobs->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/system_jobs/'.$systemJobs->id
        );

        $this->response->assertStatus(404);
    }
}
