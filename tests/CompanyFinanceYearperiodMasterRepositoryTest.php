<?php

use App\Models\CompanyFinanceYearperiodMaster;
use App\Repositories\CompanyFinanceYearperiodMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyFinanceYearperiodMasterRepositoryTest extends TestCase
{
    use MakeCompanyFinanceYearperiodMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyFinanceYearperiodMasterRepository
     */
    protected $companyFinanceYearperiodMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyFinanceYearperiodMasterRepo = App::make(CompanyFinanceYearperiodMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->fakeCompanyFinanceYearperiodMasterData();
        $createdCompanyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepo->create($companyFinanceYearperiodMaster);
        $createdCompanyFinanceYearperiodMaster = $createdCompanyFinanceYearperiodMaster->toArray();
        $this->assertArrayHasKey('id', $createdCompanyFinanceYearperiodMaster);
        $this->assertNotNull($createdCompanyFinanceYearperiodMaster['id'], 'Created CompanyFinanceYearperiodMaster must have id specified');
        $this->assertNotNull(CompanyFinanceYearperiodMaster::find($createdCompanyFinanceYearperiodMaster['id']), 'CompanyFinanceYearperiodMaster with given id must be in DB');
        $this->assertModelData($companyFinanceYearperiodMaster, $createdCompanyFinanceYearperiodMaster);
    }

    /**
     * @test read
     */
    public function testReadCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->makeCompanyFinanceYearperiodMaster();
        $dbCompanyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepo->find($companyFinanceYearperiodMaster->id);
        $dbCompanyFinanceYearperiodMaster = $dbCompanyFinanceYearperiodMaster->toArray();
        $this->assertModelData($companyFinanceYearperiodMaster->toArray(), $dbCompanyFinanceYearperiodMaster);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->makeCompanyFinanceYearperiodMaster();
        $fakeCompanyFinanceYearperiodMaster = $this->fakeCompanyFinanceYearperiodMasterData();
        $updatedCompanyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepo->update($fakeCompanyFinanceYearperiodMaster, $companyFinanceYearperiodMaster->id);
        $this->assertModelData($fakeCompanyFinanceYearperiodMaster, $updatedCompanyFinanceYearperiodMaster->toArray());
        $dbCompanyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepo->find($companyFinanceYearperiodMaster->id);
        $this->assertModelData($fakeCompanyFinanceYearperiodMaster, $dbCompanyFinanceYearperiodMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->makeCompanyFinanceYearperiodMaster();
        $resp = $this->companyFinanceYearperiodMasterRepo->delete($companyFinanceYearperiodMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyFinanceYearperiodMaster::find($companyFinanceYearperiodMaster->id), 'CompanyFinanceYearperiodMaster should not exist in DB');
    }
}
