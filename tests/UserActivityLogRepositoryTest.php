<?php namespace Tests\Repositories;

use App\Models\UserActivityLog;
use App\Repositories\UserActivityLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeUserActivityLogTrait;
use Tests\ApiTestTrait;

class UserActivityLogRepositoryTest extends TestCase
{
    use MakeUserActivityLogTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserActivityLogRepository
     */
    protected $userActivityLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->userActivityLogRepo = \App::make(UserActivityLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_user_activity_log()
    {
        $userActivityLog = $this->fakeUserActivityLogData();
        $createdUserActivityLog = $this->userActivityLogRepo->create($userActivityLog);
        $createdUserActivityLog = $createdUserActivityLog->toArray();
        $this->assertArrayHasKey('id', $createdUserActivityLog);
        $this->assertNotNull($createdUserActivityLog['id'], 'Created UserActivityLog must have id specified');
        $this->assertNotNull(UserActivityLog::find($createdUserActivityLog['id']), 'UserActivityLog with given id must be in DB');
        $this->assertModelData($userActivityLog, $createdUserActivityLog);
    }

    /**
     * @test read
     */
    public function test_read_user_activity_log()
    {
        $userActivityLog = $this->makeUserActivityLog();
        $dbUserActivityLog = $this->userActivityLogRepo->find($userActivityLog->id);
        $dbUserActivityLog = $dbUserActivityLog->toArray();
        $this->assertModelData($userActivityLog->toArray(), $dbUserActivityLog);
    }

    /**
     * @test update
     */
    public function test_update_user_activity_log()
    {
        $userActivityLog = $this->makeUserActivityLog();
        $fakeUserActivityLog = $this->fakeUserActivityLogData();
        $updatedUserActivityLog = $this->userActivityLogRepo->update($fakeUserActivityLog, $userActivityLog->id);
        $this->assertModelData($fakeUserActivityLog, $updatedUserActivityLog->toArray());
        $dbUserActivityLog = $this->userActivityLogRepo->find($userActivityLog->id);
        $this->assertModelData($fakeUserActivityLog, $dbUserActivityLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_user_activity_log()
    {
        $userActivityLog = $this->makeUserActivityLog();
        $resp = $this->userActivityLogRepo->delete($userActivityLog->id);
        $this->assertTrue($resp);
        $this->assertNull(UserActivityLog::find($userActivityLog->id), 'UserActivityLog should not exist in DB');
    }
}
