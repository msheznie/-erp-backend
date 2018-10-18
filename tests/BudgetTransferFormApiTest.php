<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetTransferFormApiTest extends TestCase
{
    use MakeBudgetTransferFormTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBudgetTransferForm()
    {
        $budgetTransferForm = $this->fakeBudgetTransferFormData();
        $this->json('POST', '/api/v1/budgetTransferForms', $budgetTransferForm);

        $this->assertApiResponse($budgetTransferForm);
    }

    /**
     * @test
     */
    public function testReadBudgetTransferForm()
    {
        $budgetTransferForm = $this->makeBudgetTransferForm();
        $this->json('GET', '/api/v1/budgetTransferForms/'.$budgetTransferForm->id);

        $this->assertApiResponse($budgetTransferForm->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBudgetTransferForm()
    {
        $budgetTransferForm = $this->makeBudgetTransferForm();
        $editedBudgetTransferForm = $this->fakeBudgetTransferFormData();

        $this->json('PUT', '/api/v1/budgetTransferForms/'.$budgetTransferForm->id, $editedBudgetTransferForm);

        $this->assertApiResponse($editedBudgetTransferForm);
    }

    /**
     * @test
     */
    public function testDeleteBudgetTransferForm()
    {
        $budgetTransferForm = $this->makeBudgetTransferForm();
        $this->json('DELETE', '/api/v1/budgetTransferForms/'.$budgetTransferForm->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/budgetTransferForms/'.$budgetTransferForm->id);

        $this->assertResponseStatus(404);
    }
}
