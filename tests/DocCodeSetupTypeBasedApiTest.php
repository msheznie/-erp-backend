<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocCodeSetupTypeBased;

class DocCodeSetupTypeBasedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/doc_code_setup_type_baseds', $docCodeSetupTypeBased
        );

        $this->assertApiResponse($docCodeSetupTypeBased);
    }

    /**
     * @test
     */
    public function test_read_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/doc_code_setup_type_baseds/'.$docCodeSetupTypeBased->id
        );

        $this->assertApiResponse($docCodeSetupTypeBased->toArray());
    }

    /**
     * @test
     */
    public function test_update_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->create();
        $editedDocCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/doc_code_setup_type_baseds/'.$docCodeSetupTypeBased->id,
            $editedDocCodeSetupTypeBased
        );

        $this->assertApiResponse($editedDocCodeSetupTypeBased);
    }

    /**
     * @test
     */
    public function test_delete_doc_code_setup_type_based()
    {
        $docCodeSetupTypeBased = factory(DocCodeSetupTypeBased::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/doc_code_setup_type_baseds/'.$docCodeSetupTypeBased->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/doc_code_setup_type_baseds/'.$docCodeSetupTypeBased->id
        );

        $this->response->assertStatus(404);
    }
}
