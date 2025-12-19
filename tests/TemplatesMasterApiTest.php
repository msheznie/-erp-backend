<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplatesMasterApiTest extends TestCase
{
    use MakeTemplatesMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTemplatesMaster()
    {
        $templatesMaster = $this->fakeTemplatesMasterData();
        $this->json('POST', '/api/v1/templatesMasters', $templatesMaster);

        $this->assertApiResponse($templatesMaster);
    }

    /**
     * @test
     */
    public function testReadTemplatesMaster()
    {
        $templatesMaster = $this->makeTemplatesMaster();
        $this->json('GET', '/api/v1/templatesMasters/'.$templatesMaster->id);

        $this->assertApiResponse($templatesMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTemplatesMaster()
    {
        $templatesMaster = $this->makeTemplatesMaster();
        $editedTemplatesMaster = $this->fakeTemplatesMasterData();

        $this->json('PUT', '/api/v1/templatesMasters/'.$templatesMaster->id, $editedTemplatesMaster);

        $this->assertApiResponse($editedTemplatesMaster);
    }

    /**
     * @test
     */
    public function testDeleteTemplatesMaster()
    {
        $templatesMaster = $this->makeTemplatesMaster();
        $this->json('DELETE', '/api/v1/templatesMasters/'.$templatesMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/templatesMasters/'.$templatesMaster->id);

        $this->assertResponseStatus(404);
    }
}
