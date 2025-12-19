<?php

use App\Models\ExpenseClaimDetails;
use App\Repositories\ExpenseClaimDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimDetailsRepositoryTest extends TestCase
{
    use MakeExpenseClaimDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimDetailsRepository
     */
    protected $expenseClaimDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->expenseClaimDetailsRepo = App::make(ExpenseClaimDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->fakeExpenseClaimDetailsData();
        $createdExpenseClaimDetails = $this->expenseClaimDetailsRepo->create($expenseClaimDetails);
        $createdExpenseClaimDetails = $createdExpenseClaimDetails->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaimDetails);
        $this->assertNotNull($createdExpenseClaimDetails['id'], 'Created ExpenseClaimDetails must have id specified');
        $this->assertNotNull(ExpenseClaimDetails::find($createdExpenseClaimDetails['id']), 'ExpenseClaimDetails with given id must be in DB');
        $this->assertModelData($expenseClaimDetails, $createdExpenseClaimDetails);
    }

    /**
     * @test read
     */
    public function testReadExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->makeExpenseClaimDetails();
        $dbExpenseClaimDetails = $this->expenseClaimDetailsRepo->find($expenseClaimDetails->id);
        $dbExpenseClaimDetails = $dbExpenseClaimDetails->toArray();
        $this->assertModelData($expenseClaimDetails->toArray(), $dbExpenseClaimDetails);
    }

    /**
     * @test update
     */
    public function testUpdateExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->makeExpenseClaimDetails();
        $fakeExpenseClaimDetails = $this->fakeExpenseClaimDetailsData();
        $updatedExpenseClaimDetails = $this->expenseClaimDetailsRepo->update($fakeExpenseClaimDetails, $expenseClaimDetails->id);
        $this->assertModelData($fakeExpenseClaimDetails, $updatedExpenseClaimDetails->toArray());
        $dbExpenseClaimDetails = $this->expenseClaimDetailsRepo->find($expenseClaimDetails->id);
        $this->assertModelData($fakeExpenseClaimDetails, $dbExpenseClaimDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->makeExpenseClaimDetails();
        $resp = $this->expenseClaimDetailsRepo->delete($expenseClaimDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaimDetails::find($expenseClaimDetails->id), 'ExpenseClaimDetails should not exist in DB');
    }
}
