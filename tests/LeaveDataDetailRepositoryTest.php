<?php namespace Tests\Repositories;

use App\Models\LeaveDataDetail;
use App\Repositories\LeaveDataDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveDataDetailTrait;
use Tests\ApiTestTrait;

class LeaveDataDetailRepositoryTest extends TestCase
{
    use MakeLeaveDataDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveDataDetailRepository
     */
    protected $leaveDataDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveDataDetailRepo = \App::make(LeaveDataDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_data_detail()
    {
        $leaveDataDetail = $this->fakeLeaveDataDetailData();
        $createdLeaveDataDetail = $this->leaveDataDetailRepo->create($leaveDataDetail);
        $createdLeaveDataDetail = $createdLeaveDataDetail->toArray();
        $this->assertArrayHasKey('id', $createdLeaveDataDetail);
        $this->assertNotNull($createdLeaveDataDetail['id'], 'Created LeaveDataDetail must have id specified');
        $this->assertNotNull(LeaveDataDetail::find($createdLeaveDataDetail['id']), 'LeaveDataDetail with given id must be in DB');
        $this->assertModelData($leaveDataDetail, $createdLeaveDataDetail);
    }

    /**
     * @test read
     */
    public function test_read_leave_data_detail()
    {
        $leaveDataDetail = $this->makeLeaveDataDetail();
        $dbLeaveDataDetail = $this->leaveDataDetailRepo->find($leaveDataDetail->id);
        $dbLeaveDataDetail = $dbLeaveDataDetail->toArray();
        $this->assertModelData($leaveDataDetail->toArray(), $dbLeaveDataDetail);
    }

    /**
     * @test update
     */
    public function test_update_leave_data_detail()
    {
        $leaveDataDetail = $this->makeLeaveDataDetail();
        $fakeLeaveDataDetail = $this->fakeLeaveDataDetailData();
        $updatedLeaveDataDetail = $this->leaveDataDetailRepo->update($fakeLeaveDataDetail, $leaveDataDetail->id);
        $this->assertModelData($fakeLeaveDataDetail, $updatedLeaveDataDetail->toArray());
        $dbLeaveDataDetail = $this->leaveDataDetailRepo->find($leaveDataDetail->id);
        $this->assertModelData($fakeLeaveDataDetail, $dbLeaveDataDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_data_detail()
    {
        $leaveDataDetail = $this->makeLeaveDataDetail();
        $resp = $this->leaveDataDetailRepo->delete($leaveDataDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(LeaveDataDetail::find($leaveDataDetail->id), 'LeaveDataDetail should not exist in DB');
    }
}
