<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApprovalRoleApiTest extends TestCase
{
    use MakeApprovalRoleTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateApprovalRole()
    {
        $approvalRole = $this->fakeApprovalRoleData();
        $this->json('POST', '/api/v1/approvalRoles', $approvalRole);

        $this->assertApiResponse($approvalRole);
    }

    /**
     * @test
     */
    public function testReadApprovalRole()
    {
        $approvalRole = $this->makeApprovalRole();
        $this->json('GET', '/api/v1/approvalRoles/'.$approvalRole->id);

        $this->assertApiResponse($approvalRole->toArray());
    }

    /**
     * @test
     */
    public function testUpdateApprovalRole()
    {
        $approvalRole = $this->makeApprovalRole();
        $editedApprovalRole = $this->fakeApprovalRoleData();

        $this->json('PUT', '/api/v1/approvalRoles/'.$approvalRole->id, $editedApprovalRole);

        $this->assertApiResponse($editedApprovalRole);
    }

    /**
     * @test
     */
    public function testDeleteApprovalRole()
    {
        $approvalRole = $this->makeApprovalRole();
        $this->json('DELETE', '/api/v1/approvalRoles/'.$approvalRole->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/approvalRoles/'.$approvalRole->id);

        $this->assertResponseStatus(404);
    }
}
