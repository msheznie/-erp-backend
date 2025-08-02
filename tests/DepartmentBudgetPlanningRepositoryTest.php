<?php namespace Tests\Repositories;

use App\Models\DepartmentBudgetPlanning;
use App\Repositories\DepartmentBudgetPlanningRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DepartmentBudgetPlanningRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepartmentBudgetPlanningRepository
     */
    protected $departmentBudgetPlanningRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->departmentBudgetPlanningRepo = \App::make(DepartmentBudgetPlanningRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->make()->toArray();

        $createdDepartmentBudgetPlanning = $this->departmentBudgetPlanningRepo->create($departmentBudgetPlanning);

        $createdDepartmentBudgetPlanning = $createdDepartmentBudgetPlanning->toArray();
        $this->assertArrayHasKey('id', $createdDepartmentBudgetPlanning);
        $this->assertNotNull($createdDepartmentBudgetPlanning['id'], 'Created DepartmentBudgetPlanning must have id specified');
        $this->assertNotNull(DepartmentBudgetPlanning::find($createdDepartmentBudgetPlanning['id']), 'DepartmentBudgetPlanning with given id must be in DB');
        $this->assertModelData($departmentBudgetPlanning, $createdDepartmentBudgetPlanning);
    }

    /**
     * @test read
     */
    public function test_read_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->create();

        $dbDepartmentBudgetPlanning = $this->departmentBudgetPlanningRepo->find($departmentBudgetPlanning->id);

        $dbDepartmentBudgetPlanning = $dbDepartmentBudgetPlanning->toArray();
        $this->assertModelData($departmentBudgetPlanning->toArray(), $dbDepartmentBudgetPlanning);
    }

    /**
     * @test update
     */
    public function test_update_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->create();
        $fakeDepartmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->make()->toArray();

        $updatedDepartmentBudgetPlanning = $this->departmentBudgetPlanningRepo->update($fakeDepartmentBudgetPlanning, $departmentBudgetPlanning->id);

        $this->assertModelData($fakeDepartmentBudgetPlanning, $updatedDepartmentBudgetPlanning->toArray());
        $dbDepartmentBudgetPlanning = $this->departmentBudgetPlanningRepo->find($departmentBudgetPlanning->id);
        $this->assertModelData($fakeDepartmentBudgetPlanning, $dbDepartmentBudgetPlanning->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->create();

        $resp = $this->departmentBudgetPlanningRepo->delete($departmentBudgetPlanning->id);

        $this->assertTrue($resp);
        $this->assertNull(DepartmentBudgetPlanning::find($departmentBudgetPlanning->id), 'DepartmentBudgetPlanning should not exist in DB');
    }
}
