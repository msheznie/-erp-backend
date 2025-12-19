<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplatesDetailsApiTest extends TestCase
{
    use MakeTemplatesDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTemplatesDetails()
    {
        $templatesDetails = $this->fakeTemplatesDetailsData();
        $this->json('POST', '/api/v1/templatesDetails', $templatesDetails);

        $this->assertApiResponse($templatesDetails);
    }

    /**
     * @test
     */
    public function testReadTemplatesDetails()
    {
        $templatesDetails = $this->makeTemplatesDetails();
        $this->json('GET', '/api/v1/templatesDetails/'.$templatesDetails->id);

        $this->assertApiResponse($templatesDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTemplatesDetails()
    {
        $templatesDetails = $this->makeTemplatesDetails();
        $editedTemplatesDetails = $this->fakeTemplatesDetailsData();

        $this->json('PUT', '/api/v1/templatesDetails/'.$templatesDetails->id, $editedTemplatesDetails);

        $this->assertApiResponse($editedTemplatesDetails);
    }

    /**
     * @test
     */
    public function testDeleteTemplatesDetails()
    {
        $templatesDetails = $this->makeTemplatesDetails();
        $this->json('DELETE', '/api/v1/templatesDetails/'.$templatesDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/templatesDetails/'.$templatesDetails->id);

        $this->assertResponseStatus(404);
    }
}
