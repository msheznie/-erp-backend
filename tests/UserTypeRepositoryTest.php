<?php namespace Tests\Repositories;

use App\Models\UserType;
use App\Repositories\UserTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class UserTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserTypeRepository
     */
    protected $userTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->userTypeRepo = \App::make(UserTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_user_type()
    {
        $userType = factory(UserType::class)->make()->toArray();

        $createdUserType = $this->userTypeRepo->create($userType);

        $createdUserType = $createdUserType->toArray();
        $this->assertArrayHasKey('id', $createdUserType);
        $this->assertNotNull($createdUserType['id'], 'Created UserType must have id specified');
        $this->assertNotNull(UserType::find($createdUserType['id']), 'UserType with given id must be in DB');
        $this->assertModelData($userType, $createdUserType);
    }

    /**
     * @test read
     */
    public function test_read_user_type()
    {
        $userType = factory(UserType::class)->create();

        $dbUserType = $this->userTypeRepo->find($userType->id);

        $dbUserType = $dbUserType->toArray();
        $this->assertModelData($userType->toArray(), $dbUserType);
    }

    /**
     * @test update
     */
    public function test_update_user_type()
    {
        $userType = factory(UserType::class)->create();
        $fakeUserType = factory(UserType::class)->make()->toArray();

        $updatedUserType = $this->userTypeRepo->update($fakeUserType, $userType->id);

        $this->assertModelData($fakeUserType, $updatedUserType->toArray());
        $dbUserType = $this->userTypeRepo->find($userType->id);
        $this->assertModelData($fakeUserType, $dbUserType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_user_type()
    {
        $userType = factory(UserType::class)->create();

        $resp = $this->userTypeRepo->delete($userType->id);

        $this->assertTrue($resp);
        $this->assertNull(UserType::find($userType->id), 'UserType should not exist in DB');
    }
}
