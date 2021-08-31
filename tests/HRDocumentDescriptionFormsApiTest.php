<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HRDocumentDescriptionForms;

class HRDocumentDescriptionFormsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/h_r_document_description_forms', $hRDocumentDescriptionForms
        );

        $this->assertApiResponse($hRDocumentDescriptionForms);
    }

    /**
     * @test
     */
    public function test_read_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/h_r_document_description_forms/'.$hRDocumentDescriptionForms->id
        );

        $this->assertApiResponse($hRDocumentDescriptionForms->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->create();
        $editedHRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/h_r_document_description_forms/'.$hRDocumentDescriptionForms->id,
            $editedHRDocumentDescriptionForms
        );

        $this->assertApiResponse($editedHRDocumentDescriptionForms);
    }

    /**
     * @test
     */
    public function test_delete_h_r_document_description_forms()
    {
        $hRDocumentDescriptionForms = factory(HRDocumentDescriptionForms::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/h_r_document_description_forms/'.$hRDocumentDescriptionForms->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/h_r_document_description_forms/'.$hRDocumentDescriptionForms->id
        );

        $this->response->assertStatus(404);
    }
}
