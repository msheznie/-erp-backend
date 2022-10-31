<?php namespace Tests\Repositories;

use App\Models\RoleRoute;
use App\Repositories\RoleRouteRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RoleRouteRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RoleRouteRepository
     */
    protected $roleRouteRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->roleRouteRepo = \App::make(RoleRouteRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->make()->toArray();

        $createdRoleRoute = $this->roleRouteRepo->create($roleRoute);

        $createdRoleRoute = $createdRoleRoute->toArray();
        $this->assertArrayHasKey('id', $createdRoleRoute);
        $this->assertNotNull($createdRoleRoute['id'], 'Created RoleRoute must have id specified');
        $this->assertNotNull(RoleRoute::find($createdRoleRoute['id']), 'RoleRoute with given id must be in DB');
        $this->assertModelData($roleRoute, $createdRoleRoute);
    }

    /**
     * @test read
     */
    public function test_read_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->create();

        $dbRoleRoute = $this->roleRouteRepo->find($roleRoute->id);

        $dbRoleRoute = $dbRoleRoute->toArray();
        $this->assertModelData($roleRoute->toArray(), $dbRoleRoute);
    }

    /**
     * @test update
     */
    public function test_update_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->create();
        $fakeRoleRoute = factory(RoleRoute::class)->make()->toArray();

        $updatedRoleRoute = $this->roleRouteRepo->update($fakeRoleRoute, $roleRoute->id);

        $this->assertModelData($fakeRoleRoute, $updatedRoleRoute->toArray());
        $dbRoleRoute = $this->roleRouteRepo->find($roleRoute->id);
        $this->assertModelData($fakeRoleRoute, $dbRoleRoute->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_role_route()
    {
        $roleRoute = factory(RoleRoute::class)->create();

        $resp = $this->roleRouteRepo->delete($roleRoute->id);

        $this->assertTrue($resp);
        $this->assertNull(RoleRoute::find($roleRoute->id), 'RoleRoute should not exist in DB');
    }
}
