<?php

use App\Models\Tax;
use App\Repositories\TaxRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxRepositoryTest extends TestCase
{
    use MakeTaxTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxRepository
     */
    protected $taxRepo;

    public function setUp()
    {
        parent::setUp();
        $this->taxRepo = App::make(TaxRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTax()
    {
        $tax = $this->fakeTaxData();
        $createdTax = $this->taxRepo->create($tax);
        $createdTax = $createdTax->toArray();
        $this->assertArrayHasKey('id', $createdTax);
        $this->assertNotNull($createdTax['id'], 'Created Tax must have id specified');
        $this->assertNotNull(Tax::find($createdTax['id']), 'Tax with given id must be in DB');
        $this->assertModelData($tax, $createdTax);
    }

    /**
     * @test read
     */
    public function testReadTax()
    {
        $tax = $this->makeTax();
        $dbTax = $this->taxRepo->find($tax->id);
        $dbTax = $dbTax->toArray();
        $this->assertModelData($tax->toArray(), $dbTax);
    }

    /**
     * @test update
     */
    public function testUpdateTax()
    {
        $tax = $this->makeTax();
        $fakeTax = $this->fakeTaxData();
        $updatedTax = $this->taxRepo->update($fakeTax, $tax->id);
        $this->assertModelData($fakeTax, $updatedTax->toArray());
        $dbTax = $this->taxRepo->find($tax->id);
        $this->assertModelData($fakeTax, $dbTax->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTax()
    {
        $tax = $this->makeTax();
        $resp = $this->taxRepo->delete($tax->id);
        $this->assertTrue($resp);
        $this->assertNull(Tax::find($tax->id), 'Tax should not exist in DB');
    }
}
