<?php namespace Tests\Repositories;

use App\Models\LeaveAccrualDetail;
use App\Repositories\LeaveAccrualDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LeaveAccrualDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveAccrualDetailRepository
     */
    protected $leaveAccrualDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveAccrualDetailRepo = \App::make(LeaveAccrualDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->make()->toArray();

        $createdLeaveAccrualDetail = $this->leaveAccrualDetailRepo->create($leaveAccrualDetail);

        $createdLeaveAccrualDetail = $createdLeaveAccrualDetail->toArray();
        $this->assertArrayHasKey('id', $createdLeaveAccrualDetail);
        $this->assertNotNull($createdLeaveAccrualDetail['id'], 'Created LeaveAccrualDetail must have id specified');
        $this->assertNotNull(LeaveAccrualDetail::find($createdLeaveAccrualDetail['id']), 'LeaveAccrualDetail with given id must be in DB');
        $this->assertModelData($leaveAccrualDetail, $createdLeaveAccrualDetail);
    }

    /**
     * @test read
     */
    public function test_read_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->create();

        $dbLeaveAccrualDetail = $this->leaveAccrualDetailRepo->find($leaveAccrualDetail->id);

        $dbLeaveAccrualDetail = $dbLeaveAccrualDetail->toArray();
        $this->assertModelData($leaveAccrualDetail->toArray(), $dbLeaveAccrualDetail);
    }

    /**
     * @test update
     */
    public function test_update_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->create();
        $fakeLeaveAccrualDetail = factory(LeaveAccrualDetail::class)->make()->toArray();

        $updatedLeaveAccrualDetail = $this->leaveAccrualDetailRepo->update($fakeLeaveAccrualDetail, $leaveAccrualDetail->id);

        $this->assertModelData($fakeLeaveAccrualDetail, $updatedLeaveAccrualDetail->toArray());
        $dbLeaveAccrualDetail = $this->leaveAccrualDetailRepo->find($leaveAccrualDetail->id);
        $this->assertModelData($fakeLeaveAccrualDetail, $dbLeaveAccrualDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->create();

        $resp = $this->leaveAccrualDetailRepo->delete($leaveAccrualDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(LeaveAccrualDetail::find($leaveAccrualDetail->id), 'LeaveAccrualDetail should not exist in DB');
    }
}
