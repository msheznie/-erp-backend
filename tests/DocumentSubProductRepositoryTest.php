<?php namespace Tests\Repositories;

use App\Models\DocumentSubProduct;
use App\Repositories\DocumentSubProductRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentSubProductRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentSubProductRepository
     */
    protected $documentSubProductRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentSubProductRepo = \App::make(DocumentSubProductRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->make()->toArray();

        $createdDocumentSubProduct = $this->documentSubProductRepo->create($documentSubProduct);

        $createdDocumentSubProduct = $createdDocumentSubProduct->toArray();
        $this->assertArrayHasKey('id', $createdDocumentSubProduct);
        $this->assertNotNull($createdDocumentSubProduct['id'], 'Created DocumentSubProduct must have id specified');
        $this->assertNotNull(DocumentSubProduct::find($createdDocumentSubProduct['id']), 'DocumentSubProduct with given id must be in DB');
        $this->assertModelData($documentSubProduct, $createdDocumentSubProduct);
    }

    /**
     * @test read
     */
    public function test_read_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->create();

        $dbDocumentSubProduct = $this->documentSubProductRepo->find($documentSubProduct->id);

        $dbDocumentSubProduct = $dbDocumentSubProduct->toArray();
        $this->assertModelData($documentSubProduct->toArray(), $dbDocumentSubProduct);
    }

    /**
     * @test update
     */
    public function test_update_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->create();
        $fakeDocumentSubProduct = factory(DocumentSubProduct::class)->make()->toArray();

        $updatedDocumentSubProduct = $this->documentSubProductRepo->update($fakeDocumentSubProduct, $documentSubProduct->id);

        $this->assertModelData($fakeDocumentSubProduct, $updatedDocumentSubProduct->toArray());
        $dbDocumentSubProduct = $this->documentSubProductRepo->find($documentSubProduct->id);
        $this->assertModelData($fakeDocumentSubProduct, $dbDocumentSubProduct->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->create();

        $resp = $this->documentSubProductRepo->delete($documentSubProduct->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentSubProduct::find($documentSubProduct->id), 'DocumentSubProduct should not exist in DB');
    }
}
