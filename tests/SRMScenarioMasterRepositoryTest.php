<?php namespace Tests\Repositories;

use App\Models\SRMScenarioMaster;
use App\Repositories\SRMScenarioMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMScenarioMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMScenarioMasterRepository
     */
    protected $sRMScenarioMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMScenarioMasterRepo = \App::make(SRMScenarioMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->make()->toArray();

        $createdSRMScenarioMaster = $this->sRMScenarioMasterRepo->create($sRMScenarioMaster);

        $createdSRMScenarioMaster = $createdSRMScenarioMaster->toArray();
        $this->assertArrayHasKey('id', $createdSRMScenarioMaster);
        $this->assertNotNull($createdSRMScenarioMaster['id'], 'Created SRMScenarioMaster must have id specified');
        $this->assertNotNull(SRMScenarioMaster::find($createdSRMScenarioMaster['id']), 'SRMScenarioMaster with given id must be in DB');
        $this->assertModelData($sRMScenarioMaster, $createdSRMScenarioMaster);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->create();

        $dbSRMScenarioMaster = $this->sRMScenarioMasterRepo->find($sRMScenarioMaster->id);

        $dbSRMScenarioMaster = $dbSRMScenarioMaster->toArray();
        $this->assertModelData($sRMScenarioMaster->toArray(), $dbSRMScenarioMaster);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->create();
        $fakeSRMScenarioMaster = factory(SRMScenarioMaster::class)->make()->toArray();

        $updatedSRMScenarioMaster = $this->sRMScenarioMasterRepo->update($fakeSRMScenarioMaster, $sRMScenarioMaster->id);

        $this->assertModelData($fakeSRMScenarioMaster, $updatedSRMScenarioMaster->toArray());
        $dbSRMScenarioMaster = $this->sRMScenarioMasterRepo->find($sRMScenarioMaster->id);
        $this->assertModelData($fakeSRMScenarioMaster, $dbSRMScenarioMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->create();

        $resp = $this->sRMScenarioMasterRepo->delete($sRMScenarioMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMScenarioMaster::find($sRMScenarioMaster->id), 'SRMScenarioMaster should not exist in DB');
    }
}
