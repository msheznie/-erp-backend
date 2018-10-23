<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetAdjustmentApiTest extends TestCase
{
    use MakeBudgetAdjustmentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBudgetAdjustment()
    {
        $budgetAdjustment = $this->fakeBudgetAdjustmentData();
        $this->json('POST', '/api/v1/budgetAdjustments', $budgetAdjustment);

        $this->assertApiResponse($budgetAdjustment);
    }

    /**
     * @test
     */
    public function testReadBudgetAdjustment()
    {
        $budgetAdjustment = $this->makeBudgetAdjustment();
        $this->json('GET', '/api/v1/budgetAdjustments/'.$budgetAdjustment->id);

        $this->assertApiResponse($budgetAdjustment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBudgetAdjustment()
    {
        $budgetAdjustment = $this->makeBudgetAdjustment();
        $editedBudgetAdjustment = $this->fakeBudgetAdjustmentData();

        $this->json('PUT', '/api/v1/budgetAdjustments/'.$budgetAdjustment->id, $editedBudgetAdjustment);

        $this->assertApiResponse($editedBudgetAdjustment);
    }

    /**
     * @test
     */
    public function testDeleteBudgetAdjustment()
    {
        $budgetAdjustment = $this->makeBudgetAdjustment();
        $this->json('DELETE', '/api/v1/budgetAdjustments/'.$budgetAdjustment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/budgetAdjustments/'.$budgetAdjustment->id);

        $this->assertResponseStatus(404);
    }
}
