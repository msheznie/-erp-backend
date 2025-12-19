<?php namespace Tests\Repositories;

use App\Models\DocumentModifyRequest;
use App\Repositories\DocumentModifyRequestRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentModifyRequestRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentModifyRequestRepository
     */
    protected $documentModifyRequestRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentModifyRequestRepo = \App::make(DocumentModifyRequestRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->make()->toArray();

        $createdDocumentModifyRequest = $this->documentModifyRequestRepo->create($documentModifyRequest);

        $createdDocumentModifyRequest = $createdDocumentModifyRequest->toArray();
        $this->assertArrayHasKey('id', $createdDocumentModifyRequest);
        $this->assertNotNull($createdDocumentModifyRequest['id'], 'Created DocumentModifyRequest must have id specified');
        $this->assertNotNull(DocumentModifyRequest::find($createdDocumentModifyRequest['id']), 'DocumentModifyRequest with given id must be in DB');
        $this->assertModelData($documentModifyRequest, $createdDocumentModifyRequest);
    }

    /**
     * @test read
     */
    public function test_read_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->create();

        $dbDocumentModifyRequest = $this->documentModifyRequestRepo->find($documentModifyRequest->id);

        $dbDocumentModifyRequest = $dbDocumentModifyRequest->toArray();
        $this->assertModelData($documentModifyRequest->toArray(), $dbDocumentModifyRequest);
    }

    /**
     * @test update
     */
    public function test_update_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->create();
        $fakeDocumentModifyRequest = factory(DocumentModifyRequest::class)->make()->toArray();

        $updatedDocumentModifyRequest = $this->documentModifyRequestRepo->update($fakeDocumentModifyRequest, $documentModifyRequest->id);

        $this->assertModelData($fakeDocumentModifyRequest, $updatedDocumentModifyRequest->toArray());
        $dbDocumentModifyRequest = $this->documentModifyRequestRepo->find($documentModifyRequest->id);
        $this->assertModelData($fakeDocumentModifyRequest, $dbDocumentModifyRequest->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->create();

        $resp = $this->documentModifyRequestRepo->delete($documentModifyRequest->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentModifyRequest::find($documentModifyRequest->id), 'DocumentModifyRequest should not exist in DB');
    }
}
