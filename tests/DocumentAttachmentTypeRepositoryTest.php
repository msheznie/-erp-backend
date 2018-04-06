<?php

use App\Models\DocumentAttachmentType;
use App\Repositories\DocumentAttachmentTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentAttachmentTypeRepositoryTest extends TestCase
{
    use MakeDocumentAttachmentTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentAttachmentTypeRepository
     */
    protected $documentAttachmentTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentAttachmentTypeRepo = App::make(DocumentAttachmentTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentAttachmentType()
    {
        $documentAttachmentType = $this->fakeDocumentAttachmentTypeData();
        $createdDocumentAttachmentType = $this->documentAttachmentTypeRepo->create($documentAttachmentType);
        $createdDocumentAttachmentType = $createdDocumentAttachmentType->toArray();
        $this->assertArrayHasKey('id', $createdDocumentAttachmentType);
        $this->assertNotNull($createdDocumentAttachmentType['id'], 'Created DocumentAttachmentType must have id specified');
        $this->assertNotNull(DocumentAttachmentType::find($createdDocumentAttachmentType['id']), 'DocumentAttachmentType with given id must be in DB');
        $this->assertModelData($documentAttachmentType, $createdDocumentAttachmentType);
    }

    /**
     * @test read
     */
    public function testReadDocumentAttachmentType()
    {
        $documentAttachmentType = $this->makeDocumentAttachmentType();
        $dbDocumentAttachmentType = $this->documentAttachmentTypeRepo->find($documentAttachmentType->id);
        $dbDocumentAttachmentType = $dbDocumentAttachmentType->toArray();
        $this->assertModelData($documentAttachmentType->toArray(), $dbDocumentAttachmentType);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentAttachmentType()
    {
        $documentAttachmentType = $this->makeDocumentAttachmentType();
        $fakeDocumentAttachmentType = $this->fakeDocumentAttachmentTypeData();
        $updatedDocumentAttachmentType = $this->documentAttachmentTypeRepo->update($fakeDocumentAttachmentType, $documentAttachmentType->id);
        $this->assertModelData($fakeDocumentAttachmentType, $updatedDocumentAttachmentType->toArray());
        $dbDocumentAttachmentType = $this->documentAttachmentTypeRepo->find($documentAttachmentType->id);
        $this->assertModelData($fakeDocumentAttachmentType, $dbDocumentAttachmentType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentAttachmentType()
    {
        $documentAttachmentType = $this->makeDocumentAttachmentType();
        $resp = $this->documentAttachmentTypeRepo->delete($documentAttachmentType->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentAttachmentType::find($documentAttachmentType->id), 'DocumentAttachmentType should not exist in DB');
    }
}
