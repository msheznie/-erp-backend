<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimCategoriesApiTest extends TestCase
{
    use MakeExpenseClaimCategoriesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->fakeExpenseClaimCategoriesData();
        $this->json('POST', '/api/v1/expenseClaimCategories', $expenseClaimCategories);

        $this->assertApiResponse($expenseClaimCategories);
    }

    /**
     * @test
     */
    public function testReadExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->makeExpenseClaimCategories();
        $this->json('GET', '/api/v1/expenseClaimCategories/'.$expenseClaimCategories->id);

        $this->assertApiResponse($expenseClaimCategories->toArray());
    }

    /**
     * @test
     */
    public function testUpdateExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->makeExpenseClaimCategories();
        $editedExpenseClaimCategories = $this->fakeExpenseClaimCategoriesData();

        $this->json('PUT', '/api/v1/expenseClaimCategories/'.$expenseClaimCategories->id, $editedExpenseClaimCategories);

        $this->assertApiResponse($editedExpenseClaimCategories);
    }

    /**
     * @test
     */
    public function testDeleteExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->makeExpenseClaimCategories();
        $this->json('DELETE', '/api/v1/expenseClaimCategories/'.$expenseClaimCategories->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/expenseClaimCategories/'.$expenseClaimCategories->id);

        $this->assertResponseStatus(404);
    }
}
