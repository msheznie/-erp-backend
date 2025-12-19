<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserGroupAssignApiTest extends TestCase
{
    use MakeUserGroupAssignTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUserGroupAssign()
    {
        $userGroupAssign = $this->fakeUserGroupAssignData();
        $this->json('POST', '/api/v1/userGroupAssigns', $userGroupAssign);

        $this->assertApiResponse($userGroupAssign);
    }

    /**
     * @test
     */
    public function testReadUserGroupAssign()
    {
        $userGroupAssign = $this->makeUserGroupAssign();
        $this->json('GET', '/api/v1/userGroupAssigns/'.$userGroupAssign->id);

        $this->assertApiResponse($userGroupAssign->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUserGroupAssign()
    {
        $userGroupAssign = $this->makeUserGroupAssign();
        $editedUserGroupAssign = $this->fakeUserGroupAssignData();

        $this->json('PUT', '/api/v1/userGroupAssigns/'.$userGroupAssign->id, $editedUserGroupAssign);

        $this->assertApiResponse($editedUserGroupAssign);
    }

    /**
     * @test
     */
    public function testDeleteUserGroupAssign()
    {
        $userGroupAssign = $this->makeUserGroupAssign();
        $this->json('DELETE', '/api/v1/userGroupAssigns/'.$userGroupAssign->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/userGroupAssigns/'.$userGroupAssign->id);

        $this->assertResponseStatus(404);
    }
}
