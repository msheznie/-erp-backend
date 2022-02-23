<?php namespace Tests\Repositories;

use App\Models\CompanyJobs;
use App\Repositories\CompanyJobsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CompanyJobsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyJobsRepository
     */
    protected $companyJobsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->companyJobsRepo = \App::make(CompanyJobsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->make()->toArray();

        $createdCompanyJobs = $this->companyJobsRepo->create($companyJobs);

        $createdCompanyJobs = $createdCompanyJobs->toArray();
        $this->assertArrayHasKey('id', $createdCompanyJobs);
        $this->assertNotNull($createdCompanyJobs['id'], 'Created CompanyJobs must have id specified');
        $this->assertNotNull(CompanyJobs::find($createdCompanyJobs['id']), 'CompanyJobs with given id must be in DB');
        $this->assertModelData($companyJobs, $createdCompanyJobs);
    }

    /**
     * @test read
     */
    public function test_read_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->create();

        $dbCompanyJobs = $this->companyJobsRepo->find($companyJobs->id);

        $dbCompanyJobs = $dbCompanyJobs->toArray();
        $this->assertModelData($companyJobs->toArray(), $dbCompanyJobs);
    }

    /**
     * @test update
     */
    public function test_update_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->create();
        $fakeCompanyJobs = factory(CompanyJobs::class)->make()->toArray();

        $updatedCompanyJobs = $this->companyJobsRepo->update($fakeCompanyJobs, $companyJobs->id);

        $this->assertModelData($fakeCompanyJobs, $updatedCompanyJobs->toArray());
        $dbCompanyJobs = $this->companyJobsRepo->find($companyJobs->id);
        $this->assertModelData($fakeCompanyJobs, $dbCompanyJobs->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_company_jobs()
    {
        $companyJobs = factory(CompanyJobs::class)->create();

        $resp = $this->companyJobsRepo->delete($companyJobs->id);

        $this->assertTrue($resp);
        $this->assertNull(CompanyJobs::find($companyJobs->id), 'CompanyJobs should not exist in DB');
    }
}
