<?php namespace Tests\Repositories;

use App\Models\EmployeeDesignation;
use App\Repositories\EmployeeDesignationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EmployeeDesignationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EmployeeDesignationRepository
     */
    protected $employeeDesignationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->employeeDesignationRepo = \App::make(EmployeeDesignationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_employee_designation()
    {
        $employeeDesignation = factory(EmployeeDesignation::class)->make()->toArray();

        $createdEmployeeDesignation = $this->employeeDesignationRepo->create($employeeDesignation);

        $createdEmployeeDesignation = $createdEmployeeDesignation->toArray();
        $this->assertArrayHasKey('id', $createdEmployeeDesignation);
        $this->assertNotNull($createdEmployeeDesignation['id'], 'Created EmployeeDesignation must have id specified');
        $this->assertNotNull(EmployeeDesignation::find($createdEmployeeDesignation['id']), 'EmployeeDesignation with given id must be in DB');
        $this->assertModelData($employeeDesignation, $createdEmployeeDesignation);
    }

    /**
     * @test read
     */
    public function test_read_employee_designation()
    {
        $employeeDesignation = factory(EmployeeDesignation::class)->create();

        $dbEmployeeDesignation = $this->employeeDesignationRepo->find($employeeDesignation->id);

        $dbEmployeeDesignation = $dbEmployeeDesignation->toArray();
        $this->assertModelData($employeeDesignation->toArray(), $dbEmployeeDesignation);
    }

    /**
     * @test update
     */
    public function test_update_employee_designation()
    {
        $employeeDesignation = factory(EmployeeDesignation::class)->create();
        $fakeEmployeeDesignation = factory(EmployeeDesignation::class)->make()->toArray();

        $updatedEmployeeDesignation = $this->employeeDesignationRepo->update($fakeEmployeeDesignation, $employeeDesignation->id);

        $this->assertModelData($fakeEmployeeDesignation, $updatedEmployeeDesignation->toArray());
        $dbEmployeeDesignation = $this->employeeDesignationRepo->find($employeeDesignation->id);
        $this->assertModelData($fakeEmployeeDesignation, $dbEmployeeDesignation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_employee_designation()
    {
        $employeeDesignation = factory(EmployeeDesignation::class)->create();

        $resp = $this->employeeDesignationRepo->delete($employeeDesignation->id);

        $this->assertTrue($resp);
        $this->assertNull(EmployeeDesignation::find($employeeDesignation->id), 'EmployeeDesignation should not exist in DB');
    }
}
