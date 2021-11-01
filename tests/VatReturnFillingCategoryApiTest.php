<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFillingCategory;

class VatReturnFillingCategoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filling_categories', $vatReturnFillingCategory
        );

        $this->assertApiResponse($vatReturnFillingCategory);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_categories/'.$vatReturnFillingCategory->id
        );

        $this->assertApiResponse($vatReturnFillingCategory->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->create();
        $editedVatReturnFillingCategory = factory(VatReturnFillingCategory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filling_categories/'.$vatReturnFillingCategory->id,
            $editedVatReturnFillingCategory
        );

        $this->assertApiResponse($editedVatReturnFillingCategory);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filling_categories/'.$vatReturnFillingCategory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_categories/'.$vatReturnFillingCategory->id
        );

        $this->response->assertStatus(404);
    }
}
