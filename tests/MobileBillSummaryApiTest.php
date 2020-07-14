<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MobileBillSummary;

class MobileBillSummaryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mobile_bill_summaries', $mobileBillSummary
        );

        $this->assertApiResponse($mobileBillSummary);
    }

    /**
     * @test
     */
    public function test_read_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/mobile_bill_summaries/'.$mobileBillSummary->id
        );

        $this->assertApiResponse($mobileBillSummary->toArray());
    }

    /**
     * @test
     */
    public function test_update_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->create();
        $editedMobileBillSummary = factory(MobileBillSummary::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mobile_bill_summaries/'.$mobileBillSummary->id,
            $editedMobileBillSummary
        );

        $this->assertApiResponse($editedMobileBillSummary);
    }

    /**
     * @test
     */
    public function test_delete_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mobile_bill_summaries/'.$mobileBillSummary->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mobile_bill_summaries/'.$mobileBillSummary->id
        );

        $this->response->assertStatus(404);
    }
}
