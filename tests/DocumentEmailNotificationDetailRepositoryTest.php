<?php

use App\Models\DocumentEmailNotificationDetail;
use App\Repositories\DocumentEmailNotificationDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentEmailNotificationDetailRepositoryTest extends TestCase
{
    use MakeDocumentEmailNotificationDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentEmailNotificationDetailRepository
     */
    protected $documentEmailNotificationDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentEmailNotificationDetailRepo = App::make(DocumentEmailNotificationDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->fakeDocumentEmailNotificationDetailData();
        $createdDocumentEmailNotificationDetail = $this->documentEmailNotificationDetailRepo->create($documentEmailNotificationDetail);
        $createdDocumentEmailNotificationDetail = $createdDocumentEmailNotificationDetail->toArray();
        $this->assertArrayHasKey('id', $createdDocumentEmailNotificationDetail);
        $this->assertNotNull($createdDocumentEmailNotificationDetail['id'], 'Created DocumentEmailNotificationDetail must have id specified');
        $this->assertNotNull(DocumentEmailNotificationDetail::find($createdDocumentEmailNotificationDetail['id']), 'DocumentEmailNotificationDetail with given id must be in DB');
        $this->assertModelData($documentEmailNotificationDetail, $createdDocumentEmailNotificationDetail);
    }

    /**
     * @test read
     */
    public function testReadDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->makeDocumentEmailNotificationDetail();
        $dbDocumentEmailNotificationDetail = $this->documentEmailNotificationDetailRepo->find($documentEmailNotificationDetail->id);
        $dbDocumentEmailNotificationDetail = $dbDocumentEmailNotificationDetail->toArray();
        $this->assertModelData($documentEmailNotificationDetail->toArray(), $dbDocumentEmailNotificationDetail);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->makeDocumentEmailNotificationDetail();
        $fakeDocumentEmailNotificationDetail = $this->fakeDocumentEmailNotificationDetailData();
        $updatedDocumentEmailNotificationDetail = $this->documentEmailNotificationDetailRepo->update($fakeDocumentEmailNotificationDetail, $documentEmailNotificationDetail->id);
        $this->assertModelData($fakeDocumentEmailNotificationDetail, $updatedDocumentEmailNotificationDetail->toArray());
        $dbDocumentEmailNotificationDetail = $this->documentEmailNotificationDetailRepo->find($documentEmailNotificationDetail->id);
        $this->assertModelData($fakeDocumentEmailNotificationDetail, $dbDocumentEmailNotificationDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->makeDocumentEmailNotificationDetail();
        $resp = $this->documentEmailNotificationDetailRepo->delete($documentEmailNotificationDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentEmailNotificationDetail::find($documentEmailNotificationDetail->id), 'DocumentEmailNotificationDetail should not exist in DB');
    }
}
