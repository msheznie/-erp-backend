<?php

use App\Models\TaxType;
use App\Repositories\TaxTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxTypeRepositoryTest extends TestCase
{
    use MakeTaxTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxTypeRepository
     */
    protected $taxTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->taxTypeRepo = App::make(TaxTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTaxType()
    {
        $taxType = $this->fakeTaxTypeData();
        $createdTaxType = $this->taxTypeRepo->create($taxType);
        $createdTaxType = $createdTaxType->toArray();
        $this->assertArrayHasKey('id', $createdTaxType);
        $this->assertNotNull($createdTaxType['id'], 'Created TaxType must have id specified');
        $this->assertNotNull(TaxType::find($createdTaxType['id']), 'TaxType with given id must be in DB');
        $this->assertModelData($taxType, $createdTaxType);
    }

    /**
     * @test read
     */
    public function testReadTaxType()
    {
        $taxType = $this->makeTaxType();
        $dbTaxType = $this->taxTypeRepo->find($taxType->id);
        $dbTaxType = $dbTaxType->toArray();
        $this->assertModelData($taxType->toArray(), $dbTaxType);
    }

    /**
     * @test update
     */
    public function testUpdateTaxType()
    {
        $taxType = $this->makeTaxType();
        $fakeTaxType = $this->fakeTaxTypeData();
        $updatedTaxType = $this->taxTypeRepo->update($fakeTaxType, $taxType->id);
        $this->assertModelData($fakeTaxType, $updatedTaxType->toArray());
        $dbTaxType = $this->taxTypeRepo->find($taxType->id);
        $this->assertModelData($fakeTaxType, $dbTaxType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTaxType()
    {
        $taxType = $this->makeTaxType();
        $resp = $this->taxTypeRepo->delete($taxType->id);
        $this->assertTrue($resp);
        $this->assertNull(TaxType::find($taxType->id), 'TaxType should not exist in DB');
    }
}
