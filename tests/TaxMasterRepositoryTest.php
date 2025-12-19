<?php namespace Tests\Repositories;

use App\Models\TaxMaster;
use App\Repositories\TaxMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TaxMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxMasterRepository
     */
    protected $taxMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->taxMasterRepo = \App::make(TaxMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->make()->toArray();

        $createdTaxMaster = $this->taxMasterRepo->create($taxMaster);

        $createdTaxMaster = $createdTaxMaster->toArray();
        $this->assertArrayHasKey('id', $createdTaxMaster);
        $this->assertNotNull($createdTaxMaster['id'], 'Created TaxMaster must have id specified');
        $this->assertNotNull(TaxMaster::find($createdTaxMaster['id']), 'TaxMaster with given id must be in DB');
        $this->assertModelData($taxMaster, $createdTaxMaster);
    }

    /**
     * @test read
     */
    public function test_read_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->create();

        $dbTaxMaster = $this->taxMasterRepo->find($taxMaster->id);

        $dbTaxMaster = $dbTaxMaster->toArray();
        $this->assertModelData($taxMaster->toArray(), $dbTaxMaster);
    }

    /**
     * @test update
     */
    public function test_update_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->create();
        $fakeTaxMaster = factory(TaxMaster::class)->make()->toArray();

        $updatedTaxMaster = $this->taxMasterRepo->update($fakeTaxMaster, $taxMaster->id);

        $this->assertModelData($fakeTaxMaster, $updatedTaxMaster->toArray());
        $dbTaxMaster = $this->taxMasterRepo->find($taxMaster->id);
        $this->assertModelData($fakeTaxMaster, $dbTaxMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tax_master()
    {
        $taxMaster = factory(TaxMaster::class)->create();

        $resp = $this->taxMasterRepo->delete($taxMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(TaxMaster::find($taxMaster->id), 'TaxMaster should not exist in DB');
    }
}
