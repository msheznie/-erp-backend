<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\WarehouseSubLevels;

class WarehouseSubLevelsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/warehouse_sub_levels', $warehouseSubLevels
        );

        $this->assertApiResponse($warehouseSubLevels);
    }

    /**
     * @test
     */
    public function test_read_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/warehouse_sub_levels/'.$warehouseSubLevels->id
        );

        $this->assertApiResponse($warehouseSubLevels->toArray());
    }

    /**
     * @test
     */
    public function test_update_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->create();
        $editedWarehouseSubLevels = factory(WarehouseSubLevels::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/warehouse_sub_levels/'.$warehouseSubLevels->id,
            $editedWarehouseSubLevels
        );

        $this->assertApiResponse($editedWarehouseSubLevels);
    }

    /**
     * @test
     */
    public function test_delete_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/warehouse_sub_levels/'.$warehouseSubLevels->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/warehouse_sub_levels/'.$warehouseSubLevels->id
        );

        $this->response->assertStatus(404);
    }
}
