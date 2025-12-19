<?php namespace Tests\Repositories;

use App\Models\Route;
use App\Repositories\RouteRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RouteRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RouteRepository
     */
    protected $routeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->routeRepo = \App::make(RouteRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_route()
    {
        $route = factory(Route::class)->make()->toArray();

        $createdRoute = $this->routeRepo->create($route);

        $createdRoute = $createdRoute->toArray();
        $this->assertArrayHasKey('id', $createdRoute);
        $this->assertNotNull($createdRoute['id'], 'Created Route must have id specified');
        $this->assertNotNull(Route::find($createdRoute['id']), 'Route with given id must be in DB');
        $this->assertModelData($route, $createdRoute);
    }

    /**
     * @test read
     */
    public function test_read_route()
    {
        $route = factory(Route::class)->create();

        $dbRoute = $this->routeRepo->find($route->id);

        $dbRoute = $dbRoute->toArray();
        $this->assertModelData($route->toArray(), $dbRoute);
    }

    /**
     * @test update
     */
    public function test_update_route()
    {
        $route = factory(Route::class)->create();
        $fakeRoute = factory(Route::class)->make()->toArray();

        $updatedRoute = $this->routeRepo->update($fakeRoute, $route->id);

        $this->assertModelData($fakeRoute, $updatedRoute->toArray());
        $dbRoute = $this->routeRepo->find($route->id);
        $this->assertModelData($fakeRoute, $dbRoute->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_route()
    {
        $route = factory(Route::class)->create();

        $resp = $this->routeRepo->delete($route->id);

        $this->assertTrue($resp);
        $this->assertNull(Route::find($route->id), 'Route should not exist in DB');
    }
}
