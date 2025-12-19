<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatSubCategoryType;

class VatSubCategoryTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_sub_category_types', $vatSubCategoryType
        );

        $this->assertApiResponse($vatSubCategoryType);
    }

    /**
     * @test
     */
    public function test_read_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_sub_category_types/'.$vatSubCategoryType->id
        );

        $this->assertApiResponse($vatSubCategoryType->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->create();
        $editedVatSubCategoryType = factory(VatSubCategoryType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_sub_category_types/'.$vatSubCategoryType->id,
            $editedVatSubCategoryType
        );

        $this->assertApiResponse($editedVatSubCategoryType);
    }

    /**
     * @test
     */
    public function test_delete_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_sub_category_types/'.$vatSubCategoryType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_sub_category_types/'.$vatSubCategoryType->id
        );

        $this->response->assertStatus(404);
    }
}
