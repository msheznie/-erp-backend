<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentCodeModuleTranslations;

class DocumentCodeModuleTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_code_module_translations', $documentCodeModuleTranslations
        );

        $this->assertApiResponse($documentCodeModuleTranslations);
    }

    /**
     * @test
     */
    public function test_read_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_code_module_translations/'.$documentCodeModuleTranslations->id
        );

        $this->assertApiResponse($documentCodeModuleTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->create();
        $editedDocumentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_code_module_translations/'.$documentCodeModuleTranslations->id,
            $editedDocumentCodeModuleTranslations
        );

        $this->assertApiResponse($editedDocumentCodeModuleTranslations);
    }

    /**
     * @test
     */
    public function test_delete_document_code_module_translations()
    {
        $documentCodeModuleTranslations = factory(DocumentCodeModuleTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_code_module_translations/'.$documentCodeModuleTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_code_module_translations/'.$documentCodeModuleTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
