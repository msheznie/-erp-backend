<?php namespace Tests\Repositories;

use App\Models\TaxLedger;
use App\Repositories\TaxLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TaxLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxLedgerRepository
     */
    protected $taxLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->taxLedgerRepo = \App::make(TaxLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tax_ledger()
    {
        $taxLedger = factory(TaxLedger::class)->make()->toArray();

        $createdTaxLedger = $this->taxLedgerRepo->create($taxLedger);

        $createdTaxLedger = $createdTaxLedger->toArray();
        $this->assertArrayHasKey('id', $createdTaxLedger);
        $this->assertNotNull($createdTaxLedger['id'], 'Created TaxLedger must have id specified');
        $this->assertNotNull(TaxLedger::find($createdTaxLedger['id']), 'TaxLedger with given id must be in DB');
        $this->assertModelData($taxLedger, $createdTaxLedger);
    }

    /**
     * @test read
     */
    public function test_read_tax_ledger()
    {
        $taxLedger = factory(TaxLedger::class)->create();

        $dbTaxLedger = $this->taxLedgerRepo->find($taxLedger->id);

        $dbTaxLedger = $dbTaxLedger->toArray();
        $this->assertModelData($taxLedger->toArray(), $dbTaxLedger);
    }

    /**
     * @test update
     */
    public function test_update_tax_ledger()
    {
        $taxLedger = factory(TaxLedger::class)->create();
        $fakeTaxLedger = factory(TaxLedger::class)->make()->toArray();

        $updatedTaxLedger = $this->taxLedgerRepo->update($fakeTaxLedger, $taxLedger->id);

        $this->assertModelData($fakeTaxLedger, $updatedTaxLedger->toArray());
        $dbTaxLedger = $this->taxLedgerRepo->find($taxLedger->id);
        $this->assertModelData($fakeTaxLedger, $dbTaxLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tax_ledger()
    {
        $taxLedger = factory(TaxLedger::class)->create();

        $resp = $this->taxLedgerRepo->delete($taxLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(TaxLedger::find($taxLedger->id), 'TaxLedger should not exist in DB');
    }
}
