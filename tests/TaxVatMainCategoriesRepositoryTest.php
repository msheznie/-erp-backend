<?php namespace Tests\Repositories;

use App\Models\TaxVatMainCategories;
use App\Repositories\TaxVatMainCategoriesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TaxVatMainCategoriesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxVatMainCategoriesRepository
     */
    protected $taxVatMainCategoriesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->taxVatMainCategoriesRepo = \App::make(TaxVatMainCategoriesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->make()->toArray();

        $createdTaxVatMainCategories = $this->taxVatMainCategoriesRepo->create($taxVatMainCategories);

        $createdTaxVatMainCategories = $createdTaxVatMainCategories->toArray();
        $this->assertArrayHasKey('id', $createdTaxVatMainCategories);
        $this->assertNotNull($createdTaxVatMainCategories['id'], 'Created TaxVatMainCategories must have id specified');
        $this->assertNotNull(TaxVatMainCategories::find($createdTaxVatMainCategories['id']), 'TaxVatMainCategories with given id must be in DB');
        $this->assertModelData($taxVatMainCategories, $createdTaxVatMainCategories);
    }

    /**
     * @test read
     */
    public function test_read_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->create();

        $dbTaxVatMainCategories = $this->taxVatMainCategoriesRepo->find($taxVatMainCategories->id);

        $dbTaxVatMainCategories = $dbTaxVatMainCategories->toArray();
        $this->assertModelData($taxVatMainCategories->toArray(), $dbTaxVatMainCategories);
    }

    /**
     * @test update
     */
    public function test_update_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->create();
        $fakeTaxVatMainCategories = factory(TaxVatMainCategories::class)->make()->toArray();

        $updatedTaxVatMainCategories = $this->taxVatMainCategoriesRepo->update($fakeTaxVatMainCategories, $taxVatMainCategories->id);

        $this->assertModelData($fakeTaxVatMainCategories, $updatedTaxVatMainCategories->toArray());
        $dbTaxVatMainCategories = $this->taxVatMainCategoriesRepo->find($taxVatMainCategories->id);
        $this->assertModelData($fakeTaxVatMainCategories, $dbTaxVatMainCategories->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tax_vat_main_categories()
    {
        $taxVatMainCategories = factory(TaxVatMainCategories::class)->create();

        $resp = $this->taxVatMainCategoriesRepo->delete($taxVatMainCategories->id);

        $this->assertTrue($resp);
        $this->assertNull(TaxVatMainCategories::find($taxVatMainCategories->id), 'TaxVatMainCategories should not exist in DB');
    }
}
