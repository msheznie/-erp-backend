<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCETaxMaster;

class POSSOURCETaxMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_tax_masters', $pOSSOURCETaxMaster
        );

        $this->assertApiResponse($pOSSOURCETaxMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_tax_masters/'.$pOSSOURCETaxMaster->id
        );

        $this->assertApiResponse($pOSSOURCETaxMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->create();
        $editedPOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_tax_masters/'.$pOSSOURCETaxMaster->id,
            $editedPOSSOURCETaxMaster
        );

        $this->assertApiResponse($editedPOSSOURCETaxMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_tax_masters/'.$pOSSOURCETaxMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_tax_masters/'.$pOSSOURCETaxMaster->id
        );

        $this->response->assertStatus(404);
    }
}
