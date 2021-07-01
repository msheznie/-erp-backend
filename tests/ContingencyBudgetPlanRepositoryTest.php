<?php namespace Tests\Repositories;

use App\Models\ContingencyBudgetPlan;
use App\Repositories\ContingencyBudgetPlanRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ContingencyBudgetPlanRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ContingencyBudgetPlanRepository
     */
    protected $contingencyBudgetPlanRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->contingencyBudgetPlanRepo = \App::make(ContingencyBudgetPlanRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->make()->toArray();

        $createdContingencyBudgetPlan = $this->contingencyBudgetPlanRepo->create($contingencyBudgetPlan);

        $createdContingencyBudgetPlan = $createdContingencyBudgetPlan->toArray();
        $this->assertArrayHasKey('id', $createdContingencyBudgetPlan);
        $this->assertNotNull($createdContingencyBudgetPlan['id'], 'Created ContingencyBudgetPlan must have id specified');
        $this->assertNotNull(ContingencyBudgetPlan::find($createdContingencyBudgetPlan['id']), 'ContingencyBudgetPlan with given id must be in DB');
        $this->assertModelData($contingencyBudgetPlan, $createdContingencyBudgetPlan);
    }

    /**
     * @test read
     */
    public function test_read_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->create();

        $dbContingencyBudgetPlan = $this->contingencyBudgetPlanRepo->find($contingencyBudgetPlan->id);

        $dbContingencyBudgetPlan = $dbContingencyBudgetPlan->toArray();
        $this->assertModelData($contingencyBudgetPlan->toArray(), $dbContingencyBudgetPlan);
    }

    /**
     * @test update
     */
    public function test_update_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->create();
        $fakeContingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->make()->toArray();

        $updatedContingencyBudgetPlan = $this->contingencyBudgetPlanRepo->update($fakeContingencyBudgetPlan, $contingencyBudgetPlan->id);

        $this->assertModelData($fakeContingencyBudgetPlan, $updatedContingencyBudgetPlan->toArray());
        $dbContingencyBudgetPlan = $this->contingencyBudgetPlanRepo->find($contingencyBudgetPlan->id);
        $this->assertModelData($fakeContingencyBudgetPlan, $dbContingencyBudgetPlan->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->create();

        $resp = $this->contingencyBudgetPlanRepo->delete($contingencyBudgetPlan->id);

        $this->assertTrue($resp);
        $this->assertNull(ContingencyBudgetPlan::find($contingencyBudgetPlan->id), 'ContingencyBudgetPlan should not exist in DB');
    }
}
