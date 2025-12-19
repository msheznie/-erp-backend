<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CompanyDigitalStamp;

class CompanyDigitalStampApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/company_digital_stamps', $companyDigitalStamp
        );

        $this->assertApiResponse($companyDigitalStamp);
    }

    /**
     * @test
     */
    public function test_read_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/company_digital_stamps/'.$companyDigitalStamp->id
        );

        $this->assertApiResponse($companyDigitalStamp->toArray());
    }

    /**
     * @test
     */
    public function test_update_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->create();
        $editedCompanyDigitalStamp = factory(CompanyDigitalStamp::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/company_digital_stamps/'.$companyDigitalStamp->id,
            $editedCompanyDigitalStamp
        );

        $this->assertApiResponse($editedCompanyDigitalStamp);
    }

    /**
     * @test
     */
    public function test_delete_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/company_digital_stamps/'.$companyDigitalStamp->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/company_digital_stamps/'.$companyDigitalStamp->id
        );

        $this->response->assertStatus(404);
    }
}
