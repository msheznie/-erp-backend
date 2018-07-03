<?php

use App\Models\GeneralLedger;
use App\Repositories\GeneralLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeneralLedgerRepositoryTest extends TestCase
{
    use MakeGeneralLedgerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GeneralLedgerRepository
     */
    protected $generalLedgerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->generalLedgerRepo = App::make(GeneralLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGeneralLedger()
    {
        $generalLedger = $this->fakeGeneralLedgerData();
        $createdGeneralLedger = $this->generalLedgerRepo->create($generalLedger);
        $createdGeneralLedger = $createdGeneralLedger->toArray();
        $this->assertArrayHasKey('id', $createdGeneralLedger);
        $this->assertNotNull($createdGeneralLedger['id'], 'Created GeneralLedger must have id specified');
        $this->assertNotNull(GeneralLedger::find($createdGeneralLedger['id']), 'GeneralLedger with given id must be in DB');
        $this->assertModelData($generalLedger, $createdGeneralLedger);
    }

    /**
     * @test read
     */
    public function testReadGeneralLedger()
    {
        $generalLedger = $this->makeGeneralLedger();
        $dbGeneralLedger = $this->generalLedgerRepo->find($generalLedger->id);
        $dbGeneralLedger = $dbGeneralLedger->toArray();
        $this->assertModelData($generalLedger->toArray(), $dbGeneralLedger);
    }

    /**
     * @test update
     */
    public function testUpdateGeneralLedger()
    {
        $generalLedger = $this->makeGeneralLedger();
        $fakeGeneralLedger = $this->fakeGeneralLedgerData();
        $updatedGeneralLedger = $this->generalLedgerRepo->update($fakeGeneralLedger, $generalLedger->id);
        $this->assertModelData($fakeGeneralLedger, $updatedGeneralLedger->toArray());
        $dbGeneralLedger = $this->generalLedgerRepo->find($generalLedger->id);
        $this->assertModelData($fakeGeneralLedger, $dbGeneralLedger->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGeneralLedger()
    {
        $generalLedger = $this->makeGeneralLedger();
        $resp = $this->generalLedgerRepo->delete($generalLedger->id);
        $this->assertTrue($resp);
        $this->assertNull(GeneralLedger::find($generalLedger->id), 'GeneralLedger should not exist in DB');
    }
}
