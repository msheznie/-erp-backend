<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMTenderCalendarLog;

class SRMTenderCalendarLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_tender_calendar_logs', $sRMTenderCalendarLog
        );

        $this->assertApiResponse($sRMTenderCalendarLog);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_tender_calendar_logs/'.$sRMTenderCalendarLog->id
        );

        $this->assertApiResponse($sRMTenderCalendarLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->create();
        $editedSRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_tender_calendar_logs/'.$sRMTenderCalendarLog->id,
            $editedSRMTenderCalendarLog
        );

        $this->assertApiResponse($editedSRMTenderCalendarLog);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_tender_calendar_logs/'.$sRMTenderCalendarLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_tender_calendar_logs/'.$sRMTenderCalendarLog->id
        );

        $this->response->assertStatus(404);
    }
}
