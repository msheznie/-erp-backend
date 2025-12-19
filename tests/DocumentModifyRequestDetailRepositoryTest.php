<?php namespace Tests\Repositories;

use App\Models\DocumentModifyRequestDetail;
use App\Repositories\DocumentModifyRequestDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentModifyRequestDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentModifyRequestDetailRepository
     */
    protected $documentModifyRequestDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentModifyRequestDetailRepo = \App::make(DocumentModifyRequestDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->make()->toArray();

        $createdDocumentModifyRequestDetail = $this->documentModifyRequestDetailRepo->create($documentModifyRequestDetail);

        $createdDocumentModifyRequestDetail = $createdDocumentModifyRequestDetail->toArray();
        $this->assertArrayHasKey('id', $createdDocumentModifyRequestDetail);
        $this->assertNotNull($createdDocumentModifyRequestDetail['id'], 'Created DocumentModifyRequestDetail must have id specified');
        $this->assertNotNull(DocumentModifyRequestDetail::find($createdDocumentModifyRequestDetail['id']), 'DocumentModifyRequestDetail with given id must be in DB');
        $this->assertModelData($documentModifyRequestDetail, $createdDocumentModifyRequestDetail);
    }

    /**
     * @test read
     */
    public function test_read_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->create();

        $dbDocumentModifyRequestDetail = $this->documentModifyRequestDetailRepo->find($documentModifyRequestDetail->id);

        $dbDocumentModifyRequestDetail = $dbDocumentModifyRequestDetail->toArray();
        $this->assertModelData($documentModifyRequestDetail->toArray(), $dbDocumentModifyRequestDetail);
    }

    /**
     * @test update
     */
    public function test_update_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->create();
        $fakeDocumentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->make()->toArray();

        $updatedDocumentModifyRequestDetail = $this->documentModifyRequestDetailRepo->update($fakeDocumentModifyRequestDetail, $documentModifyRequestDetail->id);

        $this->assertModelData($fakeDocumentModifyRequestDetail, $updatedDocumentModifyRequestDetail->toArray());
        $dbDocumentModifyRequestDetail = $this->documentModifyRequestDetailRepo->find($documentModifyRequestDetail->id);
        $this->assertModelData($fakeDocumentModifyRequestDetail, $dbDocumentModifyRequestDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->create();

        $resp = $this->documentModifyRequestDetailRepo->delete($documentModifyRequestDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentModifyRequestDetail::find($documentModifyRequestDetail->id), 'DocumentModifyRequestDetail should not exist in DB');
    }
}
