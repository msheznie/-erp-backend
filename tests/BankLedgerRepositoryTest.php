<?php

use App\Models\BankLedger;
use App\Repositories\BankLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankLedgerRepositoryTest extends TestCase
{
    use MakeBankLedgerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankLedgerRepository
     */
    protected $bankLedgerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankLedgerRepo = App::make(BankLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankLedger()
    {
        $bankLedger = $this->fakeBankLedgerData();
        $createdBankLedger = $this->bankLedgerRepo->create($bankLedger);
        $createdBankLedger = $createdBankLedger->toArray();
        $this->assertArrayHasKey('id', $createdBankLedger);
        $this->assertNotNull($createdBankLedger['id'], 'Created BankLedger must have id specified');
        $this->assertNotNull(BankLedger::find($createdBankLedger['id']), 'BankLedger with given id must be in DB');
        $this->assertModelData($bankLedger, $createdBankLedger);
    }

    /**
     * @test read
     */
    public function testReadBankLedger()
    {
        $bankLedger = $this->makeBankLedger();
        $dbBankLedger = $this->bankLedgerRepo->find($bankLedger->id);
        $dbBankLedger = $dbBankLedger->toArray();
        $this->assertModelData($bankLedger->toArray(), $dbBankLedger);
    }

    /**
     * @test update
     */
    public function testUpdateBankLedger()
    {
        $bankLedger = $this->makeBankLedger();
        $fakeBankLedger = $this->fakeBankLedgerData();
        $updatedBankLedger = $this->bankLedgerRepo->update($fakeBankLedger, $bankLedger->id);
        $this->assertModelData($fakeBankLedger, $updatedBankLedger->toArray());
        $dbBankLedger = $this->bankLedgerRepo->find($bankLedger->id);
        $this->assertModelData($fakeBankLedger, $dbBankLedger->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankLedger()
    {
        $bankLedger = $this->makeBankLedger();
        $resp = $this->bankLedgerRepo->delete($bankLedger->id);
        $this->assertTrue($resp);
        $this->assertNull(BankLedger::find($bankLedger->id), 'BankLedger should not exist in DB');
    }
}
