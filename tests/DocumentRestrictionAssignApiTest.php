<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentRestrictionAssignApiTest extends TestCase
{
    use MakeDocumentRestrictionAssignTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->fakeDocumentRestrictionAssignData();
        $this->json('POST', '/api/v1/documentRestrictionAssigns', $documentRestrictionAssign);

        $this->assertApiResponse($documentRestrictionAssign);
    }

    /**
     * @test
     */
    public function testReadDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->makeDocumentRestrictionAssign();
        $this->json('GET', '/api/v1/documentRestrictionAssigns/'.$documentRestrictionAssign->id);

        $this->assertApiResponse($documentRestrictionAssign->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->makeDocumentRestrictionAssign();
        $editedDocumentRestrictionAssign = $this->fakeDocumentRestrictionAssignData();

        $this->json('PUT', '/api/v1/documentRestrictionAssigns/'.$documentRestrictionAssign->id, $editedDocumentRestrictionAssign);

        $this->assertApiResponse($editedDocumentRestrictionAssign);
    }

    /**
     * @test
     */
    public function testDeleteDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->makeDocumentRestrictionAssign();
        $this->json('DELETE', '/api/v1/documentRestrictionAssigns/'.$documentRestrictionAssign->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentRestrictionAssigns/'.$documentRestrictionAssign->id);

        $this->assertResponseStatus(404);
    }
}
