<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PosStagMenuCategory;

class PosStagMenuCategoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pos_stag_menu_categories', $posStagMenuCategory
        );

        $this->assertApiResponse($posStagMenuCategory);
    }

    /**
     * @test
     */
    public function test_read_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pos_stag_menu_categories/'.$posStagMenuCategory->id
        );

        $this->assertApiResponse($posStagMenuCategory->toArray());
    }

    /**
     * @test
     */
    public function test_update_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->create();
        $editedPosStagMenuCategory = factory(PosStagMenuCategory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pos_stag_menu_categories/'.$posStagMenuCategory->id,
            $editedPosStagMenuCategory
        );

        $this->assertApiResponse($editedPosStagMenuCategory);
    }

    /**
     * @test
     */
    public function test_delete_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pos_stag_menu_categories/'.$posStagMenuCategory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pos_stag_menu_categories/'.$posStagMenuCategory->id
        );

        $this->response->assertStatus(404);
    }
}
