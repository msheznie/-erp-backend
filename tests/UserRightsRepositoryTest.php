<?php namespace Tests\Repositories;

use App\Models\UserRights;
use App\Repositories\UserRightsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeUserRightsTrait;
use Tests\ApiTestTrait;

class UserRightsRepositoryTest extends TestCase
{
    use MakeUserRightsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserRightsRepository
     */
    protected $userRightsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->userRightsRepo = \App::make(UserRightsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_user_rights()
    {
        $userRights = $this->fakeUserRightsData();
        $createdUserRights = $this->userRightsRepo->create($userRights);
        $createdUserRights = $createdUserRights->toArray();
        $this->assertArrayHasKey('id', $createdUserRights);
        $this->assertNotNull($createdUserRights['id'], 'Created UserRights must have id specified');
        $this->assertNotNull(UserRights::find($createdUserRights['id']), 'UserRights with given id must be in DB');
        $this->assertModelData($userRights, $createdUserRights);
    }

    /**
     * @test read
     */
    public function test_read_user_rights()
    {
        $userRights = $this->makeUserRights();
        $dbUserRights = $this->userRightsRepo->find($userRights->id);
        $dbUserRights = $dbUserRights->toArray();
        $this->assertModelData($userRights->toArray(), $dbUserRights);
    }

    /**
     * @test update
     */
    public function test_update_user_rights()
    {
        $userRights = $this->makeUserRights();
        $fakeUserRights = $this->fakeUserRightsData();
        $updatedUserRights = $this->userRightsRepo->update($fakeUserRights, $userRights->id);
        $this->assertModelData($fakeUserRights, $updatedUserRights->toArray());
        $dbUserRights = $this->userRightsRepo->find($userRights->id);
        $this->assertModelData($fakeUserRights, $dbUserRights->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_user_rights()
    {
        $userRights = $this->makeUserRights();
        $resp = $this->userRightsRepo->delete($userRights->id);
        $this->assertTrue($resp);
        $this->assertNull(UserRights::find($userRights->id), 'UserRights should not exist in DB');
    }
}
