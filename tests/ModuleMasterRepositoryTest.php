<?php namespace Tests\Repositories;

use App\Models\ModuleMaster;
use App\Repositories\ModuleMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ModuleMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ModuleMasterRepository
     */
    protected $moduleMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->moduleMasterRepo = \App::make(ModuleMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->make()->toArray();

        $createdModuleMaster = $this->moduleMasterRepo->create($moduleMaster);

        $createdModuleMaster = $createdModuleMaster->toArray();
        $this->assertArrayHasKey('id', $createdModuleMaster);
        $this->assertNotNull($createdModuleMaster['id'], 'Created ModuleMaster must have id specified');
        $this->assertNotNull(ModuleMaster::find($createdModuleMaster['id']), 'ModuleMaster with given id must be in DB');
        $this->assertModelData($moduleMaster, $createdModuleMaster);
    }

    /**
     * @test read
     */
    public function test_read_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->create();

        $dbModuleMaster = $this->moduleMasterRepo->find($moduleMaster->id);

        $dbModuleMaster = $dbModuleMaster->toArray();
        $this->assertModelData($moduleMaster->toArray(), $dbModuleMaster);
    }

    /**
     * @test update
     */
    public function test_update_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->create();
        $fakeModuleMaster = factory(ModuleMaster::class)->make()->toArray();

        $updatedModuleMaster = $this->moduleMasterRepo->update($fakeModuleMaster, $moduleMaster->id);

        $this->assertModelData($fakeModuleMaster, $updatedModuleMaster->toArray());
        $dbModuleMaster = $this->moduleMasterRepo->find($moduleMaster->id);
        $this->assertModelData($fakeModuleMaster, $dbModuleMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->create();

        $resp = $this->moduleMasterRepo->delete($moduleMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ModuleMaster::find($moduleMaster->id), 'ModuleMaster should not exist in DB');
    }
}
