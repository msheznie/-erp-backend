<?php

use App\Models\ErpItemLedger;
use App\Repositories\ErpItemLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErpItemLedgerRepositoryTest extends TestCase
{
    use MakeErpItemLedgerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpItemLedgerRepository
     */
    protected $erpItemLedgerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->erpItemLedgerRepo = App::make(ErpItemLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateErpItemLedger()
    {
        $erpItemLedger = $this->fakeErpItemLedgerData();
        $createdErpItemLedger = $this->erpItemLedgerRepo->create($erpItemLedger);
        $createdErpItemLedger = $createdErpItemLedger->toArray();
        $this->assertArrayHasKey('id', $createdErpItemLedger);
        $this->assertNotNull($createdErpItemLedger['id'], 'Created ErpItemLedger must have id specified');
        $this->assertNotNull(ErpItemLedger::find($createdErpItemLedger['id']), 'ErpItemLedger with given id must be in DB');
        $this->assertModelData($erpItemLedger, $createdErpItemLedger);
    }

    /**
     * @test read
     */
    public function testReadErpItemLedger()
    {
        $erpItemLedger = $this->makeErpItemLedger();
        $dbErpItemLedger = $this->erpItemLedgerRepo->find($erpItemLedger->id);
        $dbErpItemLedger = $dbErpItemLedger->toArray();
        $this->assertModelData($erpItemLedger->toArray(), $dbErpItemLedger);
    }

    /**
     * @test update
     */
    public function testUpdateErpItemLedger()
    {
        $erpItemLedger = $this->makeErpItemLedger();
        $fakeErpItemLedger = $this->fakeErpItemLedgerData();
        $updatedErpItemLedger = $this->erpItemLedgerRepo->update($fakeErpItemLedger, $erpItemLedger->id);
        $this->assertModelData($fakeErpItemLedger, $updatedErpItemLedger->toArray());
        $dbErpItemLedger = $this->erpItemLedgerRepo->find($erpItemLedger->id);
        $this->assertModelData($fakeErpItemLedger, $dbErpItemLedger->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteErpItemLedger()
    {
        $erpItemLedger = $this->makeErpItemLedger();
        $resp = $this->erpItemLedgerRepo->delete($erpItemLedger->id);
        $this->assertTrue($resp);
        $this->assertNull(ErpItemLedger::find($erpItemLedger->id), 'ErpItemLedger should not exist in DB');
    }
}
