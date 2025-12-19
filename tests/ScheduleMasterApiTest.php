<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeScheduleMasterTrait;
use Tests\ApiTestTrait;

class ScheduleMasterApiTest extends TestCase
{
    use MakeScheduleMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_schedule_master()
    {
        $scheduleMaster = $this->fakeScheduleMasterData();
        $this->response = $this->json('POST', '/api/scheduleMasters', $scheduleMaster);

        $this->assertApiResponse($scheduleMaster);
    }

    /**
     * @test
     */
    public function test_read_schedule_master()
    {
        $scheduleMaster = $this->makeScheduleMaster();
        $this->response = $this->json('GET', '/api/scheduleMasters/'.$scheduleMaster->id);

        $this->assertApiResponse($scheduleMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_schedule_master()
    {
        $scheduleMaster = $this->makeScheduleMaster();
        $editedScheduleMaster = $this->fakeScheduleMasterData();

        $this->response = $this->json('PUT', '/api/scheduleMasters/'.$scheduleMaster->id, $editedScheduleMaster);

        $this->assertApiResponse($editedScheduleMaster);
    }

    /**
     * @test
     */
    public function test_delete_schedule_master()
    {
        $scheduleMaster = $this->makeScheduleMaster();
        $this->response = $this->json('DELETE', '/api/scheduleMasters/'.$scheduleMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/scheduleMasters/'.$scheduleMaster->id);

        $this->response->assertStatus(404);
    }
}
