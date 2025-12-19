<?php

use App\Models\BudgetTransferForm;
use App\Repositories\BudgetTransferFormRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetTransferFormRepositoryTest extends TestCase
{
    use MakeBudgetTransferFormTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetTransferFormRepository
     */
    protected $budgetTransferFormRepo;

    public function setUp()
    {
        parent::setUp();
        $this->budgetTransferFormRepo = App::make(BudgetTransferFormRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBudgetTransferForm()
    {
        $budgetTransferForm = $this->fakeBudgetTransferFormData();
        $createdBudgetTransferForm = $this->budgetTransferFormRepo->create($budgetTransferForm);
        $createdBudgetTransferForm = $createdBudgetTransferForm->toArray();
        $this->assertArrayHasKey('id', $createdBudgetTransferForm);
        $this->assertNotNull($createdBudgetTransferForm['id'], 'Created BudgetTransferForm must have id specified');
        $this->assertNotNull(BudgetTransferForm::find($createdBudgetTransferForm['id']), 'BudgetTransferForm with given id must be in DB');
        $this->assertModelData($budgetTransferForm, $createdBudgetTransferForm);
    }

    /**
     * @test read
     */
    public function testReadBudgetTransferForm()
    {
        $budgetTransferForm = $this->makeBudgetTransferForm();
        $dbBudgetTransferForm = $this->budgetTransferFormRepo->find($budgetTransferForm->id);
        $dbBudgetTransferForm = $dbBudgetTransferForm->toArray();
        $this->assertModelData($budgetTransferForm->toArray(), $dbBudgetTransferForm);
    }

    /**
     * @test update
     */
    public function testUpdateBudgetTransferForm()
    {
        $budgetTransferForm = $this->makeBudgetTransferForm();
        $fakeBudgetTransferForm = $this->fakeBudgetTransferFormData();
        $updatedBudgetTransferForm = $this->budgetTransferFormRepo->update($fakeBudgetTransferForm, $budgetTransferForm->id);
        $this->assertModelData($fakeBudgetTransferForm, $updatedBudgetTransferForm->toArray());
        $dbBudgetTransferForm = $this->budgetTransferFormRepo->find($budgetTransferForm->id);
        $this->assertModelData($fakeBudgetTransferForm, $dbBudgetTransferForm->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBudgetTransferForm()
    {
        $budgetTransferForm = $this->makeBudgetTransferForm();
        $resp = $this->budgetTransferFormRepo->delete($budgetTransferForm->id);
        $this->assertTrue($resp);
        $this->assertNull(BudgetTransferForm::find($budgetTransferForm->id), 'BudgetTransferForm should not exist in DB');
    }
}
