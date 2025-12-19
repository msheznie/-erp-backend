<?php

use App\Models\UsersLogHistory;
use App\Repositories\UsersLogHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersLogHistoryRepositoryTest extends TestCase
{
    use MakeUsersLogHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UsersLogHistoryRepository
     */
    protected $usersLogHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->usersLogHistoryRepo = App::make(UsersLogHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUsersLogHistory()
    {
        $usersLogHistory = $this->fakeUsersLogHistoryData();
        $createdUsersLogHistory = $this->usersLogHistoryRepo->create($usersLogHistory);
        $createdUsersLogHistory = $createdUsersLogHistory->toArray();
        $this->assertArrayHasKey('id', $createdUsersLogHistory);
        $this->assertNotNull($createdUsersLogHistory['id'], 'Created UsersLogHistory must have id specified');
        $this->assertNotNull(UsersLogHistory::find($createdUsersLogHistory['id']), 'UsersLogHistory with given id must be in DB');
        $this->assertModelData($usersLogHistory, $createdUsersLogHistory);
    }

    /**
     * @test read
     */
    public function testReadUsersLogHistory()
    {
        $usersLogHistory = $this->makeUsersLogHistory();
        $dbUsersLogHistory = $this->usersLogHistoryRepo->find($usersLogHistory->id);
        $dbUsersLogHistory = $dbUsersLogHistory->toArray();
        $this->assertModelData($usersLogHistory->toArray(), $dbUsersLogHistory);
    }

    /**
     * @test update
     */
    public function testUpdateUsersLogHistory()
    {
        $usersLogHistory = $this->makeUsersLogHistory();
        $fakeUsersLogHistory = $this->fakeUsersLogHistoryData();
        $updatedUsersLogHistory = $this->usersLogHistoryRepo->update($fakeUsersLogHistory, $usersLogHistory->id);
        $this->assertModelData($fakeUsersLogHistory, $updatedUsersLogHistory->toArray());
        $dbUsersLogHistory = $this->usersLogHistoryRepo->find($usersLogHistory->id);
        $this->assertModelData($fakeUsersLogHistory, $dbUsersLogHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUsersLogHistory()
    {
        $usersLogHistory = $this->makeUsersLogHistory();
        $resp = $this->usersLogHistoryRepo->delete($usersLogHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(UsersLogHistory::find($usersLogHistory->id), 'UsersLogHistory should not exist in DB');
    }
}
