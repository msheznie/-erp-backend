<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveDocumentApprovedTrait;
use Tests\ApiTestTrait;

class LeaveDocumentApprovedApiTest extends TestCase
{
    use MakeLeaveDocumentApprovedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_document_approved()
    {
        $leaveDocumentApproved = $this->fakeLeaveDocumentApprovedData();
        $this->response = $this->json('POST', '/api/leaveDocumentApproveds', $leaveDocumentApproved);

        $this->assertApiResponse($leaveDocumentApproved);
    }

    /**
     * @test
     */
    public function test_read_leave_document_approved()
    {
        $leaveDocumentApproved = $this->makeLeaveDocumentApproved();
        $this->response = $this->json('GET', '/api/leaveDocumentApproveds/'.$leaveDocumentApproved->id);

        $this->assertApiResponse($leaveDocumentApproved->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_document_approved()
    {
        $leaveDocumentApproved = $this->makeLeaveDocumentApproved();
        $editedLeaveDocumentApproved = $this->fakeLeaveDocumentApprovedData();

        $this->response = $this->json('PUT', '/api/leaveDocumentApproveds/'.$leaveDocumentApproved->id, $editedLeaveDocumentApproved);

        $this->assertApiResponse($editedLeaveDocumentApproved);
    }

    /**
     * @test
     */
    public function test_delete_leave_document_approved()
    {
        $leaveDocumentApproved = $this->makeLeaveDocumentApproved();
        $this->response = $this->json('DELETE', '/api/leaveDocumentApproveds/'.$leaveDocumentApproved->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/leaveDocumentApproveds/'.$leaveDocumentApproved->id);

        $this->response->assertStatus(404);
    }
}
