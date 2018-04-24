<?php

use App\Models\TaxFormulaDetail;
use App\Repositories\TaxFormulaDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxFormulaDetailRepositoryTest extends TestCase
{
    use MakeTaxFormulaDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxFormulaDetailRepository
     */
    protected $taxFormulaDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->taxFormulaDetailRepo = App::make(TaxFormulaDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->fakeTaxFormulaDetailData();
        $createdTaxFormulaDetail = $this->taxFormulaDetailRepo->create($taxFormulaDetail);
        $createdTaxFormulaDetail = $createdTaxFormulaDetail->toArray();
        $this->assertArrayHasKey('id', $createdTaxFormulaDetail);
        $this->assertNotNull($createdTaxFormulaDetail['id'], 'Created TaxFormulaDetail must have id specified');
        $this->assertNotNull(TaxFormulaDetail::find($createdTaxFormulaDetail['id']), 'TaxFormulaDetail with given id must be in DB');
        $this->assertModelData($taxFormulaDetail, $createdTaxFormulaDetail);
    }

    /**
     * @test read
     */
    public function testReadTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->makeTaxFormulaDetail();
        $dbTaxFormulaDetail = $this->taxFormulaDetailRepo->find($taxFormulaDetail->id);
        $dbTaxFormulaDetail = $dbTaxFormulaDetail->toArray();
        $this->assertModelData($taxFormulaDetail->toArray(), $dbTaxFormulaDetail);
    }

    /**
     * @test update
     */
    public function testUpdateTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->makeTaxFormulaDetail();
        $fakeTaxFormulaDetail = $this->fakeTaxFormulaDetailData();
        $updatedTaxFormulaDetail = $this->taxFormulaDetailRepo->update($fakeTaxFormulaDetail, $taxFormulaDetail->id);
        $this->assertModelData($fakeTaxFormulaDetail, $updatedTaxFormulaDetail->toArray());
        $dbTaxFormulaDetail = $this->taxFormulaDetailRepo->find($taxFormulaDetail->id);
        $this->assertModelData($fakeTaxFormulaDetail, $dbTaxFormulaDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->makeTaxFormulaDetail();
        $resp = $this->taxFormulaDetailRepo->delete($taxFormulaDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(TaxFormulaDetail::find($taxFormulaDetail->id), 'TaxFormulaDetail should not exist in DB');
    }
}
