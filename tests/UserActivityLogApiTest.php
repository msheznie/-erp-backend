<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeUserActivityLogTrait;
use Tests\ApiTestTrait;

class UserActivityLogApiTest extends TestCase
{
    use MakeUserActivityLogTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_user_activity_log()
    {
        $userActivityLog = $this->fakeUserActivityLogData();
        $this->response = $this->json('POST', '/api/userActivityLogs', $userActivityLog);

        $this->assertApiResponse($userActivityLog);
    }

    /**
     * @test
     */
    public function test_read_user_activity_log()
    {
        $userActivityLog = $this->makeUserActivityLog();
        $this->response = $this->json('GET', '/api/userActivityLogs/'.$userActivityLog->id);

        $this->assertApiResponse($userActivityLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_user_activity_log()
    {
        $userActivityLog = $this->makeUserActivityLog();
        $editedUserActivityLog = $this->fakeUserActivityLogData();

        $this->response = $this->json('PUT', '/api/userActivityLogs/'.$userActivityLog->id, $editedUserActivityLog);

        $this->assertApiResponse($editedUserActivityLog);
    }

    /**
     * @test
     */
    public function test_delete_user_activity_log()
    {
        $userActivityLog = $this->makeUserActivityLog();
        $this->response = $this->json('DELETE', '/api/userActivityLogs/'.$userActivityLog->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/userActivityLogs/'.$userActivityLog->id);

        $this->response->assertStatus(404);
    }
}
