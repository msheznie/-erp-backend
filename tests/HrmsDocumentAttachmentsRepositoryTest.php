<?php namespace Tests\Repositories;

use App\Models\HrmsDocumentAttachments;
use App\Repositories\HrmsDocumentAttachmentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHrmsDocumentAttachmentsTrait;
use Tests\ApiTestTrait;

class HrmsDocumentAttachmentsRepositoryTest extends TestCase
{
    use MakeHrmsDocumentAttachmentsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrmsDocumentAttachmentsRepository
     */
    protected $hrmsDocumentAttachmentsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrmsDocumentAttachmentsRepo = \App::make(HrmsDocumentAttachmentsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->fakeHrmsDocumentAttachmentsData();
        $createdHrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepo->create($hrmsDocumentAttachments);
        $createdHrmsDocumentAttachments = $createdHrmsDocumentAttachments->toArray();
        $this->assertArrayHasKey('id', $createdHrmsDocumentAttachments);
        $this->assertNotNull($createdHrmsDocumentAttachments['id'], 'Created HrmsDocumentAttachments must have id specified');
        $this->assertNotNull(HrmsDocumentAttachments::find($createdHrmsDocumentAttachments['id']), 'HrmsDocumentAttachments with given id must be in DB');
        $this->assertModelData($hrmsDocumentAttachments, $createdHrmsDocumentAttachments);
    }

    /**
     * @test read
     */
    public function test_read_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->makeHrmsDocumentAttachments();
        $dbHrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepo->find($hrmsDocumentAttachments->id);
        $dbHrmsDocumentAttachments = $dbHrmsDocumentAttachments->toArray();
        $this->assertModelData($hrmsDocumentAttachments->toArray(), $dbHrmsDocumentAttachments);
    }

    /**
     * @test update
     */
    public function test_update_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->makeHrmsDocumentAttachments();
        $fakeHrmsDocumentAttachments = $this->fakeHrmsDocumentAttachmentsData();
        $updatedHrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepo->update($fakeHrmsDocumentAttachments, $hrmsDocumentAttachments->id);
        $this->assertModelData($fakeHrmsDocumentAttachments, $updatedHrmsDocumentAttachments->toArray());
        $dbHrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepo->find($hrmsDocumentAttachments->id);
        $this->assertModelData($fakeHrmsDocumentAttachments, $dbHrmsDocumentAttachments->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->makeHrmsDocumentAttachments();
        $resp = $this->hrmsDocumentAttachmentsRepo->delete($hrmsDocumentAttachments->id);
        $this->assertTrue($resp);
        $this->assertNull(HrmsDocumentAttachments::find($hrmsDocumentAttachments->id), 'HrmsDocumentAttachments should not exist in DB');
    }
}
