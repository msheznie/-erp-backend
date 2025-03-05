<?php namespace Tests\Repositories;

use App\Models\DocumentCodeFormat;
use App\Repositories\DocumentCodeFormatRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentCodeFormatRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentCodeFormatRepository
     */
    protected $documentCodeFormatRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentCodeFormatRepo = \App::make(DocumentCodeFormatRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->make()->toArray();

        $createdDocumentCodeFormat = $this->documentCodeFormatRepo->create($documentCodeFormat);

        $createdDocumentCodeFormat = $createdDocumentCodeFormat->toArray();
        $this->assertArrayHasKey('id', $createdDocumentCodeFormat);
        $this->assertNotNull($createdDocumentCodeFormat['id'], 'Created DocumentCodeFormat must have id specified');
        $this->assertNotNull(DocumentCodeFormat::find($createdDocumentCodeFormat['id']), 'DocumentCodeFormat with given id must be in DB');
        $this->assertModelData($documentCodeFormat, $createdDocumentCodeFormat);
    }

    /**
     * @test read
     */
    public function test_read_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->create();

        $dbDocumentCodeFormat = $this->documentCodeFormatRepo->find($documentCodeFormat->id);

        $dbDocumentCodeFormat = $dbDocumentCodeFormat->toArray();
        $this->assertModelData($documentCodeFormat->toArray(), $dbDocumentCodeFormat);
    }

    /**
     * @test update
     */
    public function test_update_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->create();
        $fakeDocumentCodeFormat = factory(DocumentCodeFormat::class)->make()->toArray();

        $updatedDocumentCodeFormat = $this->documentCodeFormatRepo->update($fakeDocumentCodeFormat, $documentCodeFormat->id);

        $this->assertModelData($fakeDocumentCodeFormat, $updatedDocumentCodeFormat->toArray());
        $dbDocumentCodeFormat = $this->documentCodeFormatRepo->find($documentCodeFormat->id);
        $this->assertModelData($fakeDocumentCodeFormat, $dbDocumentCodeFormat->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->create();

        $resp = $this->documentCodeFormatRepo->delete($documentCodeFormat->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentCodeFormat::find($documentCodeFormat->id), 'DocumentCodeFormat should not exist in DB');
    }
}
