<?php namespace Tests\Repositories;

use App\Models\DepBudgetPlDetColumn;
use App\Repositories\DepBudgetPlDetColumnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DepBudgetPlDetColumnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepBudgetPlDetColumnRepository
     */
    protected $depBudgetPlDetColumnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->depBudgetPlDetColumnRepo = \App::make(DepBudgetPlDetColumnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->make()->toArray();

        $createdDepBudgetPlDetColumn = $this->depBudgetPlDetColumnRepo->create($depBudgetPlDetColumn);

        $createdDepBudgetPlDetColumn = $createdDepBudgetPlDetColumn->toArray();
        $this->assertArrayHasKey('id', $createdDepBudgetPlDetColumn);
        $this->assertNotNull($createdDepBudgetPlDetColumn['id'], 'Created DepBudgetPlDetColumn must have id specified');
        $this->assertNotNull(DepBudgetPlDetColumn::find($createdDepBudgetPlDetColumn['id']), 'DepBudgetPlDetColumn with given id must be in DB');
        $this->assertModelData($depBudgetPlDetColumn, $createdDepBudgetPlDetColumn);
    }

    /**
     * @test read
     */
    public function test_read_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->create();

        $dbDepBudgetPlDetColumn = $this->depBudgetPlDetColumnRepo->find($depBudgetPlDetColumn->id);

        $dbDepBudgetPlDetColumn = $dbDepBudgetPlDetColumn->toArray();
        $this->assertModelData($depBudgetPlDetColumn->toArray(), $dbDepBudgetPlDetColumn);
    }

    /**
     * @test update
     */
    public function test_update_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->create();
        $fakeDepBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->make()->toArray();

        $updatedDepBudgetPlDetColumn = $this->depBudgetPlDetColumnRepo->update($fakeDepBudgetPlDetColumn, $depBudgetPlDetColumn->id);

        $this->assertModelData($fakeDepBudgetPlDetColumn, $updatedDepBudgetPlDetColumn->toArray());
        $dbDepBudgetPlDetColumn = $this->depBudgetPlDetColumnRepo->find($depBudgetPlDetColumn->id);
        $this->assertModelData($fakeDepBudgetPlDetColumn, $dbDepBudgetPlDetColumn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->create();

        $resp = $this->depBudgetPlDetColumnRepo->delete($depBudgetPlDetColumn->id);

        $this->assertTrue($resp);
        $this->assertNull(DepBudgetPlDetColumn::find($depBudgetPlDetColumn->id), 'DepBudgetPlDetColumn should not exist in DB');
    }
}
