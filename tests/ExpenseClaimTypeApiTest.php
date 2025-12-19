<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimTypeApiTest extends TestCase
{
    use MakeExpenseClaimTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateExpenseClaimType()
    {
        $expenseClaimType = $this->fakeExpenseClaimTypeData();
        $this->json('POST', '/api/v1/expenseClaimTypes', $expenseClaimType);

        $this->assertApiResponse($expenseClaimType);
    }

    /**
     * @test
     */
    public function testReadExpenseClaimType()
    {
        $expenseClaimType = $this->makeExpenseClaimType();
        $this->json('GET', '/api/v1/expenseClaimTypes/'.$expenseClaimType->id);

        $this->assertApiResponse($expenseClaimType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateExpenseClaimType()
    {
        $expenseClaimType = $this->makeExpenseClaimType();
        $editedExpenseClaimType = $this->fakeExpenseClaimTypeData();

        $this->json('PUT', '/api/v1/expenseClaimTypes/'.$expenseClaimType->id, $editedExpenseClaimType);

        $this->assertApiResponse($editedExpenseClaimType);
    }

    /**
     * @test
     */
    public function testDeleteExpenseClaimType()
    {
        $expenseClaimType = $this->makeExpenseClaimType();
        $this->json('DELETE', '/api/v1/expenseClaimTypes/'.$expenseClaimType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/expenseClaimTypes/'.$expenseClaimType->id);

        $this->assertResponseStatus(404);
    }
}
