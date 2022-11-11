<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\NavigationRoute;

class NavigationRouteApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/navigation_routes', $navigationRoute
        );

        $this->assertApiResponse($navigationRoute);
    }

    /**
     * @test
     */
    public function test_read_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/navigation_routes/'.$navigationRoute->id
        );

        $this->assertApiResponse($navigationRoute->toArray());
    }

    /**
     * @test
     */
    public function test_update_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->create();
        $editedNavigationRoute = factory(NavigationRoute::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/navigation_routes/'.$navigationRoute->id,
            $editedNavigationRoute
        );

        $this->assertApiResponse($editedNavigationRoute);
    }

    /**
     * @test
     */
    public function test_delete_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/navigation_routes/'.$navigationRoute->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/navigation_routes/'.$navigationRoute->id
        );

        $this->response->assertStatus(404);
    }
}
