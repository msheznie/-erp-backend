<?php namespace Tests\Repositories;

use App\Models\employeeDepartmentDelegation;
use App\Repositories\employeeDepartmentDelegationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeemployeeDepartmentDelegationTrait;
use Tests\ApiTestTrait;

class employeeDepartmentDelegationRepositoryTest extends TestCase
{
    use MakeemployeeDepartmentDelegationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var employeeDepartmentDelegationRepository
     */
    protected $employeeDepartmentDelegationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->employeeDepartmentDelegationRepo = \App::make(employeeDepartmentDelegationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->fakeemployeeDepartmentDelegationData();
        $createdemployeeDepartmentDelegation = $this->employeeDepartmentDelegationRepo->create($employeeDepartmentDelegation);
        $createdemployeeDepartmentDelegation = $createdemployeeDepartmentDelegation->toArray();
        $this->assertArrayHasKey('id', $createdemployeeDepartmentDelegation);
        $this->assertNotNull($createdemployeeDepartmentDelegation['id'], 'Created employeeDepartmentDelegation must have id specified');
        $this->assertNotNull(employeeDepartmentDelegation::find($createdemployeeDepartmentDelegation['id']), 'employeeDepartmentDelegation with given id must be in DB');
        $this->assertModelData($employeeDepartmentDelegation, $createdemployeeDepartmentDelegation);
    }

    /**
     * @test read
     */
    public function test_read_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->makeemployeeDepartmentDelegation();
        $dbemployeeDepartmentDelegation = $this->employeeDepartmentDelegationRepo->find($employeeDepartmentDelegation->id);
        $dbemployeeDepartmentDelegation = $dbemployeeDepartmentDelegation->toArray();
        $this->assertModelData($employeeDepartmentDelegation->toArray(), $dbemployeeDepartmentDelegation);
    }

    /**
     * @test update
     */
    public function test_update_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->makeemployeeDepartmentDelegation();
        $fakeemployeeDepartmentDelegation = $this->fakeemployeeDepartmentDelegationData();
        $updatedemployeeDepartmentDelegation = $this->employeeDepartmentDelegationRepo->update($fakeemployeeDepartmentDelegation, $employeeDepartmentDelegation->id);
        $this->assertModelData($fakeemployeeDepartmentDelegation, $updatedemployeeDepartmentDelegation->toArray());
        $dbemployeeDepartmentDelegation = $this->employeeDepartmentDelegationRepo->find($employeeDepartmentDelegation->id);
        $this->assertModelData($fakeemployeeDepartmentDelegation, $dbemployeeDepartmentDelegation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->makeemployeeDepartmentDelegation();
        $resp = $this->employeeDepartmentDelegationRepo->delete($employeeDepartmentDelegation->id);
        $this->assertTrue($resp);
        $this->assertNull(employeeDepartmentDelegation::find($employeeDepartmentDelegation->id), 'employeeDepartmentDelegation should not exist in DB');
    }
}
