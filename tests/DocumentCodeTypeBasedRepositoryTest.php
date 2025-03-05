<?php namespace Tests\Repositories;

use App\Models\DocumentCodeTypeBased;
use App\Repositories\DocumentCodeTypeBasedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodeTypeBasedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodeTypeBasedRepository
     */
    protected $documentCodeTypeBasedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodeTypeBasedRepo = \App::make(DocumentCodeTypeBasedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->make()->toArray();

        $createdDocumentCodeTypeBased = $this->documentCodeTypeBasedRepo->create($documentCodeTypeBased);

        $createdDocumentCodeTypeBased = $createdDocumentCodeTypeBased->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodeTypeBased);
        $this->assertNotNull($createdDocumentCodeTypeBased['id'], 'Created DocumentCodeTypeBased must have id specified');
        $this->assertNotNull(DocumentCodeTypeBased::find($createdDocumentCodeTypeBased['id']), 'DocumentCodeTypeBased with given id must be in DB');
        $this->assertModelData($documentCodeTypeBased, $createdDocumentCodeTypeBased);
    }

    /**
     * @test read
     */
    public function test_read_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->create();

        $dbDocumentCodeTypeBased = $this->documentCodeTypeBasedRepo->find($documentCodeTypeBased->id);

        $dbDocumentCodeTypeBased = $dbDocumentCodeTypeBased->toArray();
        $this->assertModelData($documentCodeTypeBased->toArray(), $dbDocumentCodeTypeBased);
    }

    /**
     * @test update
     */
    public function test_update_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->create();
        $fakeDocumentCodeTypeBased = factory(DocumentCodeTypeBased::class)->make()->toArray();

        $updatedDocumentCodeTypeBased = $this->documentCodeTypeBasedRepo->update($fakeDocumentCodeTypeBased, $documentCodeTypeBased->id);

        $this->assertModelData($fakeDocumentCodeTypeBased, $updatedDocumentCodeTypeBased->toArray());
        $dbDocumentCodeTypeBased = $this->documentCodeTypeBasedRepo->find($documentCodeTypeBased->id);
        $this->assertModelData($fakeDocumentCodeTypeBased, $dbDocumentCodeTypeBased->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->create();

        $resp = $this->documentCodeTypeBasedRepo->delete($documentCodeTypeBased->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodeTypeBased::find($documentCodeTypeBased->id), 'DocumentCodeTypeBased should not exist in DB');
    }
}
