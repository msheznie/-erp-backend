<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PosSourceMenuCategory;

class PosSourceMenuCategoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pos_source_menu_categories', $posSourceMenuCategory
        );

        $this->assertApiResponse($posSourceMenuCategory);
    }

    /**
     * @test
     */
    public function test_read_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pos_source_menu_categories/'.$posSourceMenuCategory->id
        );

        $this->assertApiResponse($posSourceMenuCategory->toArray());
    }

    /**
     * @test
     */
    public function test_update_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->create();
        $editedPosSourceMenuCategory = factory(PosSourceMenuCategory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pos_source_menu_categories/'.$posSourceMenuCategory->id,
            $editedPosSourceMenuCategory
        );

        $this->assertApiResponse($editedPosSourceMenuCategory);
    }

    /**
     * @test
     */
    public function test_delete_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pos_source_menu_categories/'.$posSourceMenuCategory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pos_source_menu_categories/'.$posSourceMenuCategory->id
        );

        $this->response->assertStatus(404);
    }
}
