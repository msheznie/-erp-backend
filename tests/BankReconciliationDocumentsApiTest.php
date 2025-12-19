<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankReconciliationDocuments;

class BankReconciliationDocumentsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bank_reconciliation_documents', $bankReconciliationDocuments
        );

        $this->assertApiResponse($bankReconciliationDocuments);
    }

    /**
     * @test
     */
    public function test_read_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bank_reconciliation_documents/'.$bankReconciliationDocuments->id
        );

        $this->assertApiResponse($bankReconciliationDocuments->toArray());
    }

    /**
     * @test
     */
    public function test_update_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->create();
        $editedBankReconciliationDocuments = factory(BankReconciliationDocuments::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bank_reconciliation_documents/'.$bankReconciliationDocuments->id,
            $editedBankReconciliationDocuments
        );

        $this->assertApiResponse($editedBankReconciliationDocuments);
    }

    /**
     * @test
     */
    public function test_delete_bank_reconciliation_documents()
    {
        $bankReconciliationDocuments = factory(BankReconciliationDocuments::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bank_reconciliation_documents/'.$bankReconciliationDocuments->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bank_reconciliation_documents/'.$bankReconciliationDocuments->id
        );

        $this->response->assertStatus(404);
    }
}
