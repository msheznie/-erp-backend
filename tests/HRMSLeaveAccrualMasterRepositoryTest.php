<?php namespace Tests\Repositories;

use App\Models\HRMSLeaveAccrualMaster;
use App\Repositories\HRMSLeaveAccrualMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSLeaveAccrualMasterTrait;
use Tests\ApiTestTrait;

class HRMSLeaveAccrualMasterRepositoryTest extends TestCase
{
    use MakeHRMSLeaveAccrualMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSLeaveAccrualMasterRepository
     */
    protected $hRMSLeaveAccrualMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRMSLeaveAccrualMasterRepo = \App::make(HRMSLeaveAccrualMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->fakeHRMSLeaveAccrualMasterData();
        $createdHRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepo->create($hRMSLeaveAccrualMaster);
        $createdHRMSLeaveAccrualMaster = $createdHRMSLeaveAccrualMaster->toArray();
        $this->assertArrayHasKey('id', $createdHRMSLeaveAccrualMaster);
        $this->assertNotNull($createdHRMSLeaveAccrualMaster['id'], 'Created HRMSLeaveAccrualMaster must have id specified');
        $this->assertNotNull(HRMSLeaveAccrualMaster::find($createdHRMSLeaveAccrualMaster['id']), 'HRMSLeaveAccrualMaster with given id must be in DB');
        $this->assertModelData($hRMSLeaveAccrualMaster, $createdHRMSLeaveAccrualMaster);
    }

    /**
     * @test read
     */
    public function test_read_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->makeHRMSLeaveAccrualMaster();
        $dbHRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepo->find($hRMSLeaveAccrualMaster->id);
        $dbHRMSLeaveAccrualMaster = $dbHRMSLeaveAccrualMaster->toArray();
        $this->assertModelData($hRMSLeaveAccrualMaster->toArray(), $dbHRMSLeaveAccrualMaster);
    }

    /**
     * @test update
     */
    public function test_update_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->makeHRMSLeaveAccrualMaster();
        $fakeHRMSLeaveAccrualMaster = $this->fakeHRMSLeaveAccrualMasterData();
        $updatedHRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepo->update($fakeHRMSLeaveAccrualMaster, $hRMSLeaveAccrualMaster->id);
        $this->assertModelData($fakeHRMSLeaveAccrualMaster, $updatedHRMSLeaveAccrualMaster->toArray());
        $dbHRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepo->find($hRMSLeaveAccrualMaster->id);
        $this->assertModelData($fakeHRMSLeaveAccrualMaster, $dbHRMSLeaveAccrualMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->makeHRMSLeaveAccrualMaster();
        $resp = $this->hRMSLeaveAccrualMasterRepo->delete($hRMSLeaveAccrualMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSLeaveAccrualMaster::find($hRMSLeaveAccrualMaster->id), 'HRMSLeaveAccrualMaster should not exist in DB');
    }
}
