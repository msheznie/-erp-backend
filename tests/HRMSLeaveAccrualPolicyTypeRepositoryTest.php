<?php namespace Tests\Repositories;

use App\Models\HRMSLeaveAccrualPolicyType;
use App\Repositories\HRMSLeaveAccrualPolicyTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSLeaveAccrualPolicyTypeTrait;
use Tests\ApiTestTrait;

class HRMSLeaveAccrualPolicyTypeRepositoryTest extends TestCase
{
    use MakeHRMSLeaveAccrualPolicyTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSLeaveAccrualPolicyTypeRepository
     */
    protected $hRMSLeaveAccrualPolicyTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRMSLeaveAccrualPolicyTypeRepo = \App::make(HRMSLeaveAccrualPolicyTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->fakeHRMSLeaveAccrualPolicyTypeData();
        $createdHRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepo->create($hRMSLeaveAccrualPolicyType);
        $createdHRMSLeaveAccrualPolicyType = $createdHRMSLeaveAccrualPolicyType->toArray();
        $this->assertArrayHasKey('id', $createdHRMSLeaveAccrualPolicyType);
        $this->assertNotNull($createdHRMSLeaveAccrualPolicyType['id'], 'Created HRMSLeaveAccrualPolicyType must have id specified');
        $this->assertNotNull(HRMSLeaveAccrualPolicyType::find($createdHRMSLeaveAccrualPolicyType['id']), 'HRMSLeaveAccrualPolicyType with given id must be in DB');
        $this->assertModelData($hRMSLeaveAccrualPolicyType, $createdHRMSLeaveAccrualPolicyType);
    }

    /**
     * @test read
     */
    public function test_read_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->makeHRMSLeaveAccrualPolicyType();
        $dbHRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepo->find($hRMSLeaveAccrualPolicyType->id);
        $dbHRMSLeaveAccrualPolicyType = $dbHRMSLeaveAccrualPolicyType->toArray();
        $this->assertModelData($hRMSLeaveAccrualPolicyType->toArray(), $dbHRMSLeaveAccrualPolicyType);
    }

    /**
     * @test update
     */
    public function test_update_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->makeHRMSLeaveAccrualPolicyType();
        $fakeHRMSLeaveAccrualPolicyType = $this->fakeHRMSLeaveAccrualPolicyTypeData();
        $updatedHRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepo->update($fakeHRMSLeaveAccrualPolicyType, $hRMSLeaveAccrualPolicyType->id);
        $this->assertModelData($fakeHRMSLeaveAccrualPolicyType, $updatedHRMSLeaveAccrualPolicyType->toArray());
        $dbHRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepo->find($hRMSLeaveAccrualPolicyType->id);
        $this->assertModelData($fakeHRMSLeaveAccrualPolicyType, $dbHRMSLeaveAccrualPolicyType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->makeHRMSLeaveAccrualPolicyType();
        $resp = $this->hRMSLeaveAccrualPolicyTypeRepo->delete($hRMSLeaveAccrualPolicyType->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSLeaveAccrualPolicyType::find($hRMSLeaveAccrualPolicyType->id), 'HRMSLeaveAccrualPolicyType should not exist in DB');
    }
}
