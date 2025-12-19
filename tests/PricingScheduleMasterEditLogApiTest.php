<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PricingScheduleMasterEditLog;

class PricingScheduleMasterEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pricing_schedule_master_edit_logs', $pricingScheduleMasterEditLog
        );

        $this->assertApiResponse($pricingScheduleMasterEditLog);
    }

    /**
     * @test
     */
    public function test_read_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_master_edit_logs/'.$pricingScheduleMasterEditLog->id
        );

        $this->assertApiResponse($pricingScheduleMasterEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->create();
        $editedPricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pricing_schedule_master_edit_logs/'.$pricingScheduleMasterEditLog->id,
            $editedPricingScheduleMasterEditLog
        );

        $this->assertApiResponse($editedPricingScheduleMasterEditLog);
    }

    /**
     * @test
     */
    public function test_delete_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pricing_schedule_master_edit_logs/'.$pricingScheduleMasterEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_master_edit_logs/'.$pricingScheduleMasterEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
