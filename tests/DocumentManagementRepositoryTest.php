<?php namespace Tests\Repositories;

use App\Models\DocumentManagement;
use App\Repositories\DocumentManagementRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeDocumentManagementTrait;
use Tests\ApiTestTrait;

class DocumentManagementRepositoryTest extends TestCase
{
    use MakeDocumentManagementTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentManagementRepository
     */
    protected $documentManagementRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentManagementRepo = \App::make(DocumentManagementRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_management()
    {
        $documentManagement = $this->fakeDocumentManagementData();
        $createdDocumentManagement = $this->documentManagementRepo->create($documentManagement);
        $createdDocumentManagement = $createdDocumentManagement->toArray();
        $this->assertArrayHasKey('id', $createdDocumentManagement);
        $this->assertNotNull($createdDocumentManagement['id'], 'Created DocumentManagement must have id specified');
        $this->assertNotNull(DocumentManagement::find($createdDocumentManagement['id']), 'DocumentManagement with given id must be in DB');
        $this->assertModelData($documentManagement, $createdDocumentManagement);
    }

    /**
     * @test read
     */
    public function test_read_document_management()
    {
        $documentManagement = $this->makeDocumentManagement();
        $dbDocumentManagement = $this->documentManagementRepo->find($documentManagement->id);
        $dbDocumentManagement = $dbDocumentManagement->toArray();
        $this->assertModelData($documentManagement->toArray(), $dbDocumentManagement);
    }

    /**
     * @test update
     */
    public function test_update_document_management()
    {
        $documentManagement = $this->makeDocumentManagement();
        $fakeDocumentManagement = $this->fakeDocumentManagementData();
        $updatedDocumentManagement = $this->documentManagementRepo->update($fakeDocumentManagement, $documentManagement->id);
        $this->assertModelData($fakeDocumentManagement, $updatedDocumentManagement->toArray());
        $dbDocumentManagement = $this->documentManagementRepo->find($documentManagement->id);
        $this->assertModelData($fakeDocumentManagement, $dbDocumentManagement->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_management()
    {
        $documentManagement = $this->makeDocumentManagement();
        $resp = $this->documentManagementRepo->delete($documentManagement->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentManagement::find($documentManagement->id), 'DocumentManagement should not exist in DB');
    }
}
