<?php

use App\Models\TaxFormulaMaster;
use App\Repositories\TaxFormulaMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxFormulaMasterRepositoryTest extends TestCase
{
    use MakeTaxFormulaMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxFormulaMasterRepository
     */
    protected $taxFormulaMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->taxFormulaMasterRepo = App::make(TaxFormulaMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->fakeTaxFormulaMasterData();
        $createdTaxFormulaMaster = $this->taxFormulaMasterRepo->create($taxFormulaMaster);
        $createdTaxFormulaMaster = $createdTaxFormulaMaster->toArray();
        $this->assertArrayHasKey('id', $createdTaxFormulaMaster);
        $this->assertNotNull($createdTaxFormulaMaster['id'], 'Created TaxFormulaMaster must have id specified');
        $this->assertNotNull(TaxFormulaMaster::find($createdTaxFormulaMaster['id']), 'TaxFormulaMaster with given id must be in DB');
        $this->assertModelData($taxFormulaMaster, $createdTaxFormulaMaster);
    }

    /**
     * @test read
     */
    public function testReadTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->makeTaxFormulaMaster();
        $dbTaxFormulaMaster = $this->taxFormulaMasterRepo->find($taxFormulaMaster->id);
        $dbTaxFormulaMaster = $dbTaxFormulaMaster->toArray();
        $this->assertModelData($taxFormulaMaster->toArray(), $dbTaxFormulaMaster);
    }

    /**
     * @test update
     */
    public function testUpdateTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->makeTaxFormulaMaster();
        $fakeTaxFormulaMaster = $this->fakeTaxFormulaMasterData();
        $updatedTaxFormulaMaster = $this->taxFormulaMasterRepo->update($fakeTaxFormulaMaster, $taxFormulaMaster->id);
        $this->assertModelData($fakeTaxFormulaMaster, $updatedTaxFormulaMaster->toArray());
        $dbTaxFormulaMaster = $this->taxFormulaMasterRepo->find($taxFormulaMaster->id);
        $this->assertModelData($fakeTaxFormulaMaster, $dbTaxFormulaMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->makeTaxFormulaMaster();
        $resp = $this->taxFormulaMasterRepo->delete($taxFormulaMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(TaxFormulaMaster::find($taxFormulaMaster->id), 'TaxFormulaMaster should not exist in DB');
    }
}
