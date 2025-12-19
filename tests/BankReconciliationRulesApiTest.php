<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankReconciliationRules;

class BankReconciliationRulesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bank_reconciliation_rules', $bankReconciliationRules
        );

        $this->assertApiResponse($bankReconciliationRules);
    }

    /**
     * @test
     */
    public function test_read_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bank_reconciliation_rules/'.$bankReconciliationRules->id
        );

        $this->assertApiResponse($bankReconciliationRules->toArray());
    }

    /**
     * @test
     */
    public function test_update_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->create();
        $editedBankReconciliationRules = factory(BankReconciliationRules::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bank_reconciliation_rules/'.$bankReconciliationRules->id,
            $editedBankReconciliationRules
        );

        $this->assertApiResponse($editedBankReconciliationRules);
    }

    /**
     * @test
     */
    public function test_delete_bank_reconciliation_rules()
    {
        $bankReconciliationRules = factory(BankReconciliationRules::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bank_reconciliation_rules/'.$bankReconciliationRules->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bank_reconciliation_rules/'.$bankReconciliationRules->id
        );

        $this->response->assertStatus(404);
    }
}
