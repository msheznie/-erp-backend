<?php namespace Tests\Repositories;

use App\Models\POSSTAGTaxLedger;
use App\Repositories\POSSTAGTaxLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGTaxLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGTaxLedgerRepository
     */
    protected $pOSSTAGTaxLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGTaxLedgerRepo = \App::make(POSSTAGTaxLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->make()->toArray();

        $createdPOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepo->create($pOSSTAGTaxLedger);

        $createdPOSSTAGTaxLedger = $createdPOSSTAGTaxLedger->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGTaxLedger);
        $this->assertNotNull($createdPOSSTAGTaxLedger['id'], 'Created POSSTAGTaxLedger must have id specified');
        $this->assertNotNull(POSSTAGTaxLedger::find($createdPOSSTAGTaxLedger['id']), 'POSSTAGTaxLedger with given id must be in DB');
        $this->assertModelData($pOSSTAGTaxLedger, $createdPOSSTAGTaxLedger);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->create();

        $dbPOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepo->find($pOSSTAGTaxLedger->id);

        $dbPOSSTAGTaxLedger = $dbPOSSTAGTaxLedger->toArray();
        $this->assertModelData($pOSSTAGTaxLedger->toArray(), $dbPOSSTAGTaxLedger);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->create();
        $fakePOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->make()->toArray();

        $updatedPOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepo->update($fakePOSSTAGTaxLedger, $pOSSTAGTaxLedger->id);

        $this->assertModelData($fakePOSSTAGTaxLedger, $updatedPOSSTAGTaxLedger->toArray());
        $dbPOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepo->find($pOSSTAGTaxLedger->id);
        $this->assertModelData($fakePOSSTAGTaxLedger, $dbPOSSTAGTaxLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->create();

        $resp = $this->pOSSTAGTaxLedgerRepo->delete($pOSSTAGTaxLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGTaxLedger::find($pOSSTAGTaxLedger->id), 'POSSTAGTaxLedger should not exist in DB');
    }
}
