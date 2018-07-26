<?php

use App\Models\EmployeeDetails;
use App\Repositories\EmployeeDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeDetailsRepositoryTest extends TestCase
{
    use MakeEmployeeDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeDetailsRepository
     */
    protected $employeeDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->employeeDetailsRepo = App::make(EmployeeDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEmployeeDetails()
    {
        $employeeDetails = $this->fakeEmployeeDetailsData();
        $createdEmployeeDetails = $this->employeeDetailsRepo->create($employeeDetails);
        $createdEmployeeDetails = $createdEmployeeDetails->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeDetails);
        $this->assertNotNull($createdEmployeeDetails['id'], 'Created EmployeeDetails must have id specified');
        $this->assertNotNull(EmployeeDetails::find($createdEmployeeDetails['id']), 'EmployeeDetails with given id must be in DB');
        $this->assertModelData($employeeDetails, $createdEmployeeDetails);
    }

    /**
     * @test read
     */
    public function testReadEmployeeDetails()
    {
        $employeeDetails = $this->makeEmployeeDetails();
        $dbEmployeeDetails = $this->employeeDetailsRepo->find($employeeDetails->id);
        $dbEmployeeDetails = $dbEmployeeDetails->toArray();
        $this->assertModelData($employeeDetails->toArray(), $dbEmployeeDetails);
    }

    /**
     * @test update
     */
    public function testUpdateEmployeeDetails()
    {
        $employeeDetails = $this->makeEmployeeDetails();
        $fakeEmployeeDetails = $this->fakeEmployeeDetailsData();
        $updatedEmployeeDetails = $this->employeeDetailsRepo->update($fakeEmployeeDetails, $employeeDetails->id);
        $this->assertModelData($fakeEmployeeDetails, $updatedEmployeeDetails->toArray());
        $dbEmployeeDetails = $this->employeeDetailsRepo->find($employeeDetails->id);
        $this->assertModelData($fakeEmployeeDetails, $dbEmployeeDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEmployeeDetails()
    {
        $employeeDetails = $this->makeEmployeeDetails();
        $resp = $this->employeeDetailsRepo->delete($employeeDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(EmployeeDetails::find($employeeDetails->id), 'EmployeeDetails should not exist in DB');
    }
}
