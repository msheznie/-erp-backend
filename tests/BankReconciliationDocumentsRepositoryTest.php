<?php namespace Tests\Repositories;

use App\Models\BankReconciliationDocuments;
use App\Repositories\BankReconciliationDocumentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BankReconciliationDocumentsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankReconciliationDocumentsRepository
     */
    protected $bankReconciliationDocumentsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bankReconciliationDocumentsRepo = \App::make(BankReconciliationDocumentsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->make()->toArray();

        $createdBankReconciliationDocuments = $this->bankReconciliationDocumentsRepo->create($bankReconciliationDocuments);

        $createdBankReconciliationDocuments = $createdBankReconciliationDocuments->toArray();
        $this->assertArrayHasKey('id', $createdBankReconciliationDocuments);
        $this->assertNotNull($createdBankReconciliationDocuments['id'], 'Created BankReconciliationDocuments must have id specified');
        $this->assertNotNull(BankReconciliationDocuments::find($createdBankReconciliationDocuments['id']), 'BankReconciliationDocuments with given id must be in DB');
        $this->assertModelData($bankReconciliationDocuments, $createdBankReconciliationDocuments);
    }

    /**
     * @test read
     */
    public function test_read_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->create();

        $dbBankReconciliationDocuments = $this->bankReconciliationDocumentsRepo->find($bankReconciliationDocuments->id);

        $dbBankReconciliationDocuments = $dbBankReconciliationDocuments->toArray();
        $this->assertModelData($bankReconciliationDocuments->toArray(), $dbBankReconciliationDocuments);
    }

    /**
     * @test update
     */
    public function test_update_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->create();
        $fakeBankReconciliationDocuments = factory(BankReconciliationDocuments::class)->make()->toArray();

        $updatedBankReconciliationDocuments = $this->bankReconciliationDocumentsRepo->update($fakeBankReconciliationDocuments, $bankReconciliationDocuments->id);

        $this->assertModelData($fakeBankReconciliationDocuments, $updatedBankReconciliationDocuments->toArray());
        $dbBankReconciliationDocuments = $this->bankReconciliationDocumentsRepo->find($bankReconciliationDocuments->id);
        $this->assertModelData($fakeBankReconciliationDocuments, $dbBankReconciliationDocuments->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->create();

        $resp = $this->bankReconciliationDocumentsRepo->delete($bankReconciliationDocuments->id);

        $this->assertTrue($resp);
        $this->assertNull(BankReconciliationDocuments::find($bankReconciliationDocuments->id), 'BankReconciliationDocuments should not exist in DB');
    }
}
