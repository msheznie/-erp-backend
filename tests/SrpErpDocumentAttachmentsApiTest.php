<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpErpDocumentAttachments;

class SrpErpDocumentAttachmentsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_erp_document_attachments', $srpErpDocumentAttachments
        );

        $this->assertApiResponse($srpErpDocumentAttachments);
    }

    /**
     * @test
     */
    public function test_read_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_erp_document_attachments/'.$srpErpDocumentAttachments->id
        );

        $this->assertApiResponse($srpErpDocumentAttachments->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->create();
        $editedSrpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_erp_document_attachments/'.$srpErpDocumentAttachments->id,
            $editedSrpErpDocumentAttachments
        );

        $this->assertApiResponse($editedSrpErpDocumentAttachments);
    }

    /**
     * @test
     */
    public function test_delete_srp_erp_document_attachments()
    {
        $srpErpDocumentAttachments = factory(SrpErpDocumentAttachments::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_erp_document_attachments/'.$srpErpDocumentAttachments->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_erp_document_attachments/'.$srpErpDocumentAttachments->id
        );

        $this->response->assertStatus(404);
    }
}
