<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeWarehouseRightsTrait;
use Tests\ApiTestTrait;

class WarehouseRightsApiTest extends TestCase
{
    use MakeWarehouseRightsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_warehouse_rights()
    {
        $warehouseRights = $this->fakeWarehouseRightsData();
        $this->response = $this->json('POST', '/api/warehouseRights', $warehouseRights);

        $this->assertApiResponse($warehouseRights);
    }

    /**
     * @test
     */
    public function test_read_warehouse_rights()
    {
        $warehouseRights = $this->makeWarehouseRights();
        $this->response = $this->json('GET', '/api/warehouseRights/'.$warehouseRights->id);

        $this->assertApiResponse($warehouseRights->toArray());
    }

    /**
     * @test
     */
    public function test_update_warehouse_rights()
    {
        $warehouseRights = $this->makeWarehouseRights();
        $editedWarehouseRights = $this->fakeWarehouseRightsData();

        $this->response = $this->json('PUT', '/api/warehouseRights/'.$warehouseRights->id, $editedWarehouseRights);

        $this->assertApiResponse($editedWarehouseRights);
    }

    /**
     * @test
     */
    public function test_delete_warehouse_rights()
    {
        $warehouseRights = $this->makeWarehouseRights();
        $this->response = $this->json('DELETE', '/api/warehouseRights/'.$warehouseRights->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/warehouseRights/'.$warehouseRights->id);

        $this->response->assertStatus(404);
    }
}
