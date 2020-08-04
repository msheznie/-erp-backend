<?php namespace Tests\Repositories;

use App\Models\EmployeeMobileBillMaster;
use App\Repositories\EmployeeMobileBillMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EmployeeMobileBillMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeMobileBillMasterRepository
     */
    protected $employeeMobileBillMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->employeeMobileBillMasterRepo = \App::make(EmployeeMobileBillMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->make()->toArray();

        $createdEmployeeMobileBillMaster = $this->employeeMobileBillMasterRepo->create($employeeMobileBillMaster);

        $createdEmployeeMobileBillMaster = $createdEmployeeMobileBillMaster->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeMobileBillMaster);
        $this->assertNotNull($createdEmployeeMobileBillMaster['id'], 'Created EmployeeMobileBillMaster must have id specified');
        $this->assertNotNull(EmployeeMobileBillMaster::find($createdEmployeeMobileBillMaster['id']), 'EmployeeMobileBillMaster with given id must be in DB');
        $this->assertModelData($employeeMobileBillMaster, $createdEmployeeMobileBillMaster);
    }

    /**
     * @test read
     */
    public function test_read_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->create();

        $dbEmployeeMobileBillMaster = $this->employeeMobileBillMasterRepo->find($employeeMobileBillMaster->id);

        $dbEmployeeMobileBillMaster = $dbEmployeeMobileBillMaster->toArray();
        $this->assertModelData($employeeMobileBillMaster->toArray(), $dbEmployeeMobileBillMaster);
    }

    /**
     * @test update
     */
    public function test_update_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->create();
        $fakeEmployeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->make()->toArray();

        $updatedEmployeeMobileBillMaster = $this->employeeMobileBillMasterRepo->update($fakeEmployeeMobileBillMaster, $employeeMobileBillMaster->id);

        $this->assertModelData($fakeEmployeeMobileBillMaster, $updatedEmployeeMobileBillMaster->toArray());
        $dbEmployeeMobileBillMaster = $this->employeeMobileBillMasterRepo->find($employeeMobileBillMaster->id);
        $this->assertModelData($fakeEmployeeMobileBillMaster, $dbEmployeeMobileBillMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->create();

        $resp = $this->employeeMobileBillMasterRepo->delete($employeeMobileBillMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(EmployeeMobileBillMaster::find($employeeMobileBillMaster->id), 'EmployeeMobileBillMaster should not exist in DB');
    }
}
