<?php namespace Tests\Repositories;

use App\Models\DocumentCodeTransaction;
use App\Repositories\DocumentCodeTransactionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodeTransactionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodeTransactionRepository
     */
    protected $documentCodeTransactionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodeTransactionRepo = \App::make(DocumentCodeTransactionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->make()->toArray();

        $createdDocumentCodeTransaction = $this->documentCodeTransactionRepo->create($documentCodeTransaction);

        $createdDocumentCodeTransaction = $createdDocumentCodeTransaction->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodeTransaction);
        $this->assertNotNull($createdDocumentCodeTransaction['id'], 'Created DocumentCodeTransaction must have id specified');
        $this->assertNotNull(DocumentCodeTransaction::find($createdDocumentCodeTransaction['id']), 'DocumentCodeTransaction with given id must be in DB');
        $this->assertModelData($documentCodeTransaction, $createdDocumentCodeTransaction);
    }

    /**
     * @test read
     */
    public function test_read_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->create();

        $dbDocumentCodeTransaction = $this->documentCodeTransactionRepo->find($documentCodeTransaction->id);

        $dbDocumentCodeTransaction = $dbDocumentCodeTransaction->toArray();
        $this->assertModelData($documentCodeTransaction->toArray(), $dbDocumentCodeTransaction);
    }

    /**
     * @test update
     */
    public function test_update_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->create();
        $fakeDocumentCodeTransaction = factory(DocumentCodeTransaction::class)->make()->toArray();

        $updatedDocumentCodeTransaction = $this->documentCodeTransactionRepo->update($fakeDocumentCodeTransaction, $documentCodeTransaction->id);

        $this->assertModelData($fakeDocumentCodeTransaction, $updatedDocumentCodeTransaction->toArray());
        $dbDocumentCodeTransaction = $this->documentCodeTransactionRepo->find($documentCodeTransaction->id);
        $this->assertModelData($fakeDocumentCodeTransaction, $dbDocumentCodeTransaction->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_transaction()
    {
        $documentCodeTransaction = factory(DocumentCodeTransaction::class)->create();

        $resp = $this->documentCodeTransactionRepo->delete($documentCodeTransaction->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodeTransaction::find($documentCodeTransaction->id), 'DocumentCodeTransaction should not exist in DB');
    }
}
