<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplatesGLCodeApiTest extends TestCase
{
    use MakeTemplatesGLCodeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTemplatesGLCode()
    {
        $templatesGLCode = $this->fakeTemplatesGLCodeData();
        $this->json('POST', '/api/v1/templatesGLCodes', $templatesGLCode);

        $this->assertApiResponse($templatesGLCode);
    }

    /**
     * @test
     */
    public function testReadTemplatesGLCode()
    {
        $templatesGLCode = $this->makeTemplatesGLCode();
        $this->json('GET', '/api/v1/templatesGLCodes/'.$templatesGLCode->id);

        $this->assertApiResponse($templatesGLCode->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTemplatesGLCode()
    {
        $templatesGLCode = $this->makeTemplatesGLCode();
        $editedTemplatesGLCode = $this->fakeTemplatesGLCodeData();

        $this->json('PUT', '/api/v1/templatesGLCodes/'.$templatesGLCode->id, $editedTemplatesGLCode);

        $this->assertApiResponse($editedTemplatesGLCode);
    }

    /**
     * @test
     */
    public function testDeleteTemplatesGLCode()
    {
        $templatesGLCode = $this->makeTemplatesGLCode();
        $this->json('DELETE', '/api/v1/templatesGLCodes/'.$templatesGLCode->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/templatesGLCodes/'.$templatesGLCode->id);

        $this->assertResponseStatus(404);
    }
}
