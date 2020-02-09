<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeUserRightsTrait;
use Tests\ApiTestTrait;

class UserRightsApiTest extends TestCase
{
    use MakeUserRightsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_user_rights()
    {
        $userRights = $this->fakeUserRightsData();
        $this->response = $this->json('POST', '/api/userRights', $userRights);

        $this->assertApiResponse($userRights);
    }

    /**
     * @test
     */
    public function test_read_user_rights()
    {
        $userRights = $this->makeUserRights();
        $this->response = $this->json('GET', '/api/userRights/'.$userRights->id);

        $this->assertApiResponse($userRights->toArray());
    }

    /**
     * @test
     */
    public function test_update_user_rights()
    {
        $userRights = $this->makeUserRights();
        $editedUserRights = $this->fakeUserRightsData();

        $this->response = $this->json('PUT', '/api/userRights/'.$userRights->id, $editedUserRights);

        $this->assertApiResponse($editedUserRights);
    }

    /**
     * @test
     */
    public function test_delete_user_rights()
    {
        $userRights = $this->makeUserRights();
        $this->response = $this->json('DELETE', '/api/userRights/'.$userRights->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/userRights/'.$userRights->id);

        $this->response->assertStatus(404);
    }
}
