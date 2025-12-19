<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankReconciliationTemplateMapping;

class BankReconciliationTemplateMappingApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bank_reconciliation_template_mappings', $bankReconciliationTemplateMapping
        );

        $this->assertApiResponse($bankReconciliationTemplateMapping);
    }

    /**
     * @test
     */
    public function test_read_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bank_reconciliation_template_mappings/'.$bankReconciliationTemplateMapping->id
        );

        $this->assertApiResponse($bankReconciliationTemplateMapping->toArray());
    }

    /**
     * @test
     */
    public function test_update_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->create();
        $editedBankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bank_reconciliation_template_mappings/'.$bankReconciliationTemplateMapping->id,
            $editedBankReconciliationTemplateMapping
        );

        $this->assertApiResponse($editedBankReconciliationTemplateMapping);
    }

    /**
     * @test
     */
    public function test_delete_bank_reconciliation_template_mapping()
    {
        $bankReconciliationTemplateMapping = factory(BankReconciliationTemplateMapping::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bank_reconciliation_template_mappings/'.$bankReconciliationTemplateMapping->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bank_reconciliation_template_mappings/'.$bankReconciliationTemplateMapping->id
        );

        $this->response->assertStatus(404);
    }
}
