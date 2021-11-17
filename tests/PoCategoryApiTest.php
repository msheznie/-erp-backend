<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PoCategory;

class PoCategoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_po_category()
    {
        $poCategory = factory(PoCategory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/po_categories', $poCategory
        );

        $this->assertApiResponse($poCategory);
    }

    /**
     * @test
     */
    public function test_read_po_category()
    {
        $poCategory = factory(PoCategory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/po_categories/'.$poCategory->id
        );

        $this->assertApiResponse($poCategory->toArray());
    }

    /**
     * @test
     */
    public function test_update_po_category()
    {
        $poCategory = factory(PoCategory::class)->create();
        $editedPoCategory = factory(PoCategory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/po_categories/'.$poCategory->id,
            $editedPoCategory
        );

        $this->assertApiResponse($editedPoCategory);
    }

    /**
     * @test
     */
    public function test_delete_po_category()
    {
        $poCategory = factory(PoCategory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/po_categories/'.$poCategory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/po_categories/'.$poCategory->id
        );

        $this->response->assertStatus(404);
    }
}
