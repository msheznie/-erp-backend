<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocCodeSetupCommon;

class DocCodeSetupCommonApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/doc_code_setup_commons', $docCodeSetupCommon
        );

        $this->assertApiResponse($docCodeSetupCommon);
    }

    /**
     * @test
     */
    public function test_read_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/doc_code_setup_commons/'.$docCodeSetupCommon->id
        );

        $this->assertApiResponse($docCodeSetupCommon->toArray());
    }

    /**
     * @test
     */
    public function test_update_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->create();
        $editedDocCodeSetupCommon = factory(DocCodeSetupCommon::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/doc_code_setup_commons/'.$docCodeSetupCommon->id,
            $editedDocCodeSetupCommon
        );

        $this->assertApiResponse($editedDocCodeSetupCommon);
    }

    /**
     * @test
     */
    public function test_delete_doc_code_setup_common()
    {
        $docCodeSetupCommon = factory(DocCodeSetupCommon::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/doc_code_setup_commons/'.$docCodeSetupCommon->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/doc_code_setup_commons/'.$docCodeSetupCommon->id
        );

        $this->response->assertStatus(404);
    }
}
