<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpErpFormCategory;

class SrpErpFormCategoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_erp_form_categories', $srpErpFormCategory
        );

        $this->assertApiResponse($srpErpFormCategory);
    }

    /**
     * @test
     */
    public function test_read_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_erp_form_categories/'.$srpErpFormCategory->id
        );

        $this->assertApiResponse($srpErpFormCategory->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->create();
        $editedSrpErpFormCategory = factory(SrpErpFormCategory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_erp_form_categories/'.$srpErpFormCategory->id,
            $editedSrpErpFormCategory
        );

        $this->assertApiResponse($editedSrpErpFormCategory);
    }

    /**
     * @test
     */
    public function test_delete_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_erp_form_categories/'.$srpErpFormCategory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_erp_form_categories/'.$srpErpFormCategory->id
        );

        $this->response->assertStatus(404);
    }
}
