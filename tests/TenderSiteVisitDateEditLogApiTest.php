<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderSiteVisitDateEditLog;

class TenderSiteVisitDateEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_site_visit_date_edit_logs', $tenderSiteVisitDateEditLog
        );

        $this->assertApiResponse($tenderSiteVisitDateEditLog);
    }

    /**
     * @test
     */
    public function test_read_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_site_visit_date_edit_logs/'.$tenderSiteVisitDateEditLog->id
        );

        $this->assertApiResponse($tenderSiteVisitDateEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->create();
        $editedTenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_site_visit_date_edit_logs/'.$tenderSiteVisitDateEditLog->id,
            $editedTenderSiteVisitDateEditLog
        );

        $this->assertApiResponse($editedTenderSiteVisitDateEditLog);
    }

    /**
     * @test
     */
    public function test_delete_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_site_visit_date_edit_logs/'.$tenderSiteVisitDateEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_site_visit_date_edit_logs/'.$tenderSiteVisitDateEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
