<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ItemMasterCategoryType;

class ItemMasterCategoryTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/item_master_category_types', $itemMasterCategoryType
        );

        $this->assertApiResponse($itemMasterCategoryType);
    }

    /**
     * @test
     */
    public function test_read_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/item_master_category_types/'.$itemMasterCategoryType->id
        );

        $this->assertApiResponse($itemMasterCategoryType->toArray());
    }

    /**
     * @test
     */
    public function test_update_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->create();
        $editedItemMasterCategoryType = factory(ItemMasterCategoryType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/item_master_category_types/'.$itemMasterCategoryType->id,
            $editedItemMasterCategoryType
        );

        $this->assertApiResponse($editedItemMasterCategoryType);
    }

    /**
     * @test
     */
    public function test_delete_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/item_master_category_types/'.$itemMasterCategoryType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/item_master_category_types/'.$itemMasterCategoryType->id
        );

        $this->response->assertStatus(404);
    }
}
