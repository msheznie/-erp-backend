<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderMainWorks;

class TenderMainWorksApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_main_works', $tenderMainWorks
        );

        $this->assertApiResponse($tenderMainWorks);
    }

    /**
     * @test
     */
    public function test_read_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_main_works/'.$tenderMainWorks->id
        );

        $this->assertApiResponse($tenderMainWorks->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->create();
        $editedTenderMainWorks = factory(TenderMainWorks::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_main_works/'.$tenderMainWorks->id,
            $editedTenderMainWorks
        );

        $this->assertApiResponse($editedTenderMainWorks);
    }

    /**
     * @test
     */
    public function test_delete_tender_main_works()
    {
        $tenderMainWorks = factory(TenderMainWorks::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_main_works/'.$tenderMainWorks->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_main_works/'.$tenderMainWorks->id
        );

        $this->response->assertStatus(404);
    }
}
