<?php namespace Tests\Repositories;

use App\Models\ErpProjectMaster;
use App\Repositories\ErpProjectMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpProjectMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpProjectMasterRepository
     */
    protected $erpProjectMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpProjectMasterRepo = \App::make(ErpProjectMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->make()->toArray();

        $createdErpProjectMaster = $this->erpProjectMasterRepo->create($erpProjectMaster);

        $createdErpProjectMaster = $createdErpProjectMaster->toArray();
        $this->assertArrayHasKey('id', $createdErpProjectMaster);
        $this->assertNotNull($createdErpProjectMaster['id'], 'Created ErpProjectMaster must have id specified');
        $this->assertNotNull(ErpProjectMaster::find($createdErpProjectMaster['id']), 'ErpProjectMaster with given id must be in DB');
        $this->assertModelData($erpProjectMaster, $createdErpProjectMaster);
    }

    /**
     * @test read
     */
    public function test_read_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->create();

        $dbErpProjectMaster = $this->erpProjectMasterRepo->find($erpProjectMaster->id);

        $dbErpProjectMaster = $dbErpProjectMaster->toArray();
        $this->assertModelData($erpProjectMaster->toArray(), $dbErpProjectMaster);
    }

    /**
     * @test update
     */
    public function test_update_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->create();
        $fakeErpProjectMaster = factory(ErpProjectMaster::class)->make()->toArray();

        $updatedErpProjectMaster = $this->erpProjectMasterRepo->update($fakeErpProjectMaster, $erpProjectMaster->id);

        $this->assertModelData($fakeErpProjectMaster, $updatedErpProjectMaster->toArray());
        $dbErpProjectMaster = $this->erpProjectMasterRepo->find($erpProjectMaster->id);
        $this->assertModelData($fakeErpProjectMaster, $dbErpProjectMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->create();

        $resp = $this->erpProjectMasterRepo->delete($erpProjectMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpProjectMaster::find($erpProjectMaster->id), 'ErpProjectMaster should not exist in DB');
    }
}
