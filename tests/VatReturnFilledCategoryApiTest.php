<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFilledCategory;

class VatReturnFilledCategoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filled_categories', $vatReturnFilledCategory
        );

        $this->assertApiResponse($vatReturnFilledCategory);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filled_categories/'.$vatReturnFilledCategory->id
        );

        $this->assertApiResponse($vatReturnFilledCategory->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->create();
        $editedVatReturnFilledCategory = factory(VatReturnFilledCategory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filled_categories/'.$vatReturnFilledCategory->id,
            $editedVatReturnFilledCategory
        );

        $this->assertApiResponse($editedVatReturnFilledCategory);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filled_categories/'.$vatReturnFilledCategory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filled_categories/'.$vatReturnFilledCategory->id
        );

        $this->response->assertStatus(404);
    }
}
