<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetMasterApiTest extends TestCase
{
    use MakeBudgetMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBudgetMaster()
    {
        $budgetMaster = $this->fakeBudgetMasterData();
        $this->json('POST', '/api/v1/budgetMasters', $budgetMaster);

        $this->assertApiResponse($budgetMaster);
    }

    /**
     * @test
     */
    public function testReadBudgetMaster()
    {
        $budgetMaster = $this->makeBudgetMaster();
        $this->json('GET', '/api/v1/budgetMasters/'.$budgetMaster->id);

        $this->assertApiResponse($budgetMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBudgetMaster()
    {
        $budgetMaster = $this->makeBudgetMaster();
        $editedBudgetMaster = $this->fakeBudgetMasterData();

        $this->json('PUT', '/api/v1/budgetMasters/'.$budgetMaster->id, $editedBudgetMaster);

        $this->assertApiResponse($editedBudgetMaster);
    }

    /**
     * @test
     */
    public function testDeleteBudgetMaster()
    {
        $budgetMaster = $this->makeBudgetMaster();
        $this->json('DELETE', '/api/v1/budgetMasters/'.$budgetMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/budgetMasters/'.$budgetMaster->id);

        $this->assertResponseStatus(404);
    }
}
