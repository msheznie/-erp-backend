<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderFieldType;

class TenderFieldTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_field_types', $tenderFieldType
        );

        $this->assertApiResponse($tenderFieldType);
    }

    /**
     * @test
     */
    public function test_read_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_field_types/'.$tenderFieldType->id
        );

        $this->assertApiResponse($tenderFieldType->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->create();
        $editedTenderFieldType = factory(TenderFieldType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_field_types/'.$tenderFieldType->id,
            $editedTenderFieldType
        );

        $this->assertApiResponse($editedTenderFieldType);
    }

    /**
     * @test
     */
    public function test_delete_tender_field_type()
    {
        $tenderFieldType = factory(TenderFieldType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_field_types/'.$tenderFieldType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_field_types/'.$tenderFieldType->id
        );

        $this->response->assertStatus(404);
    }
}
