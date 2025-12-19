<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEDocumentCodeMaster;

class SMEDocumentCodeMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_document_code_masters', $sMEDocumentCodeMaster
        );

        $this->assertApiResponse($sMEDocumentCodeMaster);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_document_code_masters/'.$sMEDocumentCodeMaster->id
        );

        $this->assertApiResponse($sMEDocumentCodeMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->create();
        $editedSMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_document_code_masters/'.$sMEDocumentCodeMaster->id,
            $editedSMEDocumentCodeMaster
        );

        $this->assertApiResponse($editedSMEDocumentCodeMaster);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_document_code_masters/'.$sMEDocumentCodeMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_document_code_masters/'.$sMEDocumentCodeMaster->id
        );

        $this->response->assertStatus(404);
    }
}
