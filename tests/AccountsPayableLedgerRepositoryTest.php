<?php

use App\Models\AccountsPayableLedger;
use App\Repositories\AccountsPayableLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountsPayableLedgerRepositoryTest extends TestCase
{
    use MakeAccountsPayableLedgerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountsPayableLedgerRepository
     */
    protected $accountsPayableLedgerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->accountsPayableLedgerRepo = App::make(AccountsPayableLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->fakeAccountsPayableLedgerData();
        $createdAccountsPayableLedger = $this->accountsPayableLedgerRepo->create($accountsPayableLedger);
        $createdAccountsPayableLedger = $createdAccountsPayableLedger->toArray();
        $this->assertArrayHasKey('id', $createdAccountsPayableLedger);
        $this->assertNotNull($createdAccountsPayableLedger['id'], 'Created AccountsPayableLedger must have id specified');
        $this->assertNotNull(AccountsPayableLedger::find($createdAccountsPayableLedger['id']), 'AccountsPayableLedger with given id must be in DB');
        $this->assertModelData($accountsPayableLedger, $createdAccountsPayableLedger);
    }

    /**
     * @test read
     */
    public function testReadAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->makeAccountsPayableLedger();
        $dbAccountsPayableLedger = $this->accountsPayableLedgerRepo->find($accountsPayableLedger->id);
        $dbAccountsPayableLedger = $dbAccountsPayableLedger->toArray();
        $this->assertModelData($accountsPayableLedger->toArray(), $dbAccountsPayableLedger);
    }

    /**
     * @test update
     */
    public function testUpdateAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->makeAccountsPayableLedger();
        $fakeAccountsPayableLedger = $this->fakeAccountsPayableLedgerData();
        $updatedAccountsPayableLedger = $this->accountsPayableLedgerRepo->update($fakeAccountsPayableLedger, $accountsPayableLedger->id);
        $this->assertModelData($fakeAccountsPayableLedger, $updatedAccountsPayableLedger->toArray());
        $dbAccountsPayableLedger = $this->accountsPayableLedgerRepo->find($accountsPayableLedger->id);
        $this->assertModelData($fakeAccountsPayableLedger, $dbAccountsPayableLedger->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->makeAccountsPayableLedger();
        $resp = $this->accountsPayableLedgerRepo->delete($accountsPayableLedger->id);
        $this->assertTrue($resp);
        $this->assertNull(AccountsPayableLedger::find($accountsPayableLedger->id), 'AccountsPayableLedger should not exist in DB');
    }
}
