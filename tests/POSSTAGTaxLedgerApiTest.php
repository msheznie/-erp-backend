<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGTaxLedger;

class POSSTAGTaxLedgerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_tax_ledgers', $pOSSTAGTaxLedger
        );

        $this->assertApiResponse($pOSSTAGTaxLedger);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_tax_ledgers/'.$pOSSTAGTaxLedger->id
        );

        $this->assertApiResponse($pOSSTAGTaxLedger->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->create();
        $editedPOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_tax_ledgers/'.$pOSSTAGTaxLedger->id,
            $editedPOSSTAGTaxLedger
        );

        $this->assertApiResponse($editedPOSSTAGTaxLedger);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_tax_ledger()
    {
        $pOSSTAGTaxLedger = factory(POSSTAGTaxLedger::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_tax_ledgers/'.$pOSSTAGTaxLedger->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_tax_ledgers/'.$pOSSTAGTaxLedger->id
        );

        $this->response->assertStatus(404);
    }
}
