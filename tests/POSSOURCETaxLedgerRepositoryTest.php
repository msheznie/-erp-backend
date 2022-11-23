<?php namespace Tests\Repositories;

use App\Models\POSSOURCETaxLedger;
use App\Repositories\POSSOURCETaxLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCETaxLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCETaxLedgerRepository
     */
    protected $pOSSOURCETaxLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCETaxLedgerRepo = \App::make(POSSOURCETaxLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->make()->toArray();

        $createdPOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepo->create($pOSSOURCETaxLedger);

        $createdPOSSOURCETaxLedger = $createdPOSSOURCETaxLedger->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCETaxLedger);
        $this->assertNotNull($createdPOSSOURCETaxLedger['id'], 'Created POSSOURCETaxLedger must have id specified');
        $this->assertNotNull(POSSOURCETaxLedger::find($createdPOSSOURCETaxLedger['id']), 'POSSOURCETaxLedger with given id must be in DB');
        $this->assertModelData($pOSSOURCETaxLedger, $createdPOSSOURCETaxLedger);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->create();

        $dbPOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepo->find($pOSSOURCETaxLedger->id);

        $dbPOSSOURCETaxLedger = $dbPOSSOURCETaxLedger->toArray();
        $this->assertModelData($pOSSOURCETaxLedger->toArray(), $dbPOSSOURCETaxLedger);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->create();
        $fakePOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->make()->toArray();

        $updatedPOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepo->update($fakePOSSOURCETaxLedger, $pOSSOURCETaxLedger->id);

        $this->assertModelData($fakePOSSOURCETaxLedger, $updatedPOSSOURCETaxLedger->toArray());
        $dbPOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepo->find($pOSSOURCETaxLedger->id);
        $this->assertModelData($fakePOSSOURCETaxLedger, $dbPOSSOURCETaxLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->create();

        $resp = $this->pOSSOURCETaxLedgerRepo->delete($pOSSOURCETaxLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCETaxLedger::find($pOSSOURCETaxLedger->id), 'POSSOURCETaxLedger should not exist in DB');
    }
}
