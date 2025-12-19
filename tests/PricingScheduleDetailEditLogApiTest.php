<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PricingScheduleDetailEditLog;

class PricingScheduleDetailEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pricing_schedule_detail_edit_logs', $pricingScheduleDetailEditLog
        );

        $this->assertApiResponse($pricingScheduleDetailEditLog);
    }

    /**
     * @test
     */
    public function test_read_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_detail_edit_logs/'.$pricingScheduleDetailEditLog->id
        );

        $this->assertApiResponse($pricingScheduleDetailEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->create();
        $editedPricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pricing_schedule_detail_edit_logs/'.$pricingScheduleDetailEditLog->id,
            $editedPricingScheduleDetailEditLog
        );

        $this->assertApiResponse($editedPricingScheduleDetailEditLog);
    }

    /**
     * @test
     */
    public function test_delete_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pricing_schedule_detail_edit_logs/'.$pricingScheduleDetailEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_detail_edit_logs/'.$pricingScheduleDetailEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
