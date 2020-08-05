<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TaxVatCategories;

class TaxVatCategoriesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tax_vat_categories', $taxVatCategories
        );

        $this->assertApiResponse($taxVatCategories);
    }

    /**
     * @test
     */
    public function test_read_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tax_vat_categories/'.$taxVatCategories->id
        );

        $this->assertApiResponse($taxVatCategories->toArray());
    }

    /**
     * @test
     */
    public function test_update_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->create();
        $editedTaxVatCategories = factory(TaxVatCategories::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tax_vat_categories/'.$taxVatCategories->id,
            $editedTaxVatCategories
        );

        $this->assertApiResponse($editedTaxVatCategories);
    }

    /**
     * @test
     */
    public function test_delete_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tax_vat_categories/'.$taxVatCategories->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tax_vat_categories/'.$taxVatCategories->id
        );

        $this->response->assertStatus(404);
    }
}
