<?php namespace Tests\Repositories;

use App\Models\DocumentCodeModule;
use App\Repositories\DocumentCodeModuleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodeModuleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodeModuleRepository
     */
    protected $documentCodeModuleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodeModuleRepo = \App::make(DocumentCodeModuleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_module()
    {
        $documentCodeModule = factory(DocumentCodeModule::class)->make()->toArray();

        $createdDocumentCodeModule = $this->documentCodeModuleRepo->create($documentCodeModule);

        $createdDocumentCodeModule = $createdDocumentCodeModule->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodeModule);
        $this->assertNotNull($createdDocumentCodeModule['id'], 'Created DocumentCodeModule must have id specified');
        $this->assertNotNull(DocumentCodeModule::find($createdDocumentCodeModule['id']), 'DocumentCodeModule with given id must be in DB');
        $this->assertModelData($documentCodeModule, $createdDocumentCodeModule);
    }

    /**
     * @test read
     */
    public function test_read_document_code_module()
    {
        $documentCodeModule = factory(DocumentCodeModule::class)->create();

        $dbDocumentCodeModule = $this->documentCodeModuleRepo->find($documentCodeModule->id);

        $dbDocumentCodeModule = $dbDocumentCodeModule->toArray();
        $this->assertModelData($documentCodeModule->toArray(), $dbDocumentCodeModule);
    }

    /**
     * @test update
     */
    public function test_update_document_code_module()
    {
        $documentCodeModule = factory(DocumentCodeModule::class)->create();
        $fakeDocumentCodeModule = factory(DocumentCodeModule::class)->make()->toArray();

        $updatedDocumentCodeModule = $this->documentCodeModuleRepo->update($fakeDocumentCodeModule, $documentCodeModule->id);

        $this->assertModelData($fakeDocumentCodeModule, $updatedDocumentCodeModule->toArray());
        $dbDocumentCodeModule = $this->documentCodeModuleRepo->find($documentCodeModule->id);
        $this->assertModelData($fakeDocumentCodeModule, $dbDocumentCodeModule->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_module()
    {
        $documentCodeModule = factory(DocumentCodeModule::class)->create();

        $resp = $this->documentCodeModuleRepo->delete($documentCodeModule->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodeModule::find($documentCodeModule->id), 'DocumentCodeModule should not exist in DB');
    }
}
