<?php

use App\Models\DocumentMaster;
use App\Repositories\DocumentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentMasterRepositoryTest extends TestCase
{
    use MakeDocumentMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentMasterRepository
     */
    protected $documentMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentMasterRepo = App::make(DocumentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentMaster()
    {
        $documentMaster = $this->fakeDocumentMasterData();
        $createdDocumentMaster = $this->documentMasterRepo->create($documentMaster);
        $createdDocumentMaster = $createdDocumentMaster->toArray();
        $this->assertArrayHasKey('id', $createdDocumentMaster);
        $this->assertNotNull($createdDocumentMaster['id'], 'Created DocumentMaster must have id specified');
        $this->assertNotNull(DocumentMaster::find($createdDocumentMaster['id']), 'DocumentMaster with given id must be in DB');
        $this->assertModelData($documentMaster, $createdDocumentMaster);
    }

    /**
     * @test read
     */
    public function testReadDocumentMaster()
    {
        $documentMaster = $this->makeDocumentMaster();
        $dbDocumentMaster = $this->documentMasterRepo->find($documentMaster->id);
        $dbDocumentMaster = $dbDocumentMaster->toArray();
        $this->assertModelData($documentMaster->toArray(), $dbDocumentMaster);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentMaster()
    {
        $documentMaster = $this->makeDocumentMaster();
        $fakeDocumentMaster = $this->fakeDocumentMasterData();
        $updatedDocumentMaster = $this->documentMasterRepo->update($fakeDocumentMaster, $documentMaster->id);
        $this->assertModelData($fakeDocumentMaster, $updatedDocumentMaster->toArray());
        $dbDocumentMaster = $this->documentMasterRepo->find($documentMaster->id);
        $this->assertModelData($fakeDocumentMaster, $dbDocumentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentMaster()
    {
        $documentMaster = $this->makeDocumentMaster();
        $resp = $this->documentMasterRepo->delete($documentMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentMaster::find($documentMaster->id), 'DocumentMaster should not exist in DB');
    }
}
