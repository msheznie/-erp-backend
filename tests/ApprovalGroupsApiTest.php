<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApprovalGroupsApiTest extends TestCase
{
    use MakeApprovalGroupsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateApprovalGroups()
    {
        $approvalGroups = $this->fakeApprovalGroupsData();
        $this->json('POST', '/api/v1/approvalGroups', $approvalGroups);

        $this->assertApiResponse($approvalGroups);
    }

    /**
     * @test
     */
    public function testReadApprovalGroups()
    {
        $approvalGroups = $this->makeApprovalGroups();
        $this->json('GET', '/api/v1/approvalGroups/'.$approvalGroups->id);

        $this->assertApiResponse($approvalGroups->toArray());
    }

    /**
     * @test
     */
    public function testUpdateApprovalGroups()
    {
        $approvalGroups = $this->makeApprovalGroups();
        $editedApprovalGroups = $this->fakeApprovalGroupsData();

        $this->json('PUT', '/api/v1/approvalGroups/'.$approvalGroups->id, $editedApprovalGroups);

        $this->assertApiResponse($editedApprovalGroups);
    }

    /**
     * @test
     */
    public function testDeleteApprovalGroups()
    {
        $approvalGroups = $this->makeApprovalGroups();
        $this->json('DELETE', '/api/v1/approvalGroups/'.$approvalGroups->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/approvalGroups/'.$approvalGroups->id);

        $this->assertResponseStatus(404);
    }
}
