<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGTaxMaster;

class POSSTAGTaxMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_tax_masters', $pOSSTAGTaxMaster
        );

        $this->assertApiResponse($pOSSTAGTaxMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_tax_masters/'.$pOSSTAGTaxMaster->id
        );

        $this->assertApiResponse($pOSSTAGTaxMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->create();
        $editedPOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_tax_masters/'.$pOSSTAGTaxMaster->id,
            $editedPOSSTAGTaxMaster
        );

        $this->assertApiResponse($editedPOSSTAGTaxMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_tax_masters/'.$pOSSTAGTaxMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_tax_masters/'.$pOSSTAGTaxMaster->id
        );

        $this->response->assertStatus(404);
    }
}
