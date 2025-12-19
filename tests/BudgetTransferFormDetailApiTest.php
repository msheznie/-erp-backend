<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudgetTransferFormDetailApiTest extends TestCase
{
    use MakeBudgetTransferFormDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->fakeBudgetTransferFormDetailData();
        $this->json('POST', '/api/v1/budgetTransferFormDetails', $budgetTransferFormDetail);

        $this->assertApiResponse($budgetTransferFormDetail);
    }

    /**
     * @test
     */
    public function testReadBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->makeBudgetTransferFormDetail();
        $this->json('GET', '/api/v1/budgetTransferFormDetails/'.$budgetTransferFormDetail->id);

        $this->assertApiResponse($budgetTransferFormDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->makeBudgetTransferFormDetail();
        $editedBudgetTransferFormDetail = $this->fakeBudgetTransferFormDetailData();

        $this->json('PUT', '/api/v1/budgetTransferFormDetails/'.$budgetTransferFormDetail->id, $editedBudgetTransferFormDetail);

        $this->assertApiResponse($editedBudgetTransferFormDetail);
    }

    /**
     * @test
     */
    public function testDeleteBudgetTransferFormDetail()
    {
        $budgetTransferFormDetail = $this->makeBudgetTransferFormDetail();
        $this->json('DELETE', '/api/v1/budgetTransferFormDetails/'.$budgetTransferFormDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/budgetTransferFormDetails/'.$budgetTransferFormDetail->id);

        $this->assertResponseStatus(404);
    }
}
