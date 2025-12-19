<?php

use App\Models\TaxAuthority;
use App\Repositories\TaxAuthorityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxAuthorityRepositoryTest extends TestCase
{
    use MakeTaxAuthorityTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxAuthorityRepository
     */
    protected $taxAuthorityRepo;

    public function setUp()
    {
        parent::setUp();
        $this->taxAuthorityRepo = App::make(TaxAuthorityRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTaxAuthority()
    {
        $taxAuthority = $this->fakeTaxAuthorityData();
        $createdTaxAuthority = $this->taxAuthorityRepo->create($taxAuthority);
        $createdTaxAuthority = $createdTaxAuthority->toArray();
        $this->assertArrayHasKey('id', $createdTaxAuthority);
        $this->assertNotNull($createdTaxAuthority['id'], 'Created TaxAuthority must have id specified');
        $this->assertNotNull(TaxAuthority::find($createdTaxAuthority['id']), 'TaxAuthority with given id must be in DB');
        $this->assertModelData($taxAuthority, $createdTaxAuthority);
    }

    /**
     * @test read
     */
    public function testReadTaxAuthority()
    {
        $taxAuthority = $this->makeTaxAuthority();
        $dbTaxAuthority = $this->taxAuthorityRepo->find($taxAuthority->id);
        $dbTaxAuthority = $dbTaxAuthority->toArray();
        $this->assertModelData($taxAuthority->toArray(), $dbTaxAuthority);
    }

    /**
     * @test update
     */
    public function testUpdateTaxAuthority()
    {
        $taxAuthority = $this->makeTaxAuthority();
        $fakeTaxAuthority = $this->fakeTaxAuthorityData();
        $updatedTaxAuthority = $this->taxAuthorityRepo->update($fakeTaxAuthority, $taxAuthority->id);
        $this->assertModelData($fakeTaxAuthority, $updatedTaxAuthority->toArray());
        $dbTaxAuthority = $this->taxAuthorityRepo->find($taxAuthority->id);
        $this->assertModelData($fakeTaxAuthority, $dbTaxAuthority->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTaxAuthority()
    {
        $taxAuthority = $this->makeTaxAuthority();
        $resp = $this->taxAuthorityRepo->delete($taxAuthority->id);
        $this->assertTrue($resp);
        $this->assertNull(TaxAuthority::find($taxAuthority->id), 'TaxAuthority should not exist in DB');
    }
}
