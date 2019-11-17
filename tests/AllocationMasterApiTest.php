<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeAllocationMasterTrait;
use Tests\ApiTestTrait;

class AllocationMasterApiTest extends TestCase
{
    use MakeAllocationMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_allocation_master()
    {
        $allocationMaster = $this->fakeAllocationMasterData();
        $this->response = $this->json('POST', '/api/allocationMasters', $allocationMaster);

        $this->assertApiResponse($allocationMaster);
    }

    /**
     * @test
     */
    public function test_read_allocation_master()
    {
        $allocationMaster = $this->makeAllocationMaster();
        $this->response = $this->json('GET', '/api/allocationMasters/'.$allocationMaster->id);

        $this->assertApiResponse($allocationMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_allocation_master()
    {
        $allocationMaster = $this->makeAllocationMaster();
        $editedAllocationMaster = $this->fakeAllocationMasterData();

        $this->response = $this->json('PUT', '/api/allocationMasters/'.$allocationMaster->id, $editedAllocationMaster);

        $this->assertApiResponse($editedAllocationMaster);
    }

    /**
     * @test
     */
    public function test_delete_allocation_master()
    {
        $allocationMaster = $this->makeAllocationMaster();
        $this->response = $this->json('DELETE', '/api/allocationMasters/'.$allocationMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/allocationMasters/'.$allocationMaster->id);

        $this->response->assertStatus(404);
    }
}
