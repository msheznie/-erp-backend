<?php namespace Tests\Repositories;

use App\Models\ModuleAssigned;
use App\Repositories\ModuleAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ModuleAssignedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ModuleAssignedRepository
     */
    protected $moduleAssignedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->moduleAssignedRepo = \App::make(ModuleAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->make()->toArray();

        $createdModuleAssigned = $this->moduleAssignedRepo->create($moduleAssigned);

        $createdModuleAssigned = $createdModuleAssigned->toArray();
        $this->assertArrayHasKey('id', $createdModuleAssigned);
        $this->assertNotNull($createdModuleAssigned['id'], 'Created ModuleAssigned must have id specified');
        $this->assertNotNull(ModuleAssigned::find($createdModuleAssigned['id']), 'ModuleAssigned with given id must be in DB');
        $this->assertModelData($moduleAssigned, $createdModuleAssigned);
    }

    /**
     * @test read
     */
    public function test_read_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->create();

        $dbModuleAssigned = $this->moduleAssignedRepo->find($moduleAssigned->id);

        $dbModuleAssigned = $dbModuleAssigned->toArray();
        $this->assertModelData($moduleAssigned->toArray(), $dbModuleAssigned);
    }

    /**
     * @test update
     */
    public function test_update_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->create();
        $fakeModuleAssigned = factory(ModuleAssigned::class)->make()->toArray();

        $updatedModuleAssigned = $this->moduleAssignedRepo->update($fakeModuleAssigned, $moduleAssigned->id);

        $this->assertModelData($fakeModuleAssigned, $updatedModuleAssigned->toArray());
        $dbModuleAssigned = $this->moduleAssignedRepo->find($moduleAssigned->id);
        $this->assertModelData($fakeModuleAssigned, $dbModuleAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->create();

        $resp = $this->moduleAssignedRepo->delete($moduleAssigned->id);

        $this->assertTrue($resp);
        $this->assertNull(ModuleAssigned::find($moduleAssigned->id), 'ModuleAssigned should not exist in DB');
    }
}
