<?php namespace Tests\Repositories;

use App\Models\TaxLedgerDetail;
use App\Repositories\TaxLedgerDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TaxLedgerDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxLedgerDetailRepository
     */
    protected $taxLedgerDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->taxLedgerDetailRepo = \App::make(TaxLedgerDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->make()->toArray();

        $createdTaxLedgerDetail = $this->taxLedgerDetailRepo->create($taxLedgerDetail);

        $createdTaxLedgerDetail = $createdTaxLedgerDetail->toArray();
        $this->assertArrayHasKey('id', $createdTaxLedgerDetail);
        $this->assertNotNull($createdTaxLedgerDetail['id'], 'Created TaxLedgerDetail must have id specified');
        $this->assertNotNull(TaxLedgerDetail::find($createdTaxLedgerDetail['id']), 'TaxLedgerDetail with given id must be in DB');
        $this->assertModelData($taxLedgerDetail, $createdTaxLedgerDetail);
    }

    /**
     * @test read
     */
    public function test_read_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->create();

        $dbTaxLedgerDetail = $this->taxLedgerDetailRepo->find($taxLedgerDetail->id);

        $dbTaxLedgerDetail = $dbTaxLedgerDetail->toArray();
        $this->assertModelData($taxLedgerDetail->toArray(), $dbTaxLedgerDetail);
    }

    /**
     * @test update
     */
    public function test_update_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->create();
        $fakeTaxLedgerDetail = factory(TaxLedgerDetail::class)->make()->toArray();

        $updatedTaxLedgerDetail = $this->taxLedgerDetailRepo->update($fakeTaxLedgerDetail, $taxLedgerDetail->id);

        $this->assertModelData($fakeTaxLedgerDetail, $updatedTaxLedgerDetail->toArray());
        $dbTaxLedgerDetail = $this->taxLedgerDetailRepo->find($taxLedgerDetail->id);
        $this->assertModelData($fakeTaxLedgerDetail, $dbTaxLedgerDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->create();

        $resp = $this->taxLedgerDetailRepo->delete($taxLedgerDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(TaxLedgerDetail::find($taxLedgerDetail->id), 'TaxLedgerDetail should not exist in DB');
    }
}
