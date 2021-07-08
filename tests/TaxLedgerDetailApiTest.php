<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TaxLedgerDetail;

class TaxLedgerDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tax_ledger_details', $taxLedgerDetail
        );

        $this->assertApiResponse($taxLedgerDetail);
    }

    /**
     * @test
     */
    public function test_read_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tax_ledger_details/'.$taxLedgerDetail->id
        );

        $this->assertApiResponse($taxLedgerDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->create();
        $editedTaxLedgerDetail = factory(TaxLedgerDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tax_ledger_details/'.$taxLedgerDetail->id,
            $editedTaxLedgerDetail
        );

        $this->assertApiResponse($editedTaxLedgerDetail);
    }

    /**
     * @test
     */
    public function test_delete_tax_ledger_detail()
    {
        $taxLedgerDetail = factory(TaxLedgerDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tax_ledger_details/'.$taxLedgerDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tax_ledger_details/'.$taxLedgerDetail->id
        );

        $this->response->assertStatus(404);
    }
}
