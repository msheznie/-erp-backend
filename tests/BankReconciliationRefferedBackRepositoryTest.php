<?php

use App\Models\BankReconciliationRefferedBack;
use App\Repositories\BankReconciliationRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankReconciliationRefferedBackRepositoryTest extends TestCase
{
    use MakeBankReconciliationRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankReconciliationRefferedBackRepository
     */
    protected $bankReconciliationRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankReconciliationRefferedBackRepo = App::make(BankReconciliationRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->fakeBankReconciliationRefferedBackData();
        $createdBankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepo->create($bankReconciliationRefferedBack);
        $createdBankReconciliationRefferedBack = $createdBankReconciliationRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBankReconciliationRefferedBack);
        $this->assertNotNull($createdBankReconciliationRefferedBack['id'], 'Created BankReconciliationRefferedBack must have id specified');
        $this->assertNotNull(BankReconciliationRefferedBack::find($createdBankReconciliationRefferedBack['id']), 'BankReconciliationRefferedBack with given id must be in DB');
        $this->assertModelData($bankReconciliationRefferedBack, $createdBankReconciliationRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->makeBankReconciliationRefferedBack();
        $dbBankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepo->find($bankReconciliationRefferedBack->id);
        $dbBankReconciliationRefferedBack = $dbBankReconciliationRefferedBack->toArray();
        $this->assertModelData($bankReconciliationRefferedBack->toArray(), $dbBankReconciliationRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->makeBankReconciliationRefferedBack();
        $fakeBankReconciliationRefferedBack = $this->fakeBankReconciliationRefferedBackData();
        $updatedBankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepo->update($fakeBankReconciliationRefferedBack, $bankReconciliationRefferedBack->id);
        $this->assertModelData($fakeBankReconciliationRefferedBack, $updatedBankReconciliationRefferedBack->toArray());
        $dbBankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepo->find($bankReconciliationRefferedBack->id);
        $this->assertModelData($fakeBankReconciliationRefferedBack, $dbBankReconciliationRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->makeBankReconciliationRefferedBack();
        $resp = $this->bankReconciliationRefferedBackRepo->delete($bankReconciliationRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(BankReconciliationRefferedBack::find($bankReconciliationRefferedBack->id), 'BankReconciliationRefferedBack should not exist in DB');
    }
}
