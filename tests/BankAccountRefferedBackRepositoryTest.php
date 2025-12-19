<?php

use App\Models\BankAccountRefferedBack;
use App\Repositories\BankAccountRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankAccountRefferedBackRepositoryTest extends TestCase
{
    use MakeBankAccountRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankAccountRefferedBackRepository
     */
    protected $bankAccountRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankAccountRefferedBackRepo = App::make(BankAccountRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->fakeBankAccountRefferedBackData();
        $createdBankAccountRefferedBack = $this->bankAccountRefferedBackRepo->create($bankAccountRefferedBack);
        $createdBankAccountRefferedBack = $createdBankAccountRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBankAccountRefferedBack);
        $this->assertNotNull($createdBankAccountRefferedBack['id'], 'Created BankAccountRefferedBack must have id specified');
        $this->assertNotNull(BankAccountRefferedBack::find($createdBankAccountRefferedBack['id']), 'BankAccountRefferedBack with given id must be in DB');
        $this->assertModelData($bankAccountRefferedBack, $createdBankAccountRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->makeBankAccountRefferedBack();
        $dbBankAccountRefferedBack = $this->bankAccountRefferedBackRepo->find($bankAccountRefferedBack->id);
        $dbBankAccountRefferedBack = $dbBankAccountRefferedBack->toArray();
        $this->assertModelData($bankAccountRefferedBack->toArray(), $dbBankAccountRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->makeBankAccountRefferedBack();
        $fakeBankAccountRefferedBack = $this->fakeBankAccountRefferedBackData();
        $updatedBankAccountRefferedBack = $this->bankAccountRefferedBackRepo->update($fakeBankAccountRefferedBack, $bankAccountRefferedBack->id);
        $this->assertModelData($fakeBankAccountRefferedBack, $updatedBankAccountRefferedBack->toArray());
        $dbBankAccountRefferedBack = $this->bankAccountRefferedBackRepo->find($bankAccountRefferedBack->id);
        $this->assertModelData($fakeBankAccountRefferedBack, $dbBankAccountRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->makeBankAccountRefferedBack();
        $resp = $this->bankAccountRefferedBackRepo->delete($bankAccountRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(BankAccountRefferedBack::find($bankAccountRefferedBack->id), 'BankAccountRefferedBack should not exist in DB');
    }
}
