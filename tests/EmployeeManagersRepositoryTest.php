<?php namespace Tests\Repositories;

use App\Models\EmployeeManagers;
use App\Repositories\EmployeeManagersRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeEmployeeManagersTrait;
use Tests\ApiTestTrait;

class EmployeeManagersRepositoryTest extends TestCase
{
    use MakeEmployeeManagersTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeManagersRepository
     */
    protected $employeeManagersRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->employeeManagersRepo = \App::make(EmployeeManagersRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_employee_managers()
    {
        $employeeManagers = $this->fakeEmployeeManagersData();
        $createdEmployeeManagers = $this->employeeManagersRepo->create($employeeManagers);
        $createdEmployeeManagers = $createdEmployeeManagers->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeManagers);
        $this->assertNotNull($createdEmployeeManagers['id'], 'Created EmployeeManagers must have id specified');
        $this->assertNotNull(EmployeeManagers::find($createdEmployeeManagers['id']), 'EmployeeManagers with given id must be in DB');
        $this->assertModelData($employeeManagers, $createdEmployeeManagers);
    }

    /**
     * @test read
     */
    public function test_read_employee_managers()
    {
        $employeeManagers = $this->makeEmployeeManagers();
        $dbEmployeeManagers = $this->employeeManagersRepo->find($employeeManagers->id);
        $dbEmployeeManagers = $dbEmployeeManagers->toArray();
        $this->assertModelData($employeeManagers->toArray(), $dbEmployeeManagers);
    }

    /**
     * @test update
     */
    public function test_update_employee_managers()
    {
        $employeeManagers = $this->makeEmployeeManagers();
        $fakeEmployeeManagers = $this->fakeEmployeeManagersData();
        $updatedEmployeeManagers = $this->employeeManagersRepo->update($fakeEmployeeManagers, $employeeManagers->id);
        $this->assertModelData($fakeEmployeeManagers, $updatedEmployeeManagers->toArray());
        $dbEmployeeManagers = $this->employeeManagersRepo->find($employeeManagers->id);
        $this->assertModelData($fakeEmployeeManagers, $dbEmployeeManagers->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_employee_managers()
    {
        $employeeManagers = $this->makeEmployeeManagers();
        $resp = $this->employeeManagersRepo->delete($employeeManagers->id);
        $this->assertTrue($resp);
        $this->assertNull(EmployeeManagers::find($employeeManagers->id), 'EmployeeManagers should not exist in DB');
    }
}
