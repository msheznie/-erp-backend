<?php namespace Tests\Repositories;

use App\Models\DepBudgetPlDetEmpColumn;
use App\Repositories\DepBudgetPlDetEmpColumnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DepBudgetPlDetEmpColumnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepBudgetPlDetEmpColumnRepository
     */
    protected $depBudgetPlDetEmpColumnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->depBudgetPlDetEmpColumnRepo = \App::make(DepBudgetPlDetEmpColumnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->make()->toArray();

        $createdDepBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepo->create($depBudgetPlDetEmpColumn);

        $createdDepBudgetPlDetEmpColumn = $createdDepBudgetPlDetEmpColumn->toArray();
        $this->assertArrayHasKey('id', $createdDepBudgetPlDetEmpColumn);
        $this->assertNotNull($createdDepBudgetPlDetEmpColumn['id'], 'Created DepBudgetPlDetEmpColumn must have id specified');
        $this->assertNotNull(DepBudgetPlDetEmpColumn::find($createdDepBudgetPlDetEmpColumn['id']), 'DepBudgetPlDetEmpColumn with given id must be in DB');
        $this->assertModelData($depBudgetPlDetEmpColumn, $createdDepBudgetPlDetEmpColumn);
    }

    /**
     * @test read
     */
    public function test_read_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->create();

        $dbDepBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepo->find($depBudgetPlDetEmpColumn->id);

        $dbDepBudgetPlDetEmpColumn = $dbDepBudgetPlDetEmpColumn->toArray();
        $this->assertModelData($depBudgetPlDetEmpColumn->toArray(), $dbDepBudgetPlDetEmpColumn);
    }

    /**
     * @test update
     */
    public function test_update_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->create();
        $fakeDepBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->make()->toArray();

        $updatedDepBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepo->update($fakeDepBudgetPlDetEmpColumn, $depBudgetPlDetEmpColumn->id);

        $this->assertModelData($fakeDepBudgetPlDetEmpColumn, $updatedDepBudgetPlDetEmpColumn->toArray());
        $dbDepBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepo->find($depBudgetPlDetEmpColumn->id);
        $this->assertModelData($fakeDepBudgetPlDetEmpColumn, $dbDepBudgetPlDetEmpColumn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->create();

        $resp = $this->depBudgetPlDetEmpColumnRepo->delete($depBudgetPlDetEmpColumn->id);

        $this->assertTrue($resp);
        $this->assertNull(DepBudgetPlDetEmpColumn::find($depBudgetPlDetEmpColumn->id), 'DepBudgetPlDetEmpColumn should not exist in DB');
    }
}
