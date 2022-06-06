<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderDocumentTypes;

class TenderDocumentTypesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_document_types', $tenderDocumentTypes
        );

        $this->assertApiResponse($tenderDocumentTypes);
    }

    /**
     * @test
     */
    public function test_read_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_document_types/'.$tenderDocumentTypes->id
        );

        $this->assertApiResponse($tenderDocumentTypes->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->create();
        $editedTenderDocumentTypes = factory(TenderDocumentTypes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_document_types/'.$tenderDocumentTypes->id,
            $editedTenderDocumentTypes
        );

        $this->assertApiResponse($editedTenderDocumentTypes);
    }

    /**
     * @test
     */
    public function test_delete_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_document_types/'.$tenderDocumentTypes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_document_types/'.$tenderDocumentTypes->id
        );

        $this->response->assertStatus(404);
    }
}
