<?php namespace Tests\Repositories;

use App\Models\SrmDepartmentMaster;
use App\Repositories\SrmDepartmentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrmDepartmentMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrmDepartmentMasterRepository
     */
    protected $srmDepartmentMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srmDepartmentMasterRepo = \App::make(SrmDepartmentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->make()->toArray();

        $createdSrmDepartmentMaster = $this->srmDepartmentMasterRepo->create($srmDepartmentMaster);

        $createdSrmDepartmentMaster = $createdSrmDepartmentMaster->toArray();
        $this->assertArrayHasKey('id', $createdSrmDepartmentMaster);
        $this->assertNotNull($createdSrmDepartmentMaster['id'], 'Created SrmDepartmentMaster must have id specified');
        $this->assertNotNull(SrmDepartmentMaster::find($createdSrmDepartmentMaster['id']), 'SrmDepartmentMaster with given id must be in DB');
        $this->assertModelData($srmDepartmentMaster, $createdSrmDepartmentMaster);
    }

    /**
     * @test read
     */
    public function test_read_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->create();

        $dbSrmDepartmentMaster = $this->srmDepartmentMasterRepo->find($srmDepartmentMaster->id);

        $dbSrmDepartmentMaster = $dbSrmDepartmentMaster->toArray();
        $this->assertModelData($srmDepartmentMaster->toArray(), $dbSrmDepartmentMaster);
    }

    /**
     * @test update
     */
    public function test_update_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->create();
        $fakeSrmDepartmentMaster = factory(SrmDepartmentMaster::class)->make()->toArray();

        $updatedSrmDepartmentMaster = $this->srmDepartmentMasterRepo->update($fakeSrmDepartmentMaster, $srmDepartmentMaster->id);

        $this->assertModelData($fakeSrmDepartmentMaster, $updatedSrmDepartmentMaster->toArray());
        $dbSrmDepartmentMaster = $this->srmDepartmentMasterRepo->find($srmDepartmentMaster->id);
        $this->assertModelData($fakeSrmDepartmentMaster, $dbSrmDepartmentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->create();

        $resp = $this->srmDepartmentMasterRepo->delete($srmDepartmentMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SrmDepartmentMaster::find($srmDepartmentMaster->id), 'SrmDepartmentMaster should not exist in DB');
    }
}
