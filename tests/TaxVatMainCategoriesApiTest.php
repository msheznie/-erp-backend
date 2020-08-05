<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TaxVatMainCategories;

class TaxVatMainCategoriesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tax_vat_main_categories', $taxVatMainCategories
        );

        $this->assertApiResponse($taxVatMainCategories);
    }

    /**
     * @test
     */
    public function test_read_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tax_vat_main_categories/'.$taxVatMainCategories->id
        );

        $this->assertApiResponse($taxVatMainCategories->toArray());
    }

    /**
     * @test
     */
    public function test_update_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->create();
        $editedTaxVatMainCategories = factory(TaxVatMainCategories::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tax_vat_main_categories/'.$taxVatMainCategories->id,
            $editedTaxVatMainCategories
        );

        $this->assertApiResponse($editedTaxVatMainCategories);
    }

    /**
     * @test
     */
    public function test_delete_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tax_vat_main_categories/'.$taxVatMainCategories->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tax_vat_main_categories/'.$taxVatMainCategories->id
        );

        $this->response->assertStatus(404);
    }
}
