<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrMonthlyDeductionDetail;

class HrMonthlyDeductionDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_monthly_deduction_details', $hrMonthlyDeductionDetail
        );

        $this->assertApiResponse($hrMonthlyDeductionDetail);
    }

    /**
     * @test
     */
    public function test_read_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_monthly_deduction_details/'.$hrMonthlyDeductionDetail->id
        );

        $this->assertApiResponse($hrMonthlyDeductionDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->create();
        $editedHrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_monthly_deduction_details/'.$hrMonthlyDeductionDetail->id,
            $editedHrMonthlyDeductionDetail
        );

        $this->assertApiResponse($editedHrMonthlyDeductionDetail);
    }

    /**
     * @test
     */
    public function test_delete_hr_monthly_deduction_detail()
    {
        $hrMonthlyDeductionDetail = factory(HrMonthlyDeductionDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_monthly_deduction_details/'.$hrMonthlyDeductionDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_monthly_deduction_details/'.$hrMonthlyDeductionDetail->id
        );

        $this->response->assertStatus(404);
    }
}
