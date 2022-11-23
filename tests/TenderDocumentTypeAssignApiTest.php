<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderDocumentTypeAssign;

class TenderDocumentTypeAssignApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_document_type_assigns', $tenderDocumentTypeAssign
        );

        $this->assertApiResponse($tenderDocumentTypeAssign);
    }

    /**
     * @test
     */
    public function test_read_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_document_type_assigns/'.$tenderDocumentTypeAssign->id
        );

        $this->assertApiResponse($tenderDocumentTypeAssign->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->create();
        $editedTenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_document_type_assigns/'.$tenderDocumentTypeAssign->id,
            $editedTenderDocumentTypeAssign
        );

        $this->assertApiResponse($editedTenderDocumentTypeAssign);
    }

    /**
     * @test
     */
    public function test_delete_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_document_type_assigns/'.$tenderDocumentTypeAssign->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_document_type_assigns/'.$tenderDocumentTypeAssign->id
        );

        $this->response->assertStatus(404);
    }
}
