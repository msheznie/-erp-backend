<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderType;

class TenderTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_type()
    {
        $tenderType = factory(TenderType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_types', $tenderType
        );

        $this->assertApiResponse($tenderType);
    }

    /**
     * @test
     */
    public function test_read_tender_type()
    {
        $tenderType = factory(TenderType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_types/'.$tenderType->id
        );

        $this->assertApiResponse($tenderType->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_type()
    {
        $tenderType = factory(TenderType::class)->create();
        $editedTenderType = factory(TenderType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_types/'.$tenderType->id,
            $editedTenderType
        );

        $this->assertApiResponse($editedTenderType);
    }

    /**
     * @test
     */
    public function test_delete_tender_type()
    {
        $tenderType = factory(TenderType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_types/'.$tenderType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_types/'.$tenderType->id
        );

        $this->response->assertStatus(404);
    }
}
