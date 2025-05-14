<?php namespace Tests\Repositories;

use App\Models\BankReconciliationRules;
use App\Repositories\BankReconciliationRulesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankReconciliationRulesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankReconciliationRulesRepository
     */
    protected $bankReconciliationRulesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankReconciliationRulesRepo = \App::make(BankReconciliationRulesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->make()->toArray();

        $createdBankReconciliationRules = $this->bankReconciliationRulesRepo->create($bankReconciliationRules);

        $createdBankReconciliationRules = $createdBankReconciliationRules->toArray();
        $this->assertArrayHasKey('id', $createdBankReconciliationRules);
        $this->assertNotNull($createdBankReconciliationRules['id'], 'Created BankReconciliationRules must have id specified');
        $this->assertNotNull(BankReconciliationRules::find($createdBankReconciliationRules['id']), 'BankReconciliationRules with given id must be in DB');
        $this->assertModelData($bankReconciliationRules, $createdBankReconciliationRules);
    }

    /**
     * @test read
     */
    public function test_read_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->create();

        $dbBankReconciliationRules = $this->bankReconciliationRulesRepo->find($bankReconciliationRules->id);

        $dbBankReconciliationRules = $dbBankReconciliationRules->toArray();
        $this->assertModelData($bankReconciliationRules->toArray(), $dbBankReconciliationRules);
    }

    /**
     * @test update
     */
    public function test_update_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->create();
        $fakeBankReconciliationRules = factory(BankReconciliationRules::class)->make()->toArray();

        $updatedBankReconciliationRules = $this->bankReconciliationRulesRepo->update($fakeBankReconciliationRules, $bankReconciliationRules->id);

        $this->assertModelData($fakeBankReconciliationRules, $updatedBankReconciliationRules->toArray());
        $dbBankReconciliationRules = $this->bankReconciliationRulesRepo->find($bankReconciliationRules->id);
        $this->assertModelData($fakeBankReconciliationRules, $dbBankReconciliationRules->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->create();

        $resp = $this->bankReconciliationRulesRepo->delete($bankReconciliationRules->id);

        $this->assertTrue($resp);
        $this->assertNull(BankReconciliationRules::find($bankReconciliationRules->id), 'BankReconciliationRules should not exist in DB');
    }
}
