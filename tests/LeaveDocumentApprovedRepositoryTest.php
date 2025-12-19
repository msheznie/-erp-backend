<?php namespace Tests\Repositories;

use App\Models\LeaveDocumentApproved;
use App\Repositories\LeaveDocumentApprovedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveDocumentApprovedTrait;
use Tests\ApiTestTrait;

class LeaveDocumentApprovedRepositoryTest extends TestCase
{
    use MakeLeaveDocumentApprovedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveDocumentApprovedRepository
     */
    protected $leaveDocumentApprovedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveDocumentApprovedRepo = \App::make(LeaveDocumentApprovedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_document_approved()
    {
        $leaveDocumentApproved = $this->fakeLeaveDocumentApprovedData();
        $createdLeaveDocumentApproved = $this->leaveDocumentApprovedRepo->create($leaveDocumentApproved);
        $createdLeaveDocumentApproved = $createdLeaveDocumentApproved->toArray();
        $this->assertArrayHasKey('id', $createdLeaveDocumentApproved);
        $this->assertNotNull($createdLeaveDocumentApproved['id'], 'Created LeaveDocumentApproved must have id specified');
        $this->assertNotNull(LeaveDocumentApproved::find($createdLeaveDocumentApproved['id']), 'LeaveDocumentApproved with given id must be in DB');
        $this->assertModelData($leaveDocumentApproved, $createdLeaveDocumentApproved);
    }

    /**
     * @test read
     */
    public function test_read_leave_document_approved()
    {
        $leaveDocumentApproved = $this->makeLeaveDocumentApproved();
        $dbLeaveDocumentApproved = $this->leaveDocumentApprovedRepo->find($leaveDocumentApproved->id);
        $dbLeaveDocumentApproved = $dbLeaveDocumentApproved->toArray();
        $this->assertModelData($leaveDocumentApproved->toArray(), $dbLeaveDocumentApproved);
    }

    /**
     * @test update
     */
    public function test_update_leave_document_approved()
    {
        $leaveDocumentApproved = $this->makeLeaveDocumentApproved();
        $fakeLeaveDocumentApproved = $this->fakeLeaveDocumentApprovedData();
        $updatedLeaveDocumentApproved = $this->leaveDocumentApprovedRepo->update($fakeLeaveDocumentApproved, $leaveDocumentApproved->id);
        $this->assertModelData($fakeLeaveDocumentApproved, $updatedLeaveDocumentApproved->toArray());
        $dbLeaveDocumentApproved = $this->leaveDocumentApprovedRepo->find($leaveDocumentApproved->id);
        $this->assertModelData($fakeLeaveDocumentApproved, $dbLeaveDocumentApproved->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_document_approved()
    {
        $leaveDocumentApproved = $this->makeLeaveDocumentApproved();
        $resp = $this->leaveDocumentApprovedRepo->delete($leaveDocumentApproved->id);
        $this->assertTrue($resp);
        $this->assertNull(LeaveDocumentApproved::find($leaveDocumentApproved->id), 'LeaveDocumentApproved should not exist in DB');
    }
}
