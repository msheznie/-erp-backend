<?php

use App\Models\EmployeeNavigation;
use App\Repositories\EmployeeNavigationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeNavigationRepositoryTest extends TestCase
{
    use MakeEmployeeNavigationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeNavigationRepository
     */
    protected $employeeNavigationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->employeeNavigationRepo = App::make(EmployeeNavigationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEmployeeNavigation()
    {
        $employeeNavigation = $this->fakeEmployeeNavigationData();
        $createdEmployeeNavigation = $this->employeeNavigationRepo->create($employeeNavigation);
        $createdEmployeeNavigation = $createdEmployeeNavigation->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeNavigation);
        $this->assertNotNull($createdEmployeeNavigation['id'], 'Created EmployeeNavigation must have id specified');
        $this->assertNotNull(EmployeeNavigation::find($createdEmployeeNavigation['id']), 'EmployeeNavigation with given id must be in DB');
        $this->assertModelData($employeeNavigation, $createdEmployeeNavigation);
    }

    /**
     * @test read
     */
    public function testReadEmployeeNavigation()
    {
        $employeeNavigation = $this->makeEmployeeNavigation();
        $dbEmployeeNavigation = $this->employeeNavigationRepo->find($employeeNavigation->id);
        $dbEmployeeNavigation = $dbEmployeeNavigation->toArray();
        $this->assertModelData($employeeNavigation->toArray(), $dbEmployeeNavigation);
    }

    /**
     * @test update
     */
    public function testUpdateEmployeeNavigation()
    {
        $employeeNavigation = $this->makeEmployeeNavigation();
        $fakeEmployeeNavigation = $this->fakeEmployeeNavigationData();
        $updatedEmployeeNavigation = $this->employeeNavigationRepo->update($fakeEmployeeNavigation, $employeeNavigation->id);
        $this->assertModelData($fakeEmployeeNavigation, $updatedEmployeeNavigation->toArray());
        $dbEmployeeNavigation = $this->employeeNavigationRepo->find($employeeNavigation->id);
        $this->assertModelData($fakeEmployeeNavigation, $dbEmployeeNavigation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEmployeeNavigation()
    {
        $employeeNavigation = $this->makeEmployeeNavigation();
        $resp = $this->employeeNavigationRepo->delete($employeeNavigation->id);
        $this->assertTrue($resp);
        $this->assertNull(EmployeeNavigation::find($employeeNavigation->id), 'EmployeeNavigation should not exist in DB');
    }
}
