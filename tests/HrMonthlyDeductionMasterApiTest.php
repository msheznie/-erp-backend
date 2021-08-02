<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrMonthlyDeductionMaster;

class HrMonthlyDeductionMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_monthly_deduction_masters', $hrMonthlyDeductionMaster
        );

        $this->assertApiResponse($hrMonthlyDeductionMaster);
    }

    /**
     * @test
     */
    public function test_read_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_monthly_deduction_masters/'.$hrMonthlyDeductionMaster->id
        );

        $this->assertApiResponse($hrMonthlyDeductionMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->create();
        $editedHrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_monthly_deduction_masters/'.$hrMonthlyDeductionMaster->id,
            $editedHrMonthlyDeductionMaster
        );

        $this->assertApiResponse($editedHrMonthlyDeductionMaster);
    }

    /**
     * @test
     */
    public function test_delete_hr_monthly_deduction_master()
    {
        $hrMonthlyDeductionMaster = factory(HrMonthlyDeductionMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_monthly_deduction_masters/'.$hrMonthlyDeductionMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_monthly_deduction_masters/'.$hrMonthlyDeductionMaster->id
        );

        $this->response->assertStatus(404);
    }
}
