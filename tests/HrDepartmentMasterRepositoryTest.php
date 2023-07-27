<?php namespace Tests\Repositories;

use App\Models\HrDepartmentMaster;
use App\Repositories\HrDepartmentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrDepartmentMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrDepartmentMasterRepository
     */
    protected $hrDepartmentMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrDepartmentMasterRepo = \App::make(HrDepartmentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->make()->toArray();

        $createdHrDepartmentMaster = $this->hrDepartmentMasterRepo->create($hrDepartmentMaster);

        $createdHrDepartmentMaster = $createdHrDepartmentMaster->toArray();
        $this->assertArrayHasKey('id', $createdHrDepartmentMaster);
        $this->assertNotNull($createdHrDepartmentMaster['id'], 'Created HrDepartmentMaster must have id specified');
        $this->assertNotNull(HrDepartmentMaster::find($createdHrDepartmentMaster['id']), 'HrDepartmentMaster with given id must be in DB');
        $this->assertModelData($hrDepartmentMaster, $createdHrDepartmentMaster);
    }

    /**
     * @test read
     */
    public function test_read_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->create();

        $dbHrDepartmentMaster = $this->hrDepartmentMasterRepo->find($hrDepartmentMaster->id);

        $dbHrDepartmentMaster = $dbHrDepartmentMaster->toArray();
        $this->assertModelData($hrDepartmentMaster->toArray(), $dbHrDepartmentMaster);
    }

    /**
     * @test update
     */
    public function test_update_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->create();
        $fakeHrDepartmentMaster = factory(HrDepartmentMaster::class)->make()->toArray();

        $updatedHrDepartmentMaster = $this->hrDepartmentMasterRepo->update($fakeHrDepartmentMaster, $hrDepartmentMaster->id);

        $this->assertModelData($fakeHrDepartmentMaster, $updatedHrDepartmentMaster->toArray());
        $dbHrDepartmentMaster = $this->hrDepartmentMasterRepo->find($hrDepartmentMaster->id);
        $this->assertModelData($fakeHrDepartmentMaster, $dbHrDepartmentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->create();

        $resp = $this->hrDepartmentMasterRepo->delete($hrDepartmentMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(HrDepartmentMaster::find($hrDepartmentMaster->id), 'HrDepartmentMaster should not exist in DB');
    }
}
