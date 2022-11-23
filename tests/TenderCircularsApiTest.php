<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderCirculars;

class TenderCircularsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_circulars', $tenderCirculars
        );

        $this->assertApiResponse($tenderCirculars);
    }

    /**
     * @test
     */
    public function test_read_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_circulars/'.$tenderCirculars->id
        );

        $this->assertApiResponse($tenderCirculars->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->create();
        $editedTenderCirculars = factory(TenderCirculars::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_circulars/'.$tenderCirculars->id,
            $editedTenderCirculars
        );

        $this->assertApiResponse($editedTenderCirculars);
    }

    /**
     * @test
     */
    public function test_delete_tender_circulars()
    {
        $tenderCirculars = factory(TenderCirculars::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_circulars/'.$tenderCirculars->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_circulars/'.$tenderCirculars->id
        );

        $this->response->assertStatus(404);
    }
}
