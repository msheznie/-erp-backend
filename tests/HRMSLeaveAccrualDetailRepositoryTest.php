<?php namespace Tests\Repositories;

use App\Models\HRMSLeaveAccrualDetail;
use App\Repositories\HRMSLeaveAccrualDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSLeaveAccrualDetailTrait;
use Tests\ApiTestTrait;

class HRMSLeaveAccrualDetailRepositoryTest extends TestCase
{
    use MakeHRMSLeaveAccrualDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSLeaveAccrualDetailRepository
     */
    protected $hRMSLeaveAccrualDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRMSLeaveAccrualDetailRepo = \App::make(HRMSLeaveAccrualDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->fakeHRMSLeaveAccrualDetailData();
        $createdHRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepo->create($hRMSLeaveAccrualDetail);
        $createdHRMSLeaveAccrualDetail = $createdHRMSLeaveAccrualDetail->toArray();
        $this->assertArrayHasKey('id', $createdHRMSLeaveAccrualDetail);
        $this->assertNotNull($createdHRMSLeaveAccrualDetail['id'], 'Created HRMSLeaveAccrualDetail must have id specified');
        $this->assertNotNull(HRMSLeaveAccrualDetail::find($createdHRMSLeaveAccrualDetail['id']), 'HRMSLeaveAccrualDetail with given id must be in DB');
        $this->assertModelData($hRMSLeaveAccrualDetail, $createdHRMSLeaveAccrualDetail);
    }

    /**
     * @test read
     */
    public function test_read_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->makeHRMSLeaveAccrualDetail();
        $dbHRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepo->find($hRMSLeaveAccrualDetail->id);
        $dbHRMSLeaveAccrualDetail = $dbHRMSLeaveAccrualDetail->toArray();
        $this->assertModelData($hRMSLeaveAccrualDetail->toArray(), $dbHRMSLeaveAccrualDetail);
    }

    /**
     * @test update
     */
    public function test_update_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->makeHRMSLeaveAccrualDetail();
        $fakeHRMSLeaveAccrualDetail = $this->fakeHRMSLeaveAccrualDetailData();
        $updatedHRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepo->update($fakeHRMSLeaveAccrualDetail, $hRMSLeaveAccrualDetail->id);
        $this->assertModelData($fakeHRMSLeaveAccrualDetail, $updatedHRMSLeaveAccrualDetail->toArray());
        $dbHRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepo->find($hRMSLeaveAccrualDetail->id);
        $this->assertModelData($fakeHRMSLeaveAccrualDetail, $dbHRMSLeaveAccrualDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->makeHRMSLeaveAccrualDetail();
        $resp = $this->hRMSLeaveAccrualDetailRepo->delete($hRMSLeaveAccrualDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSLeaveAccrualDetail::find($hRMSLeaveAccrualDetail->id), 'HRMSLeaveAccrualDetail should not exist in DB');
    }
}
