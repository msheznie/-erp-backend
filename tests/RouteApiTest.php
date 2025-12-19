<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Route;

class RouteApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_route()
    {
        $route = factory(Route::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/routes', $route
        );

        $this->assertApiResponse($route);
    }

    /**
     * @test
     */
    public function test_read_route()
    {
        $route = factory(Route::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/routes/'.$route->id
        );

        $this->assertApiResponse($route->toArray());
    }

    /**
     * @test
     */
    public function test_update_route()
    {
        $route = factory(Route::class)->create();
        $editedRoute = factory(Route::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/routes/'.$route->id,
            $editedRoute
        );

        $this->assertApiResponse($editedRoute);
    }

    /**
     * @test
     */
    public function test_delete_route()
    {
        $route = factory(Route::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/routes/'.$route->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/routes/'.$route->id
        );

        $this->response->assertStatus(404);
    }
}
