<?php

use App\Models\BankAccount;
use App\Repositories\BankAccountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankAccountRepositoryTest extends TestCase
{
    use MakeBankAccountTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankAccountRepository
     */
    protected $bankAccountRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankAccountRepo = App::make(BankAccountRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankAccount()
    {
        $bankAccount = $this->fakeBankAccountData();
        $createdBankAccount = $this->bankAccountRepo->create($bankAccount);
        $createdBankAccount = $createdBankAccount->toArray();
        $this->assertArrayHasKey('id', $createdBankAccount);
        $this->assertNotNull($createdBankAccount['id'], 'Created BankAccount must have id specified');
        $this->assertNotNull(BankAccount::find($createdBankAccount['id']), 'BankAccount with given id must be in DB');
        $this->assertModelData($bankAccount, $createdBankAccount);
    }

    /**
     * @test read
     */
    public function testReadBankAccount()
    {
        $bankAccount = $this->makeBankAccount();
        $dbBankAccount = $this->bankAccountRepo->find($bankAccount->id);
        $dbBankAccount = $dbBankAccount->toArray();
        $this->assertModelData($bankAccount->toArray(), $dbBankAccount);
    }

    /**
     * @test update
     */
    public function testUpdateBankAccount()
    {
        $bankAccount = $this->makeBankAccount();
        $fakeBankAccount = $this->fakeBankAccountData();
        $updatedBankAccount = $this->bankAccountRepo->update($fakeBankAccount, $bankAccount->id);
        $this->assertModelData($fakeBankAccount, $updatedBankAccount->toArray());
        $dbBankAccount = $this->bankAccountRepo->find($bankAccount->id);
        $this->assertModelData($fakeBankAccount, $dbBankAccount->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankAccount()
    {
        $bankAccount = $this->makeBankAccount();
        $resp = $this->bankAccountRepo->delete($bankAccount->id);
        $this->assertTrue($resp);
        $this->assertNull(BankAccount::find($bankAccount->id), 'BankAccount should not exist in DB');
    }
}
