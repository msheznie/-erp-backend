<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProcumentActivityEditLog;

class ProcumentActivityEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/procument_activity_edit_logs', $procumentActivityEditLog
        );

        $this->assertApiResponse($procumentActivityEditLog);
    }

    /**
     * @test
     */
    public function test_read_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/procument_activity_edit_logs/'.$procumentActivityEditLog->id
        );

        $this->assertApiResponse($procumentActivityEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->create();
        $editedProcumentActivityEditLog = factory(ProcumentActivityEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/procument_activity_edit_logs/'.$procumentActivityEditLog->id,
            $editedProcumentActivityEditLog
        );

        $this->assertApiResponse($editedProcumentActivityEditLog);
    }

    /**
     * @test
     */
    public function test_delete_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/procument_activity_edit_logs/'.$procumentActivityEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/procument_activity_edit_logs/'.$procumentActivityEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
