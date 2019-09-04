<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveMasterTrait;
use Tests\ApiTestTrait;

class LeaveMasterApiTest extends TestCase
{
    use MakeLeaveMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_master()
    {
        $leaveMaster = $this->fakeLeaveMasterData();
        $this->response = $this->json('POST', '/api/leaveMasters', $leaveMaster);

        $this->assertApiResponse($leaveMaster);
    }

    /**
     * @test
     */
    public function test_read_leave_master()
    {
        $leaveMaster = $this->makeLeaveMaster();
        $this->response = $this->json('GET', '/api/leaveMasters/'.$leaveMaster->id);

        $this->assertApiResponse($leaveMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_master()
    {
        $leaveMaster = $this->makeLeaveMaster();
        $editedLeaveMaster = $this->fakeLeaveMasterData();

        $this->response = $this->json('PUT', '/api/leaveMasters/'.$leaveMaster->id, $editedLeaveMaster);

        $this->assertApiResponse($editedLeaveMaster);
    }

    /**
     * @test
     */
    public function test_delete_leave_master()
    {
        $leaveMaster = $this->makeLeaveMaster();
        $this->response = $this->json('DELETE', '/api/leaveMasters/'.$leaveMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/leaveMasters/'.$leaveMaster->id);

        $this->response->assertStatus(404);
    }
}
