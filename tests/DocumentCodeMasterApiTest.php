<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentCodeMaster;

class DocumentCodeMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_code_masters', $documentCodeMaster
        );

        $this->assertApiResponse($documentCodeMaster);
    }

    /**
     * @test
     */
    public function test_read_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_code_masters/'.$documentCodeMaster->id
        );

        $this->assertApiResponse($documentCodeMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->create();
        $editedDocumentCodeMaster = factory(DocumentCodeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_code_masters/'.$documentCodeMaster->id,
            $editedDocumentCodeMaster
        );

        $this->assertApiResponse($editedDocumentCodeMaster);
    }

    /**
     * @test
     */
    public function test_delete_document_code_master()
    {
        $documentCodeMaster = factory(DocumentCodeMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_code_masters/'.$documentCodeMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_code_masters/'.$documentCodeMaster->id
        );

        $this->response->assertStatus(404);
    }
}
