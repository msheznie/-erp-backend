<?php namespace Tests\Repositories;

use App\Models\SMECompanyPolicyMaster;
use App\Repositories\SMECompanyPolicyMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMECompanyPolicyMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMECompanyPolicyMasterRepository
     */
    protected $sMECompanyPolicyMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMECompanyPolicyMasterRepo = \App::make(SMECompanyPolicyMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->make()->toArray();

        $createdSMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepo->create($sMECompanyPolicyMaster);

        $createdSMECompanyPolicyMaster = $createdSMECompanyPolicyMaster->toArray();
        $this->assertArrayHasKey('id', $createdSMECompanyPolicyMaster);
        $this->assertNotNull($createdSMECompanyPolicyMaster['id'], 'Created SMECompanyPolicyMaster must have id specified');
        $this->assertNotNull(SMECompanyPolicyMaster::find($createdSMECompanyPolicyMaster['id']), 'SMECompanyPolicyMaster with given id must be in DB');
        $this->assertModelData($sMECompanyPolicyMaster, $createdSMECompanyPolicyMaster);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->create();

        $dbSMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepo->find($sMECompanyPolicyMaster->id);

        $dbSMECompanyPolicyMaster = $dbSMECompanyPolicyMaster->toArray();
        $this->assertModelData($sMECompanyPolicyMaster->toArray(), $dbSMECompanyPolicyMaster);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->create();
        $fakeSMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->make()->toArray();

        $updatedSMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepo->update($fakeSMECompanyPolicyMaster, $sMECompanyPolicyMaster->id);

        $this->assertModelData($fakeSMECompanyPolicyMaster, $updatedSMECompanyPolicyMaster->toArray());
        $dbSMECompanyPolicyMaster = $this->sMECompanyPolicyMasterRepo->find($sMECompanyPolicyMaster->id);
        $this->assertModelData($fakeSMECompanyPolicyMaster, $dbSMECompanyPolicyMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->create();

        $resp = $this->sMECompanyPolicyMasterRepo->delete($sMECompanyPolicyMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SMECompanyPolicyMaster::find($sMECompanyPolicyMaster->id), 'SMECompanyPolicyMaster should not exist in DB');
    }
}
