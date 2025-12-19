<?php

use App\Models\AccountsReceivableLedger;
use App\Repositories\AccountsReceivableLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountsReceivableLedgerRepositoryTest extends TestCase
{
    use MakeAccountsReceivableLedgerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountsReceivableLedgerRepository
     */
    protected $accountsReceivableLedgerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->accountsReceivableLedgerRepo = App::make(AccountsReceivableLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->fakeAccountsReceivableLedgerData();
        $createdAccountsReceivableLedger = $this->accountsReceivableLedgerRepo->create($accountsReceivableLedger);
        $createdAccountsReceivableLedger = $createdAccountsReceivableLedger->toArray();
        $this->assertArrayHasKey('id', $createdAccountsReceivableLedger);
        $this->assertNotNull($createdAccountsReceivableLedger['id'], 'Created AccountsReceivableLedger must have id specified');
        $this->assertNotNull(AccountsReceivableLedger::find($createdAccountsReceivableLedger['id']), 'AccountsReceivableLedger with given id must be in DB');
        $this->assertModelData($accountsReceivableLedger, $createdAccountsReceivableLedger);
    }

    /**
     * @test read
     */
    public function testReadAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->makeAccountsReceivableLedger();
        $dbAccountsReceivableLedger = $this->accountsReceivableLedgerRepo->find($accountsReceivableLedger->id);
        $dbAccountsReceivableLedger = $dbAccountsReceivableLedger->toArray();
        $this->assertModelData($accountsReceivableLedger->toArray(), $dbAccountsReceivableLedger);
    }

    /**
     * @test update
     */
    public function testUpdateAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->makeAccountsReceivableLedger();
        $fakeAccountsReceivableLedger = $this->fakeAccountsReceivableLedgerData();
        $updatedAccountsReceivableLedger = $this->accountsReceivableLedgerRepo->update($fakeAccountsReceivableLedger, $accountsReceivableLedger->id);
        $this->assertModelData($fakeAccountsReceivableLedger, $updatedAccountsReceivableLedger->toArray());
        $dbAccountsReceivableLedger = $this->accountsReceivableLedgerRepo->find($accountsReceivableLedger->id);
        $this->assertModelData($fakeAccountsReceivableLedger, $dbAccountsReceivableLedger->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->makeAccountsReceivableLedger();
        $resp = $this->accountsReceivableLedgerRepo->delete($accountsReceivableLedger->id);
        $this->assertTrue($resp);
        $this->assertNull(AccountsReceivableLedger::find($accountsReceivableLedger->id), 'AccountsReceivableLedger should not exist in DB');
    }
}
