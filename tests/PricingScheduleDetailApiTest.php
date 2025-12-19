<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PricingScheduleDetail;

class PricingScheduleDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pricing_schedule_details', $pricingScheduleDetail
        );

        $this->assertApiResponse($pricingScheduleDetail);
    }

    /**
     * @test
     */
    public function test_read_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_details/'.$pricingScheduleDetail->id
        );

        $this->assertApiResponse($pricingScheduleDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->create();
        $editedPricingScheduleDetail = factory(PricingScheduleDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pricing_schedule_details/'.$pricingScheduleDetail->id,
            $editedPricingScheduleDetail
        );

        $this->assertApiResponse($editedPricingScheduleDetail);
    }

    /**
     * @test
     */
    public function test_delete_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pricing_schedule_details/'.$pricingScheduleDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_details/'.$pricingScheduleDetail->id
        );

        $this->response->assertStatus(404);
    }
}
