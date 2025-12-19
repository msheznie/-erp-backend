<?php namespace Tests\Repositories;

use App\Models\EliminationLedger;
use App\Repositories\EliminationLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EliminationLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EliminationLedgerRepository
     */
    protected $eliminationLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->eliminationLedgerRepo = \App::make(EliminationLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->make()->toArray();

        $createdEliminationLedger = $this->eliminationLedgerRepo->create($eliminationLedger);

        $createdEliminationLedger = $createdEliminationLedger->toArray();
        $this->assertArrayHasKey('id', $createdEliminationLedger);
        $this->assertNotNull($createdEliminationLedger['id'], 'Created EliminationLedger must have id specified');
        $this->assertNotNull(EliminationLedger::find($createdEliminationLedger['id']), 'EliminationLedger with given id must be in DB');
        $this->assertModelData($eliminationLedger, $createdEliminationLedger);
    }

    /**
     * @test read
     */
    public function test_read_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->create();

        $dbEliminationLedger = $this->eliminationLedgerRepo->find($eliminationLedger->id);

        $dbEliminationLedger = $dbEliminationLedger->toArray();
        $this->assertModelData($eliminationLedger->toArray(), $dbEliminationLedger);
    }

    /**
     * @test update
     */
    public function test_update_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->create();
        $fakeEliminationLedger = factory(EliminationLedger::class)->make()->toArray();

        $updatedEliminationLedger = $this->eliminationLedgerRepo->update($fakeEliminationLedger, $eliminationLedger->id);

        $this->assertModelData($fakeEliminationLedger, $updatedEliminationLedger->toArray());
        $dbEliminationLedger = $this->eliminationLedgerRepo->find($eliminationLedger->id);
        $this->assertModelData($fakeEliminationLedger, $dbEliminationLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_elimination_ledger()
    {
        $eliminationLedger = factory(EliminationLedger::class)->create();

        $resp = $this->eliminationLedgerRepo->delete($eliminationLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(EliminationLedger::find($eliminationLedger->id), 'EliminationLedger should not exist in DB');
    }
}
