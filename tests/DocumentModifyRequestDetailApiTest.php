<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentModifyRequestDetail;

class DocumentModifyRequestDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_modify_request_details', $documentModifyRequestDetail
        );

        $this->assertApiResponse($documentModifyRequestDetail);
    }

    /**
     * @test
     */
    public function test_read_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_modify_request_details/'.$documentModifyRequestDetail->id
        );

        $this->assertApiResponse($documentModifyRequestDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->create();
        $editedDocumentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_modify_request_details/'.$documentModifyRequestDetail->id,
            $editedDocumentModifyRequestDetail
        );

        $this->assertApiResponse($editedDocumentModifyRequestDetail);
    }

    /**
     * @test
     */
    public function test_delete_document_modify_request_detail()
    {
        $documentModifyRequestDetail = factory(DocumentModifyRequestDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_modify_request_details/'.$documentModifyRequestDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_modify_request_details/'.$documentModifyRequestDetail->id
        );

        $this->response->assertStatus(404);
    }
}
