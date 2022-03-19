<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderSiteVisitDates;

class TenderSiteVisitDatesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_site_visit_dates', $tenderSiteVisitDates
        );

        $this->assertApiResponse($tenderSiteVisitDates);
    }

    /**
     * @test
     */
    public function test_read_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_site_visit_dates/'.$tenderSiteVisitDates->id
        );

        $this->assertApiResponse($tenderSiteVisitDates->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->create();
        $editedTenderSiteVisitDates = factory(TenderSiteVisitDates::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_site_visit_dates/'.$tenderSiteVisitDates->id,
            $editedTenderSiteVisitDates
        );

        $this->assertApiResponse($editedTenderSiteVisitDates);
    }

    /**
     * @test
     */
    public function test_delete_tender_site_visit_dates()
    {
        $tenderSiteVisitDates = factory(TenderSiteVisitDates::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_site_visit_dates/'.$tenderSiteVisitDates->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_site_visit_dates/'.$tenderSiteVisitDates->id
        );

        $this->response->assertStatus(404);
    }
}
