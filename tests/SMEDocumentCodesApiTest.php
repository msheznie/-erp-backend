<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEDocumentCodes;

class SMEDocumentCodesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_document_codes', $sMEDocumentCodes
        );

        $this->assertApiResponse($sMEDocumentCodes);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_document_codes/'.$sMEDocumentCodes->id
        );

        $this->assertApiResponse($sMEDocumentCodes->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->create();
        $editedSMEDocumentCodes = factory(SMEDocumentCodes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_document_codes/'.$sMEDocumentCodes->id,
            $editedSMEDocumentCodes
        );

        $this->assertApiResponse($editedSMEDocumentCodes);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_document_codes/'.$sMEDocumentCodes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_document_codes/'.$sMEDocumentCodes->id
        );

        $this->response->assertStatus(404);
    }
}
