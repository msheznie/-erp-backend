<?php namespace Tests\Repositories;

use App\Models\BankReconciliationTemplateMapping;
use App\Repositories\BankReconciliationTemplateMappingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankReconciliationTemplateMappingRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankReconciliationTemplateMappingRepository
     */
    protected $bankReconciliationTemplateMappingRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankReconciliationTemplateMappingRepo = \App::make(BankReconciliationTemplateMappingRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->make()->toArray();

        $createdBankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepo->create($bankReconciliationTemplateMapping);

        $createdBankReconciliationTemplateMapping = $createdBankReconciliationTemplateMapping->toArray();
        $this->assertArrayHasKey('id', $createdBankReconciliationTemplateMapping);
        $this->assertNotNull($createdBankReconciliationTemplateMapping['id'], 'Created BankReconciliationTemplateMapping must have id specified');
        $this->assertNotNull(BankReconciliationTemplateMapping::find($createdBankReconciliationTemplateMapping['id']), 'BankReconciliationTemplateMapping with given id must be in DB');
        $this->assertModelData($bankReconciliationTemplateMapping, $createdBankReconciliationTemplateMapping);
    }

    /**
     * @test read
     */
    public function test_read_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->create();

        $dbBankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepo->find($bankReconciliationTemplateMapping->id);

        $dbBankReconciliationTemplateMapping = $dbBankReconciliationTemplateMapping->toArray();
        $this->assertModelData($bankReconciliationTemplateMapping->toArray(), $dbBankReconciliationTemplateMapping);
    }

    /**
     * @test update
     */
    public function test_update_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->create();
        $fakeBankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->make()->toArray();

        $updatedBankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepo->update($fakeBankReconciliationTemplateMapping, $bankReconciliationTemplateMapping->id);

        $this->assertModelData($fakeBankReconciliationTemplateMapping, $updatedBankReconciliationTemplateMapping->toArray());
        $dbBankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepo->find($bankReconciliationTemplateMapping->id);
        $this->assertModelData($fakeBankReconciliationTemplateMapping, $dbBankReconciliationTemplateMapping->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->create();

        $resp = $this->bankReconciliationTemplateMappingRepo->delete($bankReconciliationTemplateMapping->id);

        $this->assertTrue($resp);
        $this->assertNull(BankReconciliationTemplateMapping::find($bankReconciliationTemplateMapping->id), 'BankReconciliationTemplateMapping should not exist in DB');
    }
}
