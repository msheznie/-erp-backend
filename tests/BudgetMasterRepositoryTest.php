<?php

use App\Models\BudgetMaster;
use App\Repositories\BudgetMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetMasterRepositoryTest extends TestCase
{
    use MakeBudgetMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetMasterRepository
     */
    protected $budgetMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->budgetMasterRepo = App::make(BudgetMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBudgetMaster()
    {
        $budgetMaster = $this->fakeBudgetMasterData();
        $createdBudgetMaster = $this->budgetMasterRepo->create($budgetMaster);
        $createdBudgetMaster = $createdBudgetMaster->toArray();
        $this->assertArrayHasKey('id', $createdBudgetMaster);
        $this->assertNotNull($createdBudgetMaster['id'], 'Created BudgetMaster must have id specified');
        $this->assertNotNull(BudgetMaster::find($createdBudgetMaster['id']), 'BudgetMaster with given id must be in DB');
        $this->assertModelData($budgetMaster, $createdBudgetMaster);
    }

    /**
     * @test read
     */
    public function testReadBudgetMaster()
    {
        $budgetMaster = $this->makeBudgetMaster();
        $dbBudgetMaster = $this->budgetMasterRepo->find($budgetMaster->id);
        $dbBudgetMaster = $dbBudgetMaster->toArray();
        $this->assertModelData($budgetMaster->toArray(), $dbBudgetMaster);
    }

    /**
     * @test update
     */
    public function testUpdateBudgetMaster()
    {
        $budgetMaster = $this->makeBudgetMaster();
        $fakeBudgetMaster = $this->fakeBudgetMasterData();
        $updatedBudgetMaster = $this->budgetMasterRepo->update($fakeBudgetMaster, $budgetMaster->id);
        $this->assertModelData($fakeBudgetMaster, $updatedBudgetMaster->toArray());
        $dbBudgetMaster = $this->budgetMasterRepo->find($budgetMaster->id);
        $this->assertModelData($fakeBudgetMaster, $dbBudgetMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBudgetMaster()
    {
        $budgetMaster = $this->makeBudgetMaster();
        $resp = $this->budgetMasterRepo->delete($budgetMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(BudgetMaster::find($budgetMaster->id), 'BudgetMaster should not exist in DB');
    }
}
