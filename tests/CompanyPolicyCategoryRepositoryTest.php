<?php

use App\Models\CompanyPolicyCategory;
use App\Repositories\CompanyPolicyCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyPolicyCategoryRepositoryTest extends TestCase
{
    use MakeCompanyPolicyCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyPolicyCategoryRepository
     */
    protected $companyPolicyCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyPolicyCategoryRepo = App::make(CompanyPolicyCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->fakeCompanyPolicyCategoryData();
        $createdCompanyPolicyCategory = $this->companyPolicyCategoryRepo->create($companyPolicyCategory);
        $createdCompanyPolicyCategory = $createdCompanyPolicyCategory->toArray();
        $this->assertArrayHasKey('id', $createdCompanyPolicyCategory);
        $this->assertNotNull($createdCompanyPolicyCategory['id'], 'Created CompanyPolicyCategory must have id specified');
        $this->assertNotNull(CompanyPolicyCategory::find($createdCompanyPolicyCategory['id']), 'CompanyPolicyCategory with given id must be in DB');
        $this->assertModelData($companyPolicyCategory, $createdCompanyPolicyCategory);
    }

    /**
     * @test read
     */
    public function testReadCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->makeCompanyPolicyCategory();
        $dbCompanyPolicyCategory = $this->companyPolicyCategoryRepo->find($companyPolicyCategory->id);
        $dbCompanyPolicyCategory = $dbCompanyPolicyCategory->toArray();
        $this->assertModelData($companyPolicyCategory->toArray(), $dbCompanyPolicyCategory);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->makeCompanyPolicyCategory();
        $fakeCompanyPolicyCategory = $this->fakeCompanyPolicyCategoryData();
        $updatedCompanyPolicyCategory = $this->companyPolicyCategoryRepo->update($fakeCompanyPolicyCategory, $companyPolicyCategory->id);
        $this->assertModelData($fakeCompanyPolicyCategory, $updatedCompanyPolicyCategory->toArray());
        $dbCompanyPolicyCategory = $this->companyPolicyCategoryRepo->find($companyPolicyCategory->id);
        $this->assertModelData($fakeCompanyPolicyCategory, $dbCompanyPolicyCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->makeCompanyPolicyCategory();
        $resp = $this->companyPolicyCategoryRepo->delete($companyPolicyCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyPolicyCategory::find($companyPolicyCategory->id), 'CompanyPolicyCategory should not exist in DB');
    }
}
