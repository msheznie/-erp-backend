<?php

use App\Models\BudgetTransferFormDetail;
use App\Repositories\BudgetTransferFormDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetTransferFormDetailRepositoryTest extends TestCase
{
    use MakeBudgetTransferFormDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetTransferFormDetailRepository
     */
    protected $budgetTransferFormDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->budgetTransferFormDetailRepo = App::make(BudgetTransferFormDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->fakeBudgetTransferFormDetailData();
        $createdBudgetTransferFormDetail = $this->budgetTransferFormDetailRepo->create($budgetTransferFormDetail);
        $createdBudgetTransferFormDetail = $createdBudgetTransferFormDetail->toArray();
        $this->assertArrayHasKey('id', $createdBudgetTransferFormDetail);
        $this->assertNotNull($createdBudgetTransferFormDetail['id'], 'Created BudgetTransferFormDetail must have id specified');
        $this->assertNotNull(BudgetTransferFormDetail::find($createdBudgetTransferFormDetail['id']), 'BudgetTransferFormDetail with given id must be in DB');
        $this->assertModelData($budgetTransferFormDetail, $createdBudgetTransferFormDetail);
    }

    /**
     * @test read
     */
    public function testReadBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->makeBudgetTransferFormDetail();
        $dbBudgetTransferFormDetail = $this->budgetTransferFormDetailRepo->find($budgetTransferFormDetail->id);
        $dbBudgetTransferFormDetail = $dbBudgetTransferFormDetail->toArray();
        $this->assertModelData($budgetTransferFormDetail->toArray(), $dbBudgetTransferFormDetail);
    }

    /**
     * @test update
     */
    public function testUpdateBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->makeBudgetTransferFormDetail();
        $fakeBudgetTransferFormDetail = $this->fakeBudgetTransferFormDetailData();
        $updatedBudgetTransferFormDetail = $this->budgetTransferFormDetailRepo->update($fakeBudgetTransferFormDetail, $budgetTransferFormDetail->id);
        $this->assertModelData($fakeBudgetTransferFormDetail, $updatedBudgetTransferFormDetail->toArray());
        $dbBudgetTransferFormDetail = $this->budgetTransferFormDetailRepo->find($budgetTransferFormDetail->id);
        $this->assertModelData($fakeBudgetTransferFormDetail, $dbBudgetTransferFormDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->makeBudgetTransferFormDetail();
        $resp = $this->budgetTransferFormDetailRepo->delete($budgetTransferFormDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(BudgetTransferFormDetail::find($budgetTransferFormDetail->id), 'BudgetTransferFormDetail should not exist in DB');
    }
}
