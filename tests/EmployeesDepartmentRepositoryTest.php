<?php

use App\Models\EmployeesDepartment;
use App\Repositories\EmployeesDepartmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeesDepartmentRepositoryTest extends TestCase
{
    use MakeEmployeesDepartmentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeesDepartmentRepository
     */
    protected $employeesDepartmentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->employeesDepartmentRepo = App::make(EmployeesDepartmentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEmployeesDepartment()
    {
        $employeesDepartment = $this->fakeEmployeesDepartmentData();
        $createdEmployeesDepartment = $this->employeesDepartmentRepo->create($employeesDepartment);
        $createdEmployeesDepartment = $createdEmployeesDepartment->toArray();
        $this->assertArrayHasKey('id', $createdEmployeesDepartment);
        $this->assertNotNull($createdEmployeesDepartment['id'], 'Created EmployeesDepartment must have id specified');
        $this->assertNotNull(EmployeesDepartment::find($createdEmployeesDepartment['id']), 'EmployeesDepartment with given id must be in DB');
        $this->assertModelData($employeesDepartment, $createdEmployeesDepartment);
    }

    /**
     * @test read
     */
    public function testReadEmployeesDepartment()
    {
        $employeesDepartment = $this->makeEmployeesDepartment();
        $dbEmployeesDepartment = $this->employeesDepartmentRepo->find($employeesDepartment->id);
        $dbEmployeesDepartment = $dbEmployeesDepartment->toArray();
        $this->assertModelData($employeesDepartment->toArray(), $dbEmployeesDepartment);
    }

    /**
     * @test update
     */
    public function testUpdateEmployeesDepartment()
    {
        $employeesDepartment = $this->makeEmployeesDepartment();
        $fakeEmployeesDepartment = $this->fakeEmployeesDepartmentData();
        $updatedEmployeesDepartment = $this->employeesDepartmentRepo->update($fakeEmployeesDepartment, $employeesDepartment->id);
        $this->assertModelData($fakeEmployeesDepartment, $updatedEmployeesDepartment->toArray());
        $dbEmployeesDepartment = $this->employeesDepartmentRepo->find($employeesDepartment->id);
        $this->assertModelData($fakeEmployeesDepartment, $dbEmployeesDepartment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEmployeesDepartment()
    {
        $employeesDepartment = $this->makeEmployeesDepartment();
        $resp = $this->employeesDepartmentRepo->delete($employeesDepartment->id);
        $this->assertTrue($resp);
        $this->assertNull(EmployeesDepartment::find($employeesDepartment->id), 'EmployeesDepartment should not exist in DB');
    }
}
