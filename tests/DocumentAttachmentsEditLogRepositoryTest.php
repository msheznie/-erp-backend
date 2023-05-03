<?php namespace Tests\Repositories;

use App\Models\DocumentAttachmentsEditLog;
use App\Repositories\DocumentAttachmentsEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentAttachmentsEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentAttachmentsEditLogRepository
     */
    protected $documentAttachmentsEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentAttachmentsEditLogRepo = \App::make(DocumentAttachmentsEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->make()->toArray();

        $createdDocumentAttachmentsEditLog = $this->documentAttachmentsEditLogRepo->create($documentAttachmentsEditLog);

        $createdDocumentAttachmentsEditLog = $createdDocumentAttachmentsEditLog->toArray();
        $this->assertArrayHasKey('id', $createdDocumentAttachmentsEditLog);
        $this->assertNotNull($createdDocumentAttachmentsEditLog['id'], 'Created DocumentAttachmentsEditLog must have id specified');
        $this->assertNotNull(DocumentAttachmentsEditLog::find($createdDocumentAttachmentsEditLog['id']), 'DocumentAttachmentsEditLog with given id must be in DB');
        $this->assertModelData($documentAttachmentsEditLog, $createdDocumentAttachmentsEditLog);
    }

    /**
     * @test read
     */
    public function test_read_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->create();

        $dbDocumentAttachmentsEditLog = $this->documentAttachmentsEditLogRepo->find($documentAttachmentsEditLog->id);

        $dbDocumentAttachmentsEditLog = $dbDocumentAttachmentsEditLog->toArray();
        $this->assertModelData($documentAttachmentsEditLog->toArray(), $dbDocumentAttachmentsEditLog);
    }

    /**
     * @test update
     */
    public function test_update_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->create();
        $fakeDocumentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->make()->toArray();

        $updatedDocumentAttachmentsEditLog = $this->documentAttachmentsEditLogRepo->update($fakeDocumentAttachmentsEditLog, $documentAttachmentsEditLog->id);

        $this->assertModelData($fakeDocumentAttachmentsEditLog, $updatedDocumentAttachmentsEditLog->toArray());
        $dbDocumentAttachmentsEditLog = $this->documentAttachmentsEditLogRepo->find($documentAttachmentsEditLog->id);
        $this->assertModelData($fakeDocumentAttachmentsEditLog, $dbDocumentAttachmentsEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->create();

        $resp = $this->documentAttachmentsEditLogRepo->delete($documentAttachmentsEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentAttachmentsEditLog::find($documentAttachmentsEditLog->id), 'DocumentAttachmentsEditLog should not exist in DB');
    }
}
