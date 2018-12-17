<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentRestrictionPolicyApiTest extends TestCase
{
    use MakeDocumentRestrictionPolicyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->fakeDocumentRestrictionPolicyData();
        $this->json('POST', '/api/v1/documentRestrictionPolicies', $documentRestrictionPolicy);

        $this->assertApiResponse($documentRestrictionPolicy);
    }

    /**
     * @test
     */
    public function testReadDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->makeDocumentRestrictionPolicy();
        $this->json('GET', '/api/v1/documentRestrictionPolicies/'.$documentRestrictionPolicy->id);

        $this->assertApiResponse($documentRestrictionPolicy->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->makeDocumentRestrictionPolicy();
        $editedDocumentRestrictionPolicy = $this->fakeDocumentRestrictionPolicyData();

        $this->json('PUT', '/api/v1/documentRestrictionPolicies/'.$documentRestrictionPolicy->id, $editedDocumentRestrictionPolicy);

        $this->assertApiResponse($editedDocumentRestrictionPolicy);
    }

    /**
     * @test
     */
    public function testDeleteDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->makeDocumentRestrictionPolicy();
        $this->json('DELETE', '/api/v1/documentRestrictionPolicies/'.$documentRestrictionPolicy->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentRestrictionPolicies/'.$documentRestrictionPolicy->id);

        $this->assertResponseStatus(404);
    }
}
