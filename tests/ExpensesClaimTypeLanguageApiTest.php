<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExpensesClaimTypeLanguage;

class ExpensesClaimTypeLanguageApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/expenses_claim_type_languages', $expensesClaimTypeLanguage
        );

        $this->assertApiResponse($expensesClaimTypeLanguage);
    }

    /**
     * @test
     */
    public function test_read_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/expenses_claim_type_languages/'.$expensesClaimTypeLanguage->id
        );

        $this->assertApiResponse($expensesClaimTypeLanguage->toArray());
    }

    /**
     * @test
     */
    public function test_update_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->create();
        $editedExpensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/expenses_claim_type_languages/'.$expensesClaimTypeLanguage->id,
            $editedExpensesClaimTypeLanguage
        );

        $this->assertApiResponse($editedExpensesClaimTypeLanguage);
    }

    /**
     * @test
     */
    public function test_delete_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/expenses_claim_type_languages/'.$expensesClaimTypeLanguage->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/expenses_claim_type_languages/'.$expensesClaimTypeLanguage->id
        );

        $this->response->assertStatus(404);
    }
}
