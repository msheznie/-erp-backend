<?php

use App\Models\CompanyPolicyMaster;
use App\Repositories\CompanyPolicyMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyPolicyMasterRepositoryTest extends TestCase
{
    use MakeCompanyPolicyMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyPolicyMasterRepository
     */
    protected $companyPolicyMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyPolicyMasterRepo = App::make(CompanyPolicyMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->fakeCompanyPolicyMasterData();
        $createdCompanyPolicyMaster = $this->companyPolicyMasterRepo->create($companyPolicyMaster);
        $createdCompanyPolicyMaster = $createdCompanyPolicyMaster->toArray();
        $this->assertArrayHasKey('id', $createdCompanyPolicyMaster);
        $this->assertNotNull($createdCompanyPolicyMaster['id'], 'Created CompanyPolicyMaster must have id specified');
        $this->assertNotNull(CompanyPolicyMaster::find($createdCompanyPolicyMaster['id']), 'CompanyPolicyMaster with given id must be in DB');
        $this->assertModelData($companyPolicyMaster, $createdCompanyPolicyMaster);
    }

    /**
     * @test read
     */
    public function testReadCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->makeCompanyPolicyMaster();
        $dbCompanyPolicyMaster = $this->companyPolicyMasterRepo->find($companyPolicyMaster->id);
        $dbCompanyPolicyMaster = $dbCompanyPolicyMaster->toArray();
        $this->assertModelData($companyPolicyMaster->toArray(), $dbCompanyPolicyMaster);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->makeCompanyPolicyMaster();
        $fakeCompanyPolicyMaster = $this->fakeCompanyPolicyMasterData();
        $updatedCompanyPolicyMaster = $this->companyPolicyMasterRepo->update($fakeCompanyPolicyMaster, $companyPolicyMaster->id);
        $this->assertModelData($fakeCompanyPolicyMaster, $updatedCompanyPolicyMaster->toArray());
        $dbCompanyPolicyMaster = $this->companyPolicyMasterRepo->find($companyPolicyMaster->id);
        $this->assertModelData($fakeCompanyPolicyMaster, $dbCompanyPolicyMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->makeCompanyPolicyMaster();
        $resp = $this->companyPolicyMasterRepo->delete($companyPolicyMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyPolicyMaster::find($companyPolicyMaster->id), 'CompanyPolicyMaster should not exist in DB');
    }
}
