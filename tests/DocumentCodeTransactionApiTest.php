<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentCodeTransaction;

class DocumentCodeTransactionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_code_transactions', $documentCodeTransaction
        );

        $this->assertApiResponse($documentCodeTransaction);
    }

    /**
     * @test
     */
    public function test_read_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_code_transactions/'.$documentCodeTransaction->id
        );

        $this->assertApiResponse($documentCodeTransaction->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->create();
        $editedDocumentCodeTransaction = factory(DocumentCodeTransaction::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_code_transactions/'.$documentCodeTransaction->id,
            $editedDocumentCodeTransaction
        );

        $this->assertApiResponse($editedDocumentCodeTransaction);
    }

    /**
     * @test
     */
    public function test_delete_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_code_transactions/'.$documentCodeTransaction->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_code_transactions/'.$documentCodeTransaction->id
        );

        $this->response->assertStatus(404);
    }
}
