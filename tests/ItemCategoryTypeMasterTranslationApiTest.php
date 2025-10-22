<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ItemCategoryTypeMasterTranslation;

class ItemCategoryTypeMasterTranslationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/item_category_type_master_translations', $itemCategoryTypeMasterTranslation
        );

        $this->assertApiResponse($itemCategoryTypeMasterTranslation);
    }

    /**
     * @test
     */
    public function test_read_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/item_category_type_master_translations/'.$itemCategoryTypeMasterTranslation->id
        );

        $this->assertApiResponse($itemCategoryTypeMasterTranslation->toArray());
    }

    /**
     * @test
     */
    public function test_update_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->create();
        $editedItemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/item_category_type_master_translations/'.$itemCategoryTypeMasterTranslation->id,
            $editedItemCategoryTypeMasterTranslation
        );

        $this->assertApiResponse($editedItemCategoryTypeMasterTranslation);
    }

    /**
     * @test
     */
    public function test_delete_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/item_category_type_master_translations/'.$itemCategoryTypeMasterTranslation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/item_category_type_master_translations/'.$itemCategoryTypeMasterTranslation->id
        );

        $this->response->assertStatus(404);
    }
}
