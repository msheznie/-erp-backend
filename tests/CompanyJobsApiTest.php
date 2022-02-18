<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CompanyJobs;

class CompanyJobsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/company_jobs', $companyJobs
        );

        $this->assertApiResponse($companyJobs);
    }

    /**
     * @test
     */
    public function test_read_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/company_jobs/'.$companyJobs->id
        );

        $this->assertApiResponse($companyJobs->toArray());
    }

    /**
     * @test
     */
    public function test_update_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->create();
        $editedCompanyJobs = factory(CompanyJobs::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/company_jobs/'.$companyJobs->id,
            $editedCompanyJobs
        );

        $this->assertApiResponse($editedCompanyJobs);
    }

    /**
     * @test
     */
    public function test_delete_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/company_jobs/'.$companyJobs->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/company_jobs/'.$companyJobs->id
        );

        $this->response->assertStatus(404);
    }
}
