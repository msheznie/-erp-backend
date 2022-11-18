<?php namespace Tests\Repositories;

use App\Models\NavigationRoute;
use App\Repositories\NavigationRouteRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class NavigationRouteRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var NavigationRouteRepository
     */
    protected $navigationRouteRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->navigationRouteRepo = \App::make(NavigationRouteRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->make()->toArray();

        $createdNavigationRoute = $this->navigationRouteRepo->create($navigationRoute);

        $createdNavigationRoute = $createdNavigationRoute->toArray();
        $this->assertArrayHasKey('id', $createdNavigationRoute);
        $this->assertNotNull($createdNavigationRoute['id'], 'Created NavigationRoute must have id specified');
        $this->assertNotNull(NavigationRoute::find($createdNavigationRoute['id']), 'NavigationRoute with given id must be in DB');
        $this->assertModelData($navigationRoute, $createdNavigationRoute);
    }

    /**
     * @test read
     */
    public function test_read_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->create();

        $dbNavigationRoute = $this->navigationRouteRepo->find($navigationRoute->id);

        $dbNavigationRoute = $dbNavigationRoute->toArray();
        $this->assertModelData($navigationRoute->toArray(), $dbNavigationRoute);
    }

    /**
     * @test update
     */
    public function test_update_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->create();
        $fakeNavigationRoute = factory(NavigationRoute::class)->make()->toArray();

        $updatedNavigationRoute = $this->navigationRouteRepo->update($fakeNavigationRoute, $navigationRoute->id);

        $this->assertModelData($fakeNavigationRoute, $updatedNavigationRoute->toArray());
        $dbNavigationRoute = $this->navigationRouteRepo->find($navigationRoute->id);
        $this->assertModelData($fakeNavigationRoute, $dbNavigationRoute->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_navigation_route()
    {
        $navigationRoute = factory(NavigationRoute::class)->create();

        $resp = $this->navigationRouteRepo->delete($navigationRoute->id);

        $this->assertTrue($resp);
        $this->assertNull(NavigationRoute::find($navigationRoute->id), 'NavigationRoute should not exist in DB');
    }
}
