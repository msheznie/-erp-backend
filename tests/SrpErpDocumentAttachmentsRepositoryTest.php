<?php namespace Tests\Repositories;

use App\Models\SrpErpDocumentAttachments;
use App\Repositories\SrpErpDocumentAttachmentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpErpDocumentAttachmentsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpErpDocumentAttachmentsRepository
     */
    protected $srpErpDocumentAttachmentsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpErpDocumentAttachmentsRepo = \App::make(SrpErpDocumentAttachmentsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->make()->toArray();

        $createdSrpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepo->create($srpErpDocumentAttachments);

        $createdSrpErpDocumentAttachments = $createdSrpErpDocumentAttachments->toArray();
        $this->assertArrayHasKey('id', $createdSrpErpDocumentAttachments);
        $this->assertNotNull($createdSrpErpDocumentAttachments['id'], 'Created SrpErpDocumentAttachments must have id specified');
        $this->assertNotNull(SrpErpDocumentAttachments::find($createdSrpErpDocumentAttachments['id']), 'SrpErpDocumentAttachments with given id must be in DB');
        $this->assertModelData($srpErpDocumentAttachments, $createdSrpErpDocumentAttachments);
    }

    /**
     * @test read
     */
    public function test_read_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->create();

        $dbSrpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepo->find($srpErpDocumentAttachments->id);

        $dbSrpErpDocumentAttachments = $dbSrpErpDocumentAttachments->toArray();
        $this->assertModelData($srpErpDocumentAttachments->toArray(), $dbSrpErpDocumentAttachments);
    }

    /**
     * @test update
     */
    public function test_update_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->create();
        $fakeSrpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->make()->toArray();

        $updatedSrpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepo->update($fakeSrpErpDocumentAttachments, $srpErpDocumentAttachments->id);

        $this->assertModelData($fakeSrpErpDocumentAttachments, $updatedSrpErpDocumentAttachments->toArray());
        $dbSrpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepo->find($srpErpDocumentAttachments->id);
        $this->assertModelData($fakeSrpErpDocumentAttachments, $dbSrpErpDocumentAttachments->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->create();

        $resp = $this->srpErpDocumentAttachmentsRepo->delete($srpErpDocumentAttachments->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpErpDocumentAttachments::find($srpErpDocumentAttachments->id), 'SrpErpDocumentAttachments should not exist in DB');
    }
}
