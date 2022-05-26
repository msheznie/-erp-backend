<?php namespace Tests\Repositories;

use App\Models\PortMaster;
use App\Repositories\PortMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PortMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PortMasterRepository
     */
    protected $portMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->portMasterRepo = \App::make(PortMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_port_master()
    {
        $portMaster = factory(PortMaster::class)->make()->toArray();

        $createdPortMaster = $this->portMasterRepo->create($portMaster);

        $createdPortMaster = $createdPortMaster->toArray();
        $this->assertArrayHasKey('id', $createdPortMaster);
        $this->assertNotNull($createdPortMaster['id'], 'Created PortMaster must have id specified');
        $this->assertNotNull(PortMaster::find($createdPortMaster['id']), 'PortMaster with given id must be in DB');
        $this->assertModelData($portMaster, $createdPortMaster);
    }

    /**
     * @test read
     */
    public function test_read_port_master()
    {
        $portMaster = factory(PortMaster::class)->create();

        $dbPortMaster = $this->portMasterRepo->find($portMaster->id);

        $dbPortMaster = $dbPortMaster->toArray();
        $this->assertModelData($portMaster->toArray(), $dbPortMaster);
    }

    /**
     * @test update
     */
    public function test_update_port_master()
    {
        $portMaster = factory(PortMaster::class)->create();
        $fakePortMaster = factory(PortMaster::class)->make()->toArray();

        $updatedPortMaster = $this->portMasterRepo->update($fakePortMaster, $portMaster->id);

        $this->assertModelData($fakePortMaster, $updatedPortMaster->toArray());
        $dbPortMaster = $this->portMasterRepo->find($portMaster->id);
        $this->assertModelData($fakePortMaster, $dbPortMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_port_master()
    {
        $portMaster = factory(PortMaster::class)->create();

        $resp = $this->portMasterRepo->delete($portMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(PortMaster::find($portMaster->id), 'PortMaster should not exist in DB');
    }
}
