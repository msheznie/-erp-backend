<?php

use App\Models\EmployeeProfile;
use App\Repositories\EmployeeProfileRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeProfileRepositoryTest extends TestCase
{
    use MakeEmployeeProfileTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeProfileRepository
     */
    protected $employeeProfileRepo;

    public function setUp()
    {
        parent::setUp();
        $this->employeeProfileRepo = App::make(EmployeeProfileRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEmployeeProfile()
    {
        $employeeProfile = $this->fakeEmployeeProfileData();
        $createdEmployeeProfile = $this->employeeProfileRepo->create($employeeProfile);
        $createdEmployeeProfile = $createdEmployeeProfile->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeProfile);
        $this->assertNotNull($createdEmployeeProfile['id'], 'Created EmployeeProfile must have id specified');
        $this->assertNotNull(EmployeeProfile::find($createdEmployeeProfile['id']), 'EmployeeProfile with given id must be in DB');
        $this->assertModelData($employeeProfile, $createdEmployeeProfile);
    }

    /**
     * @test read
     */
    public function testReadEmployeeProfile()
    {
        $employeeProfile = $this->makeEmployeeProfile();
        $dbEmployeeProfile = $this->employeeProfileRepo->find($employeeProfile->id);
        $dbEmployeeProfile = $dbEmployeeProfile->toArray();
        $this->assertModelData($employeeProfile->toArray(), $dbEmployeeProfile);
    }

    /**
     * @test update
     */
    public function testUpdateEmployeeProfile()
    {
        $employeeProfile = $this->makeEmployeeProfile();
        $fakeEmployeeProfile = $this->fakeEmployeeProfileData();
        $updatedEmployeeProfile = $this->employeeProfileRepo->update($fakeEmployeeProfile, $employeeProfile->id);
        $this->assertModelData($fakeEmployeeProfile, $updatedEmployeeProfile->toArray());
        $dbEmployeeProfile = $this->employeeProfileRepo->find($employeeProfile->id);
        $this->assertModelData($fakeEmployeeProfile, $dbEmployeeProfile->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEmployeeProfile()
    {
        $employeeProfile = $this->makeEmployeeProfile();
        $resp = $this->employeeProfileRepo->delete($employeeProfile->id);
        $this->assertTrue($resp);
        $this->assertNull(EmployeeProfile::find($employeeProfile->id), 'EmployeeProfile should not exist in DB');
    }
}
