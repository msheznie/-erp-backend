<?php namespace Tests\Repositories;

use App\Models\TaxVatCategories;
use App\Repositories\TaxVatCategoriesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TaxVatCategoriesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxVatCategoriesRepository
     */
    protected $taxVatCategoriesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->taxVatCategoriesRepo = \App::make(TaxVatCategoriesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->make()->toArray();

        $createdTaxVatCategories = $this->taxVatCategoriesRepo->create($taxVatCategories);

        $createdTaxVatCategories = $createdTaxVatCategories->toArray();
        $this->assertArrayHasKey('id', $createdTaxVatCategories);
        $this->assertNotNull($createdTaxVatCategories['id'], 'Created TaxVatCategories must have id specified');
        $this->assertNotNull(TaxVatCategories::find($createdTaxVatCategories['id']), 'TaxVatCategories with given id must be in DB');
        $this->assertModelData($taxVatCategories, $createdTaxVatCategories);
    }

    /**
     * @test read
     */
    public function test_read_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->create();

        $dbTaxVatCategories = $this->taxVatCategoriesRepo->find($taxVatCategories->id);

        $dbTaxVatCategories = $dbTaxVatCategories->toArray();
        $this->assertModelData($taxVatCategories->toArray(), $dbTaxVatCategories);
    }

    /**
     * @test update
     */
    public function test_update_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->create();
        $fakeTaxVatCategories = factory(TaxVatCategories::class)->make()->toArray();

        $updatedTaxVatCategories = $this->taxVatCategoriesRepo->update($fakeTaxVatCategories, $taxVatCategories->id);

        $this->assertModelData($fakeTaxVatCategories, $updatedTaxVatCategories->toArray());
        $dbTaxVatCategories = $this->taxVatCategoriesRepo->find($taxVatCategories->id);
        $this->assertModelData($fakeTaxVatCategories, $dbTaxVatCategories->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tax_vat_categories()
    {
        $taxVatCategories = factory(TaxVatCategories::class)->create();

        $resp = $this->taxVatCategoriesRepo->delete($taxVatCategories->id);

        $this->assertTrue($resp);
        $this->assertNull(TaxVatCategories::find($taxVatCategories->id), 'TaxVatCategories should not exist in DB');
    }
}
