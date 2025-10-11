<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\VatReturnFillingCategoryLanguage;

class VatReturnFillingCategoryLanguageApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/vat_return_filling_category_languages', $vatReturnFillingCategoryLanguage
        );

        $this->assertApiResponse($vatReturnFillingCategoryLanguage);
    }

    /**
     * @test
     */
    public function test_read_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_category_languages/'.$vatReturnFillingCategoryLanguage->id
        );

        $this->assertApiResponse($vatReturnFillingCategoryLanguage->toArray());
    }

    /**
     * @test
     */
    public function test_update_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->create();
        $editedVatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/vat_return_filling_category_languages/'.$vatReturnFillingCategoryLanguage->id,
            $editedVatReturnFillingCategoryLanguage
        );

        $this->assertApiResponse($editedVatReturnFillingCategoryLanguage);
    }

    /**
     * @test
     */
    public function test_delete_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/vat_return_filling_category_languages/'.$vatReturnFillingCategoryLanguage->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/vat_return_filling_category_languages/'.$vatReturnFillingCategoryLanguage->id
        );

        $this->response->assertStatus(404);
    }
}
