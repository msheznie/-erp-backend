<?php namespace Tests\Repositories;

use App\Models\DocumentAttachments;
use App\Repositories\DocumentAttachmentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentAttachmentsRepositoryTest extends TestCase
{
    use MakeDocumentAttachmentsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentAttachmentsRepository
     */
    protected $documentAttachmentsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentAttachmentsRepo = App::make(DocumentAttachmentsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentAttachments()
    {
        $documentAttachments = $this->fakeDocumentAttachmentsData();
        $createdDocumentAttachments = $this->documentAttachmentsRepo->create($documentAttachments);
        $createdDocumentAttachments = $createdDocumentAttachments->toArray();
        $this->assertArrayHasKey('id', $createdDocumentAttachments);
        $this->assertNotNull($createdDocumentAttachments['id'], 'Created DocumentAttachments must have id specified');
        $this->assertNotNull(DocumentAttachments::find($createdDocumentAttachments['id']), 'DocumentAttachments with given id must be in DB');
        $this->assertModelData($documentAttachments, $createdDocumentAttachments);
    }

    /**
     * @test read
     */
    public function testReadDocumentAttachments()
    {
        $documentAttachments = $this->makeDocumentAttachments();
        $dbDocumentAttachments = $this->documentAttachmentsRepo->find($documentAttachments->id);
        $dbDocumentAttachments = $dbDocumentAttachments->toArray();
        $this->assertModelData($documentAttachments->toArray(), $dbDocumentAttachments);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentAttachments()
    {
        $documentAttachments = $this->makeDocumentAttachments();
        $fakeDocumentAttachments = $this->fakeDocumentAttachmentsData();
        $updatedDocumentAttachments = $this->documentAttachmentsRepo->update($fakeDocumentAttachments, $documentAttachments->id);
        $this->assertModelData($fakeDocumentAttachments, $updatedDocumentAttachments->toArray());
        $dbDocumentAttachments = $this->documentAttachmentsRepo->find($documentAttachments->id);
        $this->assertModelData($fakeDocumentAttachments, $dbDocumentAttachments->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentAttachments()
    {
        $documentAttachments = $this->makeDocumentAttachments();
        $resp = $this->documentAttachmentsRepo->delete($documentAttachments->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentAttachments::find($documentAttachments->id), 'DocumentAttachments should not exist in DB');
    }
}
