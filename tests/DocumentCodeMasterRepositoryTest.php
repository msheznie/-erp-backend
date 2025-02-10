<?php namespace Tests\Repositories;

use App\Models\DocumentCodeMaster;
use App\Repositories\DocumentCodeMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodeMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodeMasterRepository
     */
    protected $documentCodeMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodeMasterRepo = \App::make(DocumentCodeMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->make()->toArray();

        $createdDocumentCodeMaster = $this->documentCodeMasterRepo->create($documentCodeMaster);

        $createdDocumentCodeMaster = $createdDocumentCodeMaster->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodeMaster);
        $this->assertNotNull($createdDocumentCodeMaster['id'], 'Created DocumentCodeMaster must have id specified');
        $this->assertNotNull(DocumentCodeMaster::find($createdDocumentCodeMaster['id']), 'DocumentCodeMaster with given id must be in DB');
        $this->assertModelData($documentCodeMaster, $createdDocumentCodeMaster);
    }

    /**
     * @test read
     */
    public function test_read_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->create();

        $dbDocumentCodeMaster = $this->documentCodeMasterRepo->find($documentCodeMaster->id);

        $dbDocumentCodeMaster = $dbDocumentCodeMaster->toArray();
        $this->assertModelData($documentCodeMaster->toArray(), $dbDocumentCodeMaster);
    }

    /**
     * @test update
     */
    public function test_update_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->create();
        $fakeDocumentCodeMaster = factory(DocumentCodeMaster::class)->make()->toArray();

        $updatedDocumentCodeMaster = $this->documentCodeMasterRepo->update($fakeDocumentCodeMaster, $documentCodeMaster->id);

        $this->assertModelData($fakeDocumentCodeMaster, $updatedDocumentCodeMaster->toArray());
        $dbDocumentCodeMaster = $this->documentCodeMasterRepo->find($documentCodeMaster->id);
        $this->assertModelData($fakeDocumentCodeMaster, $dbDocumentCodeMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->create();

        $resp = $this->documentCodeMasterRepo->delete($documentCodeMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodeMaster::find($documentCodeMaster->id), 'DocumentCodeMaster should not exist in DB');
    }
}
