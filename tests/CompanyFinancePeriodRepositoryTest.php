<?php

use App\Models\CompanyFinancePeriod;
use App\Repositories\CompanyFinancePeriodRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyFinancePeriodRepositoryTest extends TestCase
{
    use MakeCompanyFinancePeriodTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyFinancePeriodRepository
     */
    protected $companyFinancePeriodRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyFinancePeriodRepo = App::make(CompanyFinancePeriodRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->fakeCompanyFinancePeriodData();
        $createdCompanyFinancePeriod = $this->companyFinancePeriodRepo->create($companyFinancePeriod);
        $createdCompanyFinancePeriod = $createdCompanyFinancePeriod->toArray();
        $this->assertArrayHasKey('id', $createdCompanyFinancePeriod);
        $this->assertNotNull($createdCompanyFinancePeriod['id'], 'Created CompanyFinancePeriod must have id specified');
        $this->assertNotNull(CompanyFinancePeriod::find($createdCompanyFinancePeriod['id']), 'CompanyFinancePeriod with given id must be in DB');
        $this->assertModelData($companyFinancePeriod, $createdCompanyFinancePeriod);
    }

    /**
     * @test read
     */
    public function testReadCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->makeCompanyFinancePeriod();
        $dbCompanyFinancePeriod = $this->companyFinancePeriodRepo->find($companyFinancePeriod->id);
        $dbCompanyFinancePeriod = $dbCompanyFinancePeriod->toArray();
        $this->assertModelData($companyFinancePeriod->toArray(), $dbCompanyFinancePeriod);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->makeCompanyFinancePeriod();
        $fakeCompanyFinancePeriod = $this->fakeCompanyFinancePeriodData();
        $updatedCompanyFinancePeriod = $this->companyFinancePeriodRepo->update($fakeCompanyFinancePeriod, $companyFinancePeriod->id);
        $this->assertModelData($fakeCompanyFinancePeriod, $updatedCompanyFinancePeriod->toArray());
        $dbCompanyFinancePeriod = $this->companyFinancePeriodRepo->find($companyFinancePeriod->id);
        $this->assertModelData($fakeCompanyFinancePeriod, $dbCompanyFinancePeriod->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->makeCompanyFinancePeriod();
        $resp = $this->companyFinancePeriodRepo->delete($companyFinancePeriod->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyFinancePeriod::find($companyFinancePeriod->id), 'CompanyFinancePeriod should not exist in DB');
    }
}
