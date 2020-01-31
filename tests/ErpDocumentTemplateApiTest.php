<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeErpDocumentTemplateTrait;
use Tests\ApiTestTrait;

class ErpDocumentTemplateApiTest extends TestCase
{
    use MakeErpDocumentTemplateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_document_template()
    {
        $erpDocumentTemplate = $this->fakeErpDocumentTemplateData();
        $this->response = $this->json('POST', '/api/erpDocumentTemplates', $erpDocumentTemplate);

        $this->assertApiResponse($erpDocumentTemplate);
    }

    /**
     * @test
     */
    public function test_read_erp_document_template()
    {
        $erpDocumentTemplate = $this->makeErpDocumentTemplate();
        $this->response = $this->json('GET', '/api/erpDocumentTemplates/'.$erpDocumentTemplate->id);

        $this->assertApiResponse($erpDocumentTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_document_template()
    {
        $erpDocumentTemplate = $this->makeErpDocumentTemplate();
        $editedErpDocumentTemplate = $this->fakeErpDocumentTemplateData();

        $this->response = $this->json('PUT', '/api/erpDocumentTemplates/'.$erpDocumentTemplate->id, $editedErpDocumentTemplate);

        $this->assertApiResponse($editedErpDocumentTemplate);
    }

    /**
     * @test
     */
    public function test_delete_erp_document_template()
    {
        $erpDocumentTemplate = $this->makeErpDocumentTemplate();
        $this->response = $this->json('DELETE', '/api/erpDocumentTemplates/'.$erpDocumentTemplate->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/erpDocumentTemplates/'.$erpDocumentTemplate->id);

        $this->response->assertStatus(404);
    }
}
