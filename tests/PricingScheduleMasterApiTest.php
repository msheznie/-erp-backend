<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PricingScheduleMaster;

class PricingScheduleMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/pricing_schedule_masters', $pricingScheduleMaster
        );

        $this->assertApiResponse($pricingScheduleMaster);
    }

    /**
     * @test
     */
    public function test_read_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_masters/'.$pricingScheduleMaster->id
        );

        $this->assertApiResponse($pricingScheduleMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->create();
        $editedPricingScheduleMaster = factory(PricingScheduleMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/pricing_schedule_masters/'.$pricingScheduleMaster->id,
            $editedPricingScheduleMaster
        );

        $this->assertApiResponse($editedPricingScheduleMaster);
    }

    /**
     * @test
     */
    public function test_delete_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/pricing_schedule_masters/'.$pricingScheduleMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/pricing_schedule_masters/'.$pricingScheduleMaster->id
        );

        $this->response->assertStatus(404);
    }
}
