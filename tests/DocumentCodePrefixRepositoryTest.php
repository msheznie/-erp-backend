<?php namespace Tests\Repositories;

use App\Models\DocumentCodePrefix;
use App\Repositories\DocumentCodePrefixRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodePrefixRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodePrefixRepository
     */
    protected $documentCodePrefixRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodePrefixRepo = \App::make(DocumentCodePrefixRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->make()->toArray();

        $createdDocumentCodePrefix = $this->documentCodePrefixRepo->create($documentCodePrefix);

        $createdDocumentCodePrefix = $createdDocumentCodePrefix->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodePrefix);
        $this->assertNotNull($createdDocumentCodePrefix['id'], 'Created DocumentCodePrefix must have id specified');
        $this->assertNotNull(DocumentCodePrefix::find($createdDocumentCodePrefix['id']), 'DocumentCodePrefix with given id must be in DB');
        $this->assertModelData($documentCodePrefix, $createdDocumentCodePrefix);
    }

    /**
     * @test read
     */
    public function test_read_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->create();

        $dbDocumentCodePrefix = $this->documentCodePrefixRepo->find($documentCodePrefix->id);

        $dbDocumentCodePrefix = $dbDocumentCodePrefix->toArray();
        $this->assertModelData($documentCodePrefix->toArray(), $dbDocumentCodePrefix);
    }

    /**
     * @test update
     */
    public function test_update_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->create();
        $fakeDocumentCodePrefix = factory(DocumentCodePrefix::class)->make()->toArray();

        $updatedDocumentCodePrefix = $this->documentCodePrefixRepo->update($fakeDocumentCodePrefix, $documentCodePrefix->id);

        $this->assertModelData($fakeDocumentCodePrefix, $updatedDocumentCodePrefix->toArray());
        $dbDocumentCodePrefix = $this->documentCodePrefixRepo->find($documentCodePrefix->id);
        $this->assertModelData($fakeDocumentCodePrefix, $dbDocumentCodePrefix->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->create();

        $resp = $this->documentCodePrefixRepo->delete($documentCodePrefix->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodePrefix::find($documentCodePrefix->id), 'DocumentCodePrefix should not exist in DB');
    }
}
