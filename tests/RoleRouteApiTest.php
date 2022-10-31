<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RoleRoute;

class RoleRouteApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/role_routes', $roleRoute
        );

        $this->assertApiResponse($roleRoute);
    }

    /**
     * @test
     */
    public function test_read_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/role_routes/'.$roleRoute->id
        );

        $this->assertApiResponse($roleRoute->toArray());
    }

    /**
     * @test
     */
    public function test_update_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->create();
        $editedRoleRoute = factory(RoleRoute::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/role_routes/'.$roleRoute->id,
            $editedRoleRoute
        );

        $this->assertApiResponse($editedRoleRoute);
    }

    /**
     * @test
     */
    public function test_delete_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/role_routes/'.$roleRoute->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/role_routes/'.$roleRoute->id
        );

        $this->response->assertStatus(404);
    }
}
