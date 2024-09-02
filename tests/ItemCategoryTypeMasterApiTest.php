<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ItemCategoryTypeMaster;

class ItemCategoryTypeMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/item_category_type_masters', $itemCategoryTypeMaster
        );

        $this->assertApiResponse($itemCategoryTypeMaster);
    }

    /**
     * @test
     */
    public function test_read_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/item_category_type_masters/'.$itemCategoryTypeMaster->id
        );

        $this->assertApiResponse($itemCategoryTypeMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->create();
        $editedItemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/item_category_type_masters/'.$itemCategoryTypeMaster->id,
            $editedItemCategoryTypeMaster
        );

        $this->assertApiResponse($editedItemCategoryTypeMaster);
    }

    /**
     * @test
     */
    public function test_delete_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/item_category_type_masters/'.$itemCategoryTypeMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/item_category_type_masters/'.$itemCategoryTypeMaster->id
        );

        $this->response->assertStatus(404);
    }
}
