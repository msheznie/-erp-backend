<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSPersonalDocumentsTrait;
use Tests\ApiTestTrait;

class HRMSPersonalDocumentsApiTest extends TestCase
{
    use MakeHRMSPersonalDocumentsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->fakeHRMSPersonalDocumentsData();
        $this->response = $this->json('POST', '/api/hRMSPersonalDocuments', $hRMSPersonalDocuments);

        $this->assertApiResponse($hRMSPersonalDocuments);
    }

    /**
     * @test
     */
    public function test_read_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->makeHRMSPersonalDocuments();
        $this->response = $this->json('GET', '/api/hRMSPersonalDocuments/'.$hRMSPersonalDocuments->id);

        $this->assertApiResponse($hRMSPersonalDocuments->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->makeHRMSPersonalDocuments();
        $editedHRMSPersonalDocuments = $this->fakeHRMSPersonalDocumentsData();

        $this->response = $this->json('PUT', '/api/hRMSPersonalDocuments/'.$hRMSPersonalDocuments->id, $editedHRMSPersonalDocuments);

        $this->assertApiResponse($editedHRMSPersonalDocuments);
    }

    /**
     * @test
     */
    public function test_delete_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->makeHRMSPersonalDocuments();
        $this->response = $this->json('DELETE', '/api/hRMSPersonalDocuments/'.$hRMSPersonalDocuments->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hRMSPersonalDocuments/'.$hRMSPersonalDocuments->id);

        $this->response->assertStatus(404);
    }
}
