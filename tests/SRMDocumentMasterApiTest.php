<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMDocumentMaster;

class SRMDocumentMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_document_masters', $sRMDocumentMaster
        );

        $this->assertApiResponse($sRMDocumentMaster);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_document_masters/'.$sRMDocumentMaster->id
        );

        $this->assertApiResponse($sRMDocumentMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->create();
        $editedSRMDocumentMaster = factory(SRMDocumentMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_document_masters/'.$sRMDocumentMaster->id,
            $editedSRMDocumentMaster
        );

        $this->assertApiResponse($editedSRMDocumentMaster);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_document_masters/'.$sRMDocumentMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_document_masters/'.$sRMDocumentMaster->id
        );

        $this->response->assertStatus(404);
    }
}
