<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimDetailsApiTest extends TestCase
{
    use MakeExpenseClaimDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->fakeExpenseClaimDetailsData();
        $this->json('POST', '/api/v1/expenseClaimDetails', $expenseClaimDetails);

        $this->assertApiResponse($expenseClaimDetails);
    }

    /**
     * @test
     */
    public function testReadExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->makeExpenseClaimDetails();
        $this->json('GET', '/api/v1/expenseClaimDetails/'.$expenseClaimDetails->id);

        $this->assertApiResponse($expenseClaimDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->makeExpenseClaimDetails();
        $editedExpenseClaimDetails = $this->fakeExpenseClaimDetailsData();

        $this->json('PUT', '/api/v1/expenseClaimDetails/'.$expenseClaimDetails->id, $editedExpenseClaimDetails);

        $this->assertApiResponse($editedExpenseClaimDetails);
    }

    /**
     * @test
     */
    public function testDeleteExpenseClaimDetails()
    {
        $expenseClaimDetails = $this->makeExpenseClaimDetails();
        $this->json('DELETE', '/api/v1/expenseClaimDetails/'.$expenseClaimDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/expenseClaimDetails/'.$expenseClaimDetails->id);

        $this->assertResponseStatus(404);
    }
}
