<?php

use App\Models\Taxdetail;
use App\Repositories\TaxdetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxdetailRepositoryTest extends TestCase
{
    use MakeTaxdetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxdetailRepository
     */
    protected $taxdetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->taxdetailRepo = App::make(TaxdetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTaxdetail()
    {
        $taxdetail = $this->fakeTaxdetailData();
        $createdTaxdetail = $this->taxdetailRepo->create($taxdetail);
        $createdTaxdetail = $createdTaxdetail->toArray();
        $this->assertArrayHasKey('id', $createdTaxdetail);
        $this->assertNotNull($createdTaxdetail['id'], 'Created Taxdetail must have id specified');
        $this->assertNotNull(Taxdetail::find($createdTaxdetail['id']), 'Taxdetail with given id must be in DB');
        $this->assertModelData($taxdetail, $createdTaxdetail);
    }

    /**
     * @test read
     */
    public function testReadTaxdetail()
    {
        $taxdetail = $this->makeTaxdetail();
        $dbTaxdetail = $this->taxdetailRepo->find($taxdetail->id);
        $dbTaxdetail = $dbTaxdetail->toArray();
        $this->assertModelData($taxdetail->toArray(), $dbTaxdetail);
    }

    /**
     * @test update
     */
    public function testUpdateTaxdetail()
    {
        $taxdetail = $this->makeTaxdetail();
        $fakeTaxdetail = $this->fakeTaxdetailData();
        $updatedTaxdetail = $this->taxdetailRepo->update($fakeTaxdetail, $taxdetail->id);
        $this->assertModelData($fakeTaxdetail, $updatedTaxdetail->toArray());
        $dbTaxdetail = $this->taxdetailRepo->find($taxdetail->id);
        $this->assertModelData($fakeTaxdetail, $dbTaxdetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTaxdetail()
    {
        $taxdetail = $this->makeTaxdetail();
        $resp = $this->taxdetailRepo->delete($taxdetail->id);
        $this->assertTrue($resp);
        $this->assertNull(Taxdetail::find($taxdetail->id), 'Taxdetail should not exist in DB');
    }
}
