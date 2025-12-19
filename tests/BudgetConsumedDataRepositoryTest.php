<?php

use App\Models\BudgetConsumedData;
use App\Repositories\BudgetConsumedDataRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetConsumedDataRepositoryTest extends TestCase
{
    use MakeBudgetConsumedDataTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetConsumedDataRepository
     */
    protected $budgetConsumedDataRepo;

    public function setUp()
    {
        parent::setUp();
        $this->budgetConsumedDataRepo = App::make(BudgetConsumedDataRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBudgetConsumedData()
    {
        $budgetConsumedData = $this->fakeBudgetConsumedDataData();
        $createdBudgetConsumedData = $this->budgetConsumedDataRepo->create($budgetConsumedData);
        $createdBudgetConsumedData = $createdBudgetConsumedData->toArray();
        $this->assertArrayHasKey('id', $createdBudgetConsumedData);
        $this->assertNotNull($createdBudgetConsumedData['id'], 'Created BudgetConsumedData must have id specified');
        $this->assertNotNull(BudgetConsumedData::find($createdBudgetConsumedData['id']), 'BudgetConsumedData with given id must be in DB');
        $this->assertModelData($budgetConsumedData, $createdBudgetConsumedData);
    }

    /**
     * @test read
     */
    public function testReadBudgetConsumedData()
    {
        $budgetConsumedData = $this->makeBudgetConsumedData();
        $dbBudgetConsumedData = $this->budgetConsumedDataRepo->find($budgetConsumedData->id);
        $dbBudgetConsumedData = $dbBudgetConsumedData->toArray();
        $this->assertModelData($budgetConsumedData->toArray(), $dbBudgetConsumedData);
    }

    /**
     * @test update
     */
    public function testUpdateBudgetConsumedData()
    {
        $budgetConsumedData = $this->makeBudgetConsumedData();
        $fakeBudgetConsumedData = $this->fakeBudgetConsumedDataData();
        $updatedBudgetConsumedData = $this->budgetConsumedDataRepo->update($fakeBudgetConsumedData, $budgetConsumedData->id);
        $this->assertModelData($fakeBudgetConsumedData, $updatedBudgetConsumedData->toArray());
        $dbBudgetConsumedData = $this->budgetConsumedDataRepo->find($budgetConsumedData->id);
        $this->assertModelData($fakeBudgetConsumedData, $dbBudgetConsumedData->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBudgetConsumedData()
    {
        $budgetConsumedData = $this->makeBudgetConsumedData();
        $resp = $this->budgetConsumedDataRepo->delete($budgetConsumedData->id);
        $this->assertTrue($resp);
        $this->assertNull(BudgetConsumedData::find($budgetConsumedData->id), 'BudgetConsumedData should not exist in DB');
    }
}
