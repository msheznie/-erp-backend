<?php

use App\Models\UserGroupAssign;
use App\Repositories\UserGroupAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserGroupAssignRepositoryTest extends TestCase
{
    use MakeUserGroupAssignTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserGroupAssignRepository
     */
    protected $userGroupAssignRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userGroupAssignRepo = App::make(UserGroupAssignRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUserGroupAssign()
    {
        $userGroupAssign = $this->fakeUserGroupAssignData();
        $createdUserGroupAssign = $this->userGroupAssignRepo->create($userGroupAssign);
        $createdUserGroupAssign = $createdUserGroupAssign->toArray();
        $this->assertArrayHasKey('id', $createdUserGroupAssign);
        $this->assertNotNull($createdUserGroupAssign['id'], 'Created UserGroupAssign must have id specified');
        $this->assertNotNull(UserGroupAssign::find($createdUserGroupAssign['id']), 'UserGroupAssign with given id must be in DB');
        $this->assertModelData($userGroupAssign, $createdUserGroupAssign);
    }

    /**
     * @test read
     */
    public function testReadUserGroupAssign()
    {
        $userGroupAssign = $this->makeUserGroupAssign();
        $dbUserGroupAssign = $this->userGroupAssignRepo->find($userGroupAssign->id);
        $dbUserGroupAssign = $dbUserGroupAssign->toArray();
        $this->assertModelData($userGroupAssign->toArray(), $dbUserGroupAssign);
    }

    /**
     * @test update
     */
    public function testUpdateUserGroupAssign()
    {
        $userGroupAssign = $this->makeUserGroupAssign();
        $fakeUserGroupAssign = $this->fakeUserGroupAssignData();
        $updatedUserGroupAssign = $this->userGroupAssignRepo->update($fakeUserGroupAssign, $userGroupAssign->id);
        $this->assertModelData($fakeUserGroupAssign, $updatedUserGroupAssign->toArray());
        $dbUserGroupAssign = $this->userGroupAssignRepo->find($userGroupAssign->id);
        $this->assertModelData($fakeUserGroupAssign, $dbUserGroupAssign->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUserGroupAssign()
    {
        $userGroupAssign = $this->makeUserGroupAssign();
        $resp = $this->userGroupAssignRepo->delete($userGroupAssign->id);
        $this->assertTrue($resp);
        $this->assertNull(UserGroupAssign::find($userGroupAssign->id), 'UserGroupAssign should not exist in DB');
    }
}
