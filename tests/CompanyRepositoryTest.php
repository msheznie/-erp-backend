<?php

use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyRepositoryTest extends TestCase
{
    use MakeCompanyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyRepository
     */
    protected $companyRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyRepo = App::make(CompanyRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompany()
    {
        $company = $this->fakeCompanyData();
        $createdCompany = $this->companyRepo->create($company);
        $createdCompany = $createdCompany->toArray();
        $this->assertArrayHasKey('id', $createdCompany);
        $this->assertNotNull($createdCompany['id'], 'Created Company must have id specified');
        $this->assertNotNull(Company::find($createdCompany['id']), 'Company with given id must be in DB');
        $this->assertModelData($company, $createdCompany);
    }

    /**
     * @test read
     */
    public function testReadCompany()
    {
        $company = $this->makeCompany();
        $dbCompany = $this->companyRepo->find($company->id);
        $dbCompany = $dbCompany->toArray();
        $this->assertModelData($company->toArray(), $dbCompany);
    }

    /**
     * @test update
     */
    public function testUpdateCompany()
    {
        $company = $this->makeCompany();
        $fakeCompany = $this->fakeCompanyData();
        $updatedCompany = $this->companyRepo->update($fakeCompany, $company->id);
        $this->assertModelData($fakeCompany, $updatedCompany->toArray());
        $dbCompany = $this->companyRepo->find($company->id);
        $this->assertModelData($fakeCompany, $dbCompany->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompany()
    {
        $company = $this->makeCompany();
        $resp = $this->companyRepo->delete($company->id);
        $this->assertTrue($resp);
        $this->assertNull(Company::find($company->id), 'Company should not exist in DB');
    }
}
