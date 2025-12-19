<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetConsumedDataApiTest extends TestCase
{
    use MakeBudgetConsumedDataTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBudgetConsumedData()
    {
        $budgetConsumedData = $this->fakeBudgetConsumedDataData();
        $this->json('POST', '/api/v1/budgetConsumedDatas', $budgetConsumedData);

        $this->assertApiResponse($budgetConsumedData);
    }

    /**
     * @test
     */
    public function testReadBudgetConsumedData()
    {
        $budgetConsumedData = $this->makeBudgetConsumedData();
        $this->json('GET', '/api/v1/budgetConsumedDatas/'.$budgetConsumedData->id);

        $this->assertApiResponse($budgetConsumedData->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBudgetConsumedData()
    {
        $budgetConsumedData = $this->makeBudgetConsumedData();
        $editedBudgetConsumedData = $this->fakeBudgetConsumedDataData();

        $this->json('PUT', '/api/v1/budgetConsumedDatas/'.$budgetConsumedData->id, $editedBudgetConsumedData);

        $this->assertApiResponse($editedBudgetConsumedData);
    }

    /**
     * @test
     */
    public function testDeleteBudgetConsumedData()
    {
        $budgetConsumedData = $this->makeBudgetConsumedData();
        $this->json('DELETE', '/api/v1/budgetConsumedDatas/'.$budgetConsumedData->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/budgetConsumedDatas/'.$budgetConsumedData->id);

        $this->assertResponseStatus(404);
    }
}
