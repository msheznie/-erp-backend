<?php

use App\Models\DepartmentMaster;
use App\Repositories\DepartmentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepartmentMasterRepositoryTest extends TestCase
{
    use MakeDepartmentMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepartmentMasterRepository
     */
    protected $departmentMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->departmentMasterRepo = App::make(DepartmentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDepartmentMaster()
    {
        $departmentMaster = $this->fakeDepartmentMasterData();
        $createdDepartmentMaster = $this->departmentMasterRepo->create($departmentMaster);
        $createdDepartmentMaster = $createdDepartmentMaster->toArray();
        $this->assertArrayHasKey('id', $createdDepartmentMaster);
        $this->assertNotNull($createdDepartmentMaster['id'], 'Created DepartmentMaster must have id specified');
        $this->assertNotNull(DepartmentMaster::find($createdDepartmentMaster['id']), 'DepartmentMaster with given id must be in DB');
        $this->assertModelData($departmentMaster, $createdDepartmentMaster);
    }

    /**
     * @test read
     */
    public function testReadDepartmentMaster()
    {
        $departmentMaster = $this->makeDepartmentMaster();
        $dbDepartmentMaster = $this->departmentMasterRepo->find($departmentMaster->id);
        $dbDepartmentMaster = $dbDepartmentMaster->toArray();
        $this->assertModelData($departmentMaster->toArray(), $dbDepartmentMaster);
    }

    /**
     * @test update
     */
    public function testUpdateDepartmentMaster()
    {
        $departmentMaster = $this->makeDepartmentMaster();
        $fakeDepartmentMaster = $this->fakeDepartmentMasterData();
        $updatedDepartmentMaster = $this->departmentMasterRepo->update($fakeDepartmentMaster, $departmentMaster->id);
        $this->assertModelData($fakeDepartmentMaster, $updatedDepartmentMaster->toArray());
        $dbDepartmentMaster = $this->departmentMasterRepo->find($departmentMaster->id);
        $this->assertModelData($fakeDepartmentMaster, $dbDepartmentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDepartmentMaster()
    {
        $departmentMaster = $this->makeDepartmentMaster();
        $resp = $this->departmentMasterRepo->delete($departmentMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(DepartmentMaster::find($departmentMaster->id), 'DepartmentMaster should not exist in DB');
    }
}
