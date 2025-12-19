<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSalaryProcessDetailTrait;
use Tests\ApiTestTrait;

class SalaryProcessDetailApiTest extends TestCase
{
    use MakeSalaryProcessDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_salary_process_detail()
    {
        $salaryProcessDetail = $this->fakeSalaryProcessDetailData();
        $this->response = $this->json('POST', '/api/salaryProcessDetails', $salaryProcessDetail);

        $this->assertApiResponse($salaryProcessDetail);
    }

    /**
     * @test
     */
    public function test_read_salary_process_detail()
    {
        $salaryProcessDetail = $this->makeSalaryProcessDetail();
        $this->response = $this->json('GET', '/api/salaryProcessDetails/'.$salaryProcessDetail->id);

        $this->assertApiResponse($salaryProcessDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_salary_process_detail()
    {
        $salaryProcessDetail = $this->makeSalaryProcessDetail();
        $editedSalaryProcessDetail = $this->fakeSalaryProcessDetailData();

        $this->response = $this->json('PUT', '/api/salaryProcessDetails/'.$salaryProcessDetail->id, $editedSalaryProcessDetail);

        $this->assertApiResponse($editedSalaryProcessDetail);
    }

    /**
     * @test
     */
    public function test_delete_salary_process_detail()
    {
        $salaryProcessDetail = $this->makeSalaryProcessDetail();
        $this->response = $this->json('DELETE', '/api/salaryProcessDetails/'.$salaryProcessDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/salaryProcessDetails/'.$salaryProcessDetail->id);

        $this->response->assertStatus(404);
    }
}
