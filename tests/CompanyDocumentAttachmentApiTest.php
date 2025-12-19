<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyDocumentAttachmentApiTest extends TestCase
{
    use MakeCompanyDocumentAttachmentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->fakeCompanyDocumentAttachmentData();
        $this->json('POST', '/api/v1/companyDocumentAttachments', $companyDocumentAttachment);

        $this->assertApiResponse($companyDocumentAttachment);
    }

    /**
     * @test
     */
    public function testReadCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->makeCompanyDocumentAttachment();
        $this->json('GET', '/api/v1/companyDocumentAttachments/'.$companyDocumentAttachment->id);

        $this->assertApiResponse($companyDocumentAttachment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->makeCompanyDocumentAttachment();
        $editedCompanyDocumentAttachment = $this->fakeCompanyDocumentAttachmentData();

        $this->json('PUT', '/api/v1/companyDocumentAttachments/'.$companyDocumentAttachment->id, $editedCompanyDocumentAttachment);

        $this->assertApiResponse($editedCompanyDocumentAttachment);
    }

    /**
     * @test
     */
    public function testDeleteCompanyDocumentAttachment()
    {
        $companyDocumentAttachment = $this->makeCompanyDocumentAttachment();
        $this->json('DELETE', '/api/v1/companyDocumentAttachments/'.$companyDocumentAttachment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyDocumentAttachments/'.$companyDocumentAttachment->id);

        $this->assertResponseStatus(404);
    }
}
