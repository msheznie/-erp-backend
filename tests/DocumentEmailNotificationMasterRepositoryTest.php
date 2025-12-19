<?php

use App\Models\DocumentEmailNotificationMaster;
use App\Repositories\DocumentEmailNotificationMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentEmailNotificationMasterRepositoryTest extends TestCase
{
    use MakeDocumentEmailNotificationMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentEmailNotificationMasterRepository
     */
    protected $documentEmailNotificationMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentEmailNotificationMasterRepo = App::make(DocumentEmailNotificationMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->fakeDocumentEmailNotificationMasterData();
        $createdDocumentEmailNotificationMaster = $this->documentEmailNotificationMasterRepo->create($documentEmailNotificationMaster);
        $createdDocumentEmailNotificationMaster = $createdDocumentEmailNotificationMaster->toArray();
        $this->assertArrayHasKey('id', $createdDocumentEmailNotificationMaster);
        $this->assertNotNull($createdDocumentEmailNotificationMaster['id'], 'Created DocumentEmailNotificationMaster must have id specified');
        $this->assertNotNull(DocumentEmailNotificationMaster::find($createdDocumentEmailNotificationMaster['id']), 'DocumentEmailNotificationMaster with given id must be in DB');
        $this->assertModelData($documentEmailNotificationMaster, $createdDocumentEmailNotificationMaster);
    }

    /**
     * @test read
     */
    public function testReadDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->makeDocumentEmailNotificationMaster();
        $dbDocumentEmailNotificationMaster = $this->documentEmailNotificationMasterRepo->find($documentEmailNotificationMaster->id);
        $dbDocumentEmailNotificationMaster = $dbDocumentEmailNotificationMaster->toArray();
        $this->assertModelData($documentEmailNotificationMaster->toArray(), $dbDocumentEmailNotificationMaster);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->makeDocumentEmailNotificationMaster();
        $fakeDocumentEmailNotificationMaster = $this->fakeDocumentEmailNotificationMasterData();
        $updatedDocumentEmailNotificationMaster = $this->documentEmailNotificationMasterRepo->update($fakeDocumentEmailNotificationMaster, $documentEmailNotificationMaster->id);
        $this->assertModelData($fakeDocumentEmailNotificationMaster, $updatedDocumentEmailNotificationMaster->toArray());
        $dbDocumentEmailNotificationMaster = $this->documentEmailNotificationMasterRepo->find($documentEmailNotificationMaster->id);
        $this->assertModelData($fakeDocumentEmailNotificationMaster, $dbDocumentEmailNotificationMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->makeDocumentEmailNotificationMaster();
        $resp = $this->documentEmailNotificationMasterRepo->delete($documentEmailNotificationMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentEmailNotificationMaster::find($documentEmailNotificationMaster->id), 'DocumentEmailNotificationMaster should not exist in DB');
    }
}
