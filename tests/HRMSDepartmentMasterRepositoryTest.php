<?php namespace Tests\Repositories;

use App\Models\HrmsDepartmentMaster;
use App\Repositories\HrmsDepartmentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHrmsDepartmentMasterTrait;
use Tests\ApiTestTrait;

class HrmsDepartmentMasterRepositoryTest extends TestCase
{
    use MakeHrmsDepartmentMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrmsDepartmentMasterRepository
     */
    protected $hrmsDepartmentMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrmsDepartmentMasterRepo = \App::make(HrmsDepartmentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->fakeHrmsDepartmentMasterData();
        $createdHrmsDepartmentMaster = $this->hrmsDepartmentMasterRepo->create($hrmsDepartmentMaster);
        $createdHrmsDepartmentMaster = $createdHrmsDepartmentMaster->toArray();
        $this->assertArrayHasKey('id', $createdHrmsDepartmentMaster);
        $this->assertNotNull($createdHrmsDepartmentMaster['id'], 'Created HrmsDepartmentMaster must have id specified');
        $this->assertNotNull(HrmsDepartmentMaster::find($createdHrmsDepartmentMaster['id']), 'HrmsDepartmentMaster with given id must be in DB');
        $this->assertModelData($hrmsDepartmentMaster, $createdHrmsDepartmentMaster);
    }

    /**
     * @test read
     */
    public function test_read_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->makeHrmsDepartmentMaster();
        $dbHrmsDepartmentMaster = $this->hrmsDepartmentMasterRepo->find($hrmsDepartmentMaster->id);
        $dbHrmsDepartmentMaster = $dbHrmsDepartmentMaster->toArray();
        $this->assertModelData($hrmsDepartmentMaster->toArray(), $dbHrmsDepartmentMaster);
    }

    /**
     * @test update
     */
    public function test_update_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->makeHrmsDepartmentMaster();
        $fakeHrmsDepartmentMaster = $this->fakeHrmsDepartmentMasterData();
        $updatedHrmsDepartmentMaster = $this->hrmsDepartmentMasterRepo->update($fakeHrmsDepartmentMaster, $hrmsDepartmentMaster->id);
        $this->assertModelData($fakeHrmsDepartmentMaster, $updatedHrmsDepartmentMaster->toArray());
        $dbHrmsDepartmentMaster = $this->hrmsDepartmentMasterRepo->find($hrmsDepartmentMaster->id);
        $this->assertModelData($fakeHrmsDepartmentMaster, $dbHrmsDepartmentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->makeHrmsDepartmentMaster();
        $resp = $this->hrmsDepartmentMasterRepo->delete($hrmsDepartmentMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(HrmsDepartmentMaster::find($hrmsDepartmentMaster->id), 'HrmsDepartmentMaster should not exist in DB');
    }
}
