<?php namespace Tests\Repositories;

use App\Models\SubModuleMaster;
use App\Repositories\SubModuleMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SubModuleMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SubModuleMasterRepository
     */
    protected $subModuleMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->subModuleMasterRepo = \App::make(SubModuleMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->make()->toArray();

        $createdSubModuleMaster = $this->subModuleMasterRepo->create($subModuleMaster);

        $createdSubModuleMaster = $createdSubModuleMaster->toArray();
        $this->assertArrayHasKey('id', $createdSubModuleMaster);
        $this->assertNotNull($createdSubModuleMaster['id'], 'Created SubModuleMaster must have id specified');
        $this->assertNotNull(SubModuleMaster::find($createdSubModuleMaster['id']), 'SubModuleMaster with given id must be in DB');
        $this->assertModelData($subModuleMaster, $createdSubModuleMaster);
    }

    /**
     * @test read
     */
    public function test_read_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->create();

        $dbSubModuleMaster = $this->subModuleMasterRepo->find($subModuleMaster->id);

        $dbSubModuleMaster = $dbSubModuleMaster->toArray();
        $this->assertModelData($subModuleMaster->toArray(), $dbSubModuleMaster);
    }

    /**
     * @test update
     */
    public function test_update_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->create();
        $fakeSubModuleMaster = factory(SubModuleMaster::class)->make()->toArray();

        $updatedSubModuleMaster = $this->subModuleMasterRepo->update($fakeSubModuleMaster, $subModuleMaster->id);

        $this->assertModelData($fakeSubModuleMaster, $updatedSubModuleMaster->toArray());
        $dbSubModuleMaster = $this->subModuleMasterRepo->find($subModuleMaster->id);
        $this->assertModelData($fakeSubModuleMaster, $dbSubModuleMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->create();

        $resp = $this->subModuleMasterRepo->delete($subModuleMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SubModuleMaster::find($subModuleMaster->id), 'SubModuleMaster should not exist in DB');
    }
}
