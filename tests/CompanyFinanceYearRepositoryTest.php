<?php

use App\Models\CompanyFinanceYear;
use App\Repositories\CompanyFinanceYearRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyFinanceYearRepositoryTest extends TestCase
{
    use MakeCompanyFinanceYearTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyFinanceYearRepository
     */
    protected $companyFinanceYearRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyFinanceYearRepo = App::make(CompanyFinanceYearRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyFinanceYear()
    {
        $companyFinanceYear = $this->fakeCompanyFinanceYearData();
        $createdCompanyFinanceYear = $this->companyFinanceYearRepo->create($companyFinanceYear);
        $createdCompanyFinanceYear = $createdCompanyFinanceYear->toArray();
        $this->assertArrayHasKey('id', $createdCompanyFinanceYear);
        $this->assertNotNull($createdCompanyFinanceYear['id'], 'Created CompanyFinanceYear must have id specified');
        $this->assertNotNull(CompanyFinanceYear::find($createdCompanyFinanceYear['id']), 'CompanyFinanceYear with given id must be in DB');
        $this->assertModelData($companyFinanceYear, $createdCompanyFinanceYear);
    }

    /**
     * @test read
     */
    public function testReadCompanyFinanceYear()
    {
        $companyFinanceYear = $this->makeCompanyFinanceYear();
        $dbCompanyFinanceYear = $this->companyFinanceYearRepo->find($companyFinanceYear->id);
        $dbCompanyFinanceYear = $dbCompanyFinanceYear->toArray();
        $this->assertModelData($companyFinanceYear->toArray(), $dbCompanyFinanceYear);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyFinanceYear()
    {
        $companyFinanceYear = $this->makeCompanyFinanceYear();
        $fakeCompanyFinanceYear = $this->fakeCompanyFinanceYearData();
        $updatedCompanyFinanceYear = $this->companyFinanceYearRepo->update($fakeCompanyFinanceYear, $companyFinanceYear->id);
        $this->assertModelData($fakeCompanyFinanceYear, $updatedCompanyFinanceYear->toArray());
        $dbCompanyFinanceYear = $this->companyFinanceYearRepo->find($companyFinanceYear->id);
        $this->assertModelData($fakeCompanyFinanceYear, $dbCompanyFinanceYear->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyFinanceYear()
    {
        $companyFinanceYear = $this->makeCompanyFinanceYear();
        $resp = $this->companyFinanceYearRepo->delete($companyFinanceYear->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyFinanceYear::find($companyFinanceYear->id), 'CompanyFinanceYear should not exist in DB');
    }
}
