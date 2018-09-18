<?php

use App\Models\BankReconciliation;
use App\Repositories\BankReconciliationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankReconciliationRepositoryTest extends TestCase
{
    use MakeBankReconciliationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankReconciliationRepository
     */
    protected $bankReconciliationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->bankReconciliationRepo = App::make(BankReconciliationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBankReconciliation()
    {
        $bankReconciliation = $this->fakeBankReconciliationData();
        $createdBankReconciliation = $this->bankReconciliationRepo->create($bankReconciliation);
        $createdBankReconciliation = $createdBankReconciliation->toArray();
        $this->assertArrayHasKey('id', $createdBankReconciliation);
        $this->assertNotNull($createdBankReconciliation['id'], 'Created BankReconciliation must have id specified');
        $this->assertNotNull(BankReconciliation::find($createdBankReconciliation['id']), 'BankReconciliation with given id must be in DB');
        $this->assertModelData($bankReconciliation, $createdBankReconciliation);
    }

    /**
     * @test read
     */
    public function testReadBankReconciliation()
    {
        $bankReconciliation = $this->makeBankReconciliation();
        $dbBankReconciliation = $this->bankReconciliationRepo->find($bankReconciliation->id);
        $dbBankReconciliation = $dbBankReconciliation->toArray();
        $this->assertModelData($bankReconciliation->toArray(), $dbBankReconciliation);
    }

    /**
     * @test update
     */
    public function testUpdateBankReconciliation()
    {
        $bankReconciliation = $this->makeBankReconciliation();
        $fakeBankReconciliation = $this->fakeBankReconciliationData();
        $updatedBankReconciliation = $this->bankReconciliationRepo->update($fakeBankReconciliation, $bankReconciliation->id);
        $this->assertModelData($fakeBankReconciliation, $updatedBankReconciliation->toArray());
        $dbBankReconciliation = $this->bankReconciliationRepo->find($bankReconciliation->id);
        $this->assertModelData($fakeBankReconciliation, $dbBankReconciliation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBankReconciliation()
    {
        $bankReconciliation = $this->makeBankReconciliation();
        $resp = $this->bankReconciliationRepo->delete($bankReconciliation->id);
        $this->assertTrue($resp);
        $this->assertNull(BankReconciliation::find($bankReconciliation->id), 'BankReconciliation should not exist in DB');
    }
}
