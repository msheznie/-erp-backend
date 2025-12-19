<?php namespace Tests\Repositories;

use App\Models\HRMSPeriodMaster;
use App\Repositories\HRMSPeriodMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSPeriodMasterTrait;
use Tests\ApiTestTrait;

class HRMSPeriodMasterRepositoryTest extends TestCase
{
    use MakeHRMSPeriodMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSPeriodMasterRepository
     */
    protected $hRMSPeriodMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRMSPeriodMasterRepo = \App::make(HRMSPeriodMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->fakeHRMSPeriodMasterData();
        $createdHRMSPeriodMaster = $this->hRMSPeriodMasterRepo->create($hRMSPeriodMaster);
        $createdHRMSPeriodMaster = $createdHRMSPeriodMaster->toArray();
        $this->assertArrayHasKey('id', $createdHRMSPeriodMaster);
        $this->assertNotNull($createdHRMSPeriodMaster['id'], 'Created HRMSPeriodMaster must have id specified');
        $this->assertNotNull(HRMSPeriodMaster::find($createdHRMSPeriodMaster['id']), 'HRMSPeriodMaster with given id must be in DB');
        $this->assertModelData($hRMSPeriodMaster, $createdHRMSPeriodMaster);
    }

    /**
     * @test read
     */
    public function test_read_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->makeHRMSPeriodMaster();
        $dbHRMSPeriodMaster = $this->hRMSPeriodMasterRepo->find($hRMSPeriodMaster->id);
        $dbHRMSPeriodMaster = $dbHRMSPeriodMaster->toArray();
        $this->assertModelData($hRMSPeriodMaster->toArray(), $dbHRMSPeriodMaster);
    }

    /**
     * @test update
     */
    public function test_update_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->makeHRMSPeriodMaster();
        $fakeHRMSPeriodMaster = $this->fakeHRMSPeriodMasterData();
        $updatedHRMSPeriodMaster = $this->hRMSPeriodMasterRepo->update($fakeHRMSPeriodMaster, $hRMSPeriodMaster->id);
        $this->assertModelData($fakeHRMSPeriodMaster, $updatedHRMSPeriodMaster->toArray());
        $dbHRMSPeriodMaster = $this->hRMSPeriodMasterRepo->find($hRMSPeriodMaster->id);
        $this->assertModelData($fakeHRMSPeriodMaster, $dbHRMSPeriodMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->makeHRMSPeriodMaster();
        $resp = $this->hRMSPeriodMasterRepo->delete($hRMSPeriodMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSPeriodMaster::find($hRMSPeriodMaster->id), 'HRMSPeriodMaster should not exist in DB');
    }
}
