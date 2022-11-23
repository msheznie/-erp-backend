<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCETaxLedger;

class POSSOURCETaxLedgerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_tax_ledgers', $pOSSOURCETaxLedger
        );

        $this->assertApiResponse($pOSSOURCETaxLedger);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_tax_ledgers/'.$pOSSOURCETaxLedger->id
        );

        $this->assertApiResponse($pOSSOURCETaxLedger->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->create();
        $editedPOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_tax_ledgers/'.$pOSSOURCETaxLedger->id,
            $editedPOSSOURCETaxLedger
        );

        $this->assertApiResponse($editedPOSSOURCETaxLedger);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_tax_ledger()
    {
        $pOSSOURCETaxLedger = factory(POSSOURCETaxLedger::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_tax_ledgers/'.$pOSSOURCETaxLedger->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_tax_ledgers/'.$pOSSOURCETaxLedger->id
        );

        $this->response->assertStatus(404);
    }
}
