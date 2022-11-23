<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCECustomerMaster;

class POSSOURCECustomerMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_customer_masters', $pOSSOURCECustomerMaster
        );

        $this->assertApiResponse($pOSSOURCECustomerMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_customer_masters/'.$pOSSOURCECustomerMaster->id
        );

        $this->assertApiResponse($pOSSOURCECustomerMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->create();
        $editedPOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_customer_masters/'.$pOSSOURCECustomerMaster->id,
            $editedPOSSOURCECustomerMaster
        );

        $this->assertApiResponse($editedPOSSOURCECustomerMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_customer_masters/'.$pOSSOURCECustomerMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_customer_masters/'.$pOSSOURCECustomerMaster->id
        );

        $this->response->assertStatus(404);
    }
}
