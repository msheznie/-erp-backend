<?php

use App\Models\CompanyDocumentAttachment;
use App\Repositories\CompanyDocumentAttachmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyDocumentAttachmentRepositoryTest extends TestCase
{
    use MakeCompanyDocumentAttachmentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyDocumentAttachmentRepository
     */
    protected $companyDocumentAttachmentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyDocumentAttachmentRepo = App::make(CompanyDocumentAttachmentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->fakeCompanyDocumentAttachmentData();
        $createdCompanyDocumentAttachment = $this->companyDocumentAttachmentRepo->create($companyDocumentAttachment);
        $createdCompanyDocumentAttachment = $createdCompanyDocumentAttachment->toArray();
        $this->assertArrayHasKey('id', $createdCompanyDocumentAttachment);
        $this->assertNotNull($createdCompanyDocumentAttachment['id'], 'Created CompanyDocumentAttachment must have id specified');
        $this->assertNotNull(CompanyDocumentAttachment::find($createdCompanyDocumentAttachment['id']), 'CompanyDocumentAttachment with given id must be in DB');
        $this->assertModelData($companyDocumentAttachment, $createdCompanyDocumentAttachment);
    }

    /**
     * @test read
     */
    public function testReadCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->makeCompanyDocumentAttachment();
        $dbCompanyDocumentAttachment = $this->companyDocumentAttachmentRepo->find($companyDocumentAttachment->id);
        $dbCompanyDocumentAttachment = $dbCompanyDocumentAttachment->toArray();
        $this->assertModelData($companyDocumentAttachment->toArray(), $dbCompanyDocumentAttachment);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->makeCompanyDocumentAttachment();
        $fakeCompanyDocumentAttachment = $this->fakeCompanyDocumentAttachmentData();
        $updatedCompanyDocumentAttachment = $this->companyDocumentAttachmentRepo->update($fakeCompanyDocumentAttachment, $companyDocumentAttachment->id);
        $this->assertModelData($fakeCompanyDocumentAttachment, $updatedCompanyDocumentAttachment->toArray());
        $dbCompanyDocumentAttachment = $this->companyDocumentAttachmentRepo->find($companyDocumentAttachment->id);
        $this->assertModelData($fakeCompanyDocumentAttachment, $dbCompanyDocumentAttachment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->makeCompanyDocumentAttachment();
        $resp = $this->companyDocumentAttachmentRepo->delete($companyDocumentAttachment->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyDocumentAttachment::find($companyDocumentAttachment->id), 'CompanyDocumentAttachment should not exist in DB');
    }
}
