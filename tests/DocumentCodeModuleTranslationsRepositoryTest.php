<?php namespace Tests\Repositories;

use App\Models\DocumentCodeModuleTranslations;
use App\Repositories\DocumentCodeModuleTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodeModuleTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodeModuleTranslationsRepository
     */
    protected $documentCodeModuleTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodeModuleTranslationsRepo = \App::make(DocumentCodeModuleTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->make()->toArray();

        $createdDocumentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepo->create($documentCodeModuleTranslations);

        $createdDocumentCodeModuleTranslations = $createdDocumentCodeModuleTranslations->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodeModuleTranslations);
        $this->assertNotNull($createdDocumentCodeModuleTranslations['id'], 'Created DocumentCodeModuleTranslations must have id specified');
        $this->assertNotNull(DocumentCodeModuleTranslations::find($createdDocumentCodeModuleTranslations['id']), 'DocumentCodeModuleTranslations with given id must be in DB');
        $this->assertModelData($documentCodeModuleTranslations, $createdDocumentCodeModuleTranslations);
    }

    /**
     * @test read
     */
    public function test_read_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->create();

        $dbDocumentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepo->find($documentCodeModuleTranslations->id);

        $dbDocumentCodeModuleTranslations = $dbDocumentCodeModuleTranslations->toArray();
        $this->assertModelData($documentCodeModuleTranslations->toArray(), $dbDocumentCodeModuleTranslations);
    }

    /**
     * @test update
     */
    public function test_update_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->create();
        $fakeDocumentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->make()->toArray();

        $updatedDocumentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepo->update($fakeDocumentCodeModuleTranslations, $documentCodeModuleTranslations->id);

        $this->assertModelData($fakeDocumentCodeModuleTranslations, $updatedDocumentCodeModuleTranslations->toArray());
        $dbDocumentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepo->find($documentCodeModuleTranslations->id);
        $this->assertModelData($fakeDocumentCodeModuleTranslations, $dbDocumentCodeModuleTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->create();

        $resp = $this->documentCodeModuleTranslationsRepo->delete($documentCodeModuleTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodeModuleTranslations::find($documentCodeModuleTranslations->id), 'DocumentCodeModuleTranslations should not exist in DB');
    }
}
