<?php namespace Tests\Repositories;

use App\Models\BudgetControlInfo;
use App\Repositories\BudgetControlInfoRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetControlInfoRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetControlInfoRepository
     */
    protected $budgetControlInfoRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetControlInfoRepo = \App::make(BudgetControlInfoRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->make()->toArray();

        $createdBudgetControlInfo = $this->budgetControlInfoRepo->create($budgetControlInfo);

        $createdBudgetControlInfo = $createdBudgetControlInfo->toArray();
        $this->assertArrayHasKey('id', $createdBudgetControlInfo);
        $this->assertNotNull($createdBudgetControlInfo['id'], 'Created BudgetControlInfo must have id specified');
        $this->assertNotNull(BudgetControlInfo::find($createdBudgetControlInfo['id']), 'BudgetControlInfo with given id must be in DB');
        $this->assertModelData($budgetControlInfo, $createdBudgetControlInfo);
    }

    /**
     * @test read
     */
    public function test_read_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->create();

        $dbBudgetControlInfo = $this->budgetControlInfoRepo->find($budgetControlInfo->id);

        $dbBudgetControlInfo = $dbBudgetControlInfo->toArray();
        $this->assertModelData($budgetControlInfo->toArray(), $dbBudgetControlInfo);
    }

    /**
     * @test update
     */
    public function test_update_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->create();
        $fakeBudgetControlInfo = factory(BudgetControlInfo::class)->make()->toArray();

        $updatedBudgetControlInfo = $this->budgetControlInfoRepo->update($fakeBudgetControlInfo, $budgetControlInfo->id);

        $this->assertModelData($fakeBudgetControlInfo, $updatedBudgetControlInfo->toArray());
        $dbBudgetControlInfo = $this->budgetControlInfoRepo->find($budgetControlInfo->id);
        $this->assertModelData($fakeBudgetControlInfo, $dbBudgetControlInfo->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_control_info()
    {
        $budgetControlInfo = factory(BudgetControlInfo::class)->create();

        $resp = $this->budgetControlInfoRepo->delete($budgetControlInfo->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetControlInfo::find($budgetControlInfo->id), 'BudgetControlInfo should not exist in DB');
    }
}
