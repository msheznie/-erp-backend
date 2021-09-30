<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HRDocumentDescriptionMaster;

class HRDocumentDescriptionMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/h_r_document_description_masters', $hRDocumentDescriptionMaster
        );

        $this->assertApiResponse($hRDocumentDescriptionMaster);
    }

    /**
     * @test
     */
    public function test_read_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/h_r_document_description_masters/'.$hRDocumentDescriptionMaster->id
        );

        $this->assertApiResponse($hRDocumentDescriptionMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->create();
        $editedHRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/h_r_document_description_masters/'.$hRDocumentDescriptionMaster->id,
            $editedHRDocumentDescriptionMaster
        );

        $this->assertApiResponse($editedHRDocumentDescriptionMaster);
    }

    /**
     * @test
     */
    public function test_delete_h_r_document_description_master()
    {
        $hRDocumentDescriptionMaster = factory(HRDocumentDescriptionMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/h_r_document_description_masters/'.$hRDocumentDescriptionMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/h_r_document_description_masters/'.$hRDocumentDescriptionMaster->id
        );

        $this->response->assertStatus(404);
    }
}
