<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersLogHistoryApiTest extends TestCase
{
    use MakeUsersLogHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUsersLogHistory()
    {
        $usersLogHistory = $this->fakeUsersLogHistoryData();
        $this->json('POST', '/api/v1/usersLogHistories', $usersLogHistory);

        $this->assertApiResponse($usersLogHistory);
    }

    /**
     * @test
     */
    public function testReadUsersLogHistory()
    {
        $usersLogHistory = $this->makeUsersLogHistory();
        $this->json('GET', '/api/v1/usersLogHistories/'.$usersLogHistory->id);

        $this->assertApiResponse($usersLogHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUsersLogHistory()
    {
        $usersLogHistory = $this->makeUsersLogHistory();
        $editedUsersLogHistory = $this->fakeUsersLogHistoryData();

        $this->json('PUT', '/api/v1/usersLogHistories/'.$usersLogHistory->id, $editedUsersLogHistory);

        $this->assertApiResponse($editedUsersLogHistory);
    }

    /**
     * @test
     */
    public function testDeleteUsersLogHistory()
    {
        $usersLogHistory = $this->makeUsersLogHistory();
        $this->json('DELETE', '/api/v1/usersLogHistories/'.$usersLogHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/usersLogHistories/'.$usersLogHistory->id);

        $this->assertResponseStatus(404);
    }
}
