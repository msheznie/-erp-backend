<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimApiTest extends TestCase
{
    use MakeExpenseClaimTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateExpenseClaim()
    {
        $expenseClaim = $this->fakeExpenseClaimData();
        $this->json('POST', '/api/v1/expenseClaims', $expenseClaim);

        $this->assertApiResponse($expenseClaim);
    }

    /**
     * @test
     */
    public function testReadExpenseClaim()
    {
        $expenseClaim = $this->makeExpenseClaim();
        $this->json('GET', '/api/v1/expenseClaims/'.$expenseClaim->id);

        $this->assertApiResponse($expenseClaim->toArray());
    }

    /**
     * @test
     */
    public function testUpdateExpenseClaim()
    {
        $expenseClaim = $this->makeExpenseClaim();
        $editedExpenseClaim = $this->fakeExpenseClaimData();

        $this->json('PUT', '/api/v1/expenseClaims/'.$expenseClaim->id, $editedExpenseClaim);

        $this->assertApiResponse($editedExpenseClaim);
    }

    /**
     * @test
     */
    public function testDeleteExpenseClaim()
    {
        $expenseClaim = $this->makeExpenseClaim();
        $this->json('DELETE', '/api/v1/expenseClaims/'.$expenseClaim->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/expenseClaims/'.$expenseClaim->id);

        $this->assertResponseStatus(404);
    }
}
