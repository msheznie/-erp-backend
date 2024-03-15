<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\UserType;

class UserTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_user_type()
    {
        $userType = factory(UserType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/user_types', $userType
        );

        $this->assertApiResponse($userType);
    }

    /**
     * @test
     */
    public function test_read_user_type()
    {
        $userType = factory(UserType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/user_types/'.$userType->id
        );

        $this->assertApiResponse($userType->toArray());
    }

    /**
     * @test
     */
    public function test_update_user_type()
    {
        $userType = factory(UserType::class)->create();
        $editedUserType = factory(UserType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/user_types/'.$userType->id,
            $editedUserType
        );

        $this->assertApiResponse($editedUserType);
    }

    /**
     * @test
     */
    public function test_delete_user_type()
    {
        $userType = factory(UserType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/user_types/'.$userType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/user_types/'.$userType->id
        );

        $this->response->assertStatus(404);
    }
}
