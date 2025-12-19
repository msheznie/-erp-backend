<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\WorkOrderGenerationLog;

class WorkOrderGenerationLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/work_order_generation_logs', $workOrderGenerationLog
        );

        $this->assertApiResponse($workOrderGenerationLog);
    }

    /**
     * @test
     */
    public function test_read_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/work_order_generation_logs/'.$workOrderGenerationLog->id
        );

        $this->assertApiResponse($workOrderGenerationLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->create();
        $editedWorkOrderGenerationLog = factory(WorkOrderGenerationLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/work_order_generation_logs/'.$workOrderGenerationLog->id,
            $editedWorkOrderGenerationLog
        );

        $this->assertApiResponse($editedWorkOrderGenerationLog);
    }

    /**
     * @test
     */
    public function test_delete_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/work_order_generation_logs/'.$workOrderGenerationLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/work_order_generation_logs/'.$workOrderGenerationLog->id
        );

        $this->response->assertStatus(404);
    }
}
