<?php

use App\Models\BudgetAdjustment;
use App\Repositories\BudgetAdjustmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetAdjustmentRepositoryTest extends TestCase
{
    use MakeBudgetAdjustmentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetAdjustmentRepository
     */
    protected $budgetAdjustmentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->budgetAdjustmentRepo = App::make(BudgetAdjustmentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBudgetAdjustment()
    {
        $budgetAdjustment = $this->fakeBudgetAdjustmentData();
        $createdBudgetAdjustment = $this->budgetAdjustmentRepo->create($budgetAdjustment);
        $createdBudgetAdjustment = $createdBudgetAdjustment->toArray();
        $this->assertArrayHasKey('id', $createdBudgetAdjustment);
        $this->assertNotNull($createdBudgetAdjustment['id'], 'Created BudgetAdjustment must have id specified');
        $this->assertNotNull(BudgetAdjustment::find($createdBudgetAdjustment['id']), 'BudgetAdjustment with given id must be in DB');
        $this->assertModelData($budgetAdjustment, $createdBudgetAdjustment);
    }

    /**
     * @test read
     */
    public function testReadBudgetAdjustment()
    {
        $budgetAdjustment = $this->makeBudgetAdjustment();
        $dbBudgetAdjustment = $this->budgetAdjustmentRepo->find($budgetAdjustment->id);
        $dbBudgetAdjustment = $dbBudgetAdjustment->toArray();
        $this->assertModelData($budgetAdjustment->toArray(), $dbBudgetAdjustment);
    }

    /**
     * @test update
     */
    public function testUpdateBudgetAdjustment()
    {
        $budgetAdjustment = $this->makeBudgetAdjustment();
        $fakeBudgetAdjustment = $this->fakeBudgetAdjustmentData();
        $updatedBudgetAdjustment = $this->budgetAdjustmentRepo->update($fakeBudgetAdjustment, $budgetAdjustment->id);
        $this->assertModelData($fakeBudgetAdjustment, $updatedBudgetAdjustment->toArray());
        $dbBudgetAdjustment = $this->budgetAdjustmentRepo->find($budgetAdjustment->id);
        $this->assertModelData($fakeBudgetAdjustment, $dbBudgetAdjustment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBudgetAdjustment()
    {
        $budgetAdjustment = $this->makeBudgetAdjustment();
        $resp = $this->budgetAdjustmentRepo->delete($budgetAdjustment->id);
        $this->assertTrue($resp);
        $this->assertNull(BudgetAdjustment::find($budgetAdjustment->id), 'BudgetAdjustment should not exist in DB');
    }
}
