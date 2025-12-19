<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGCustomerMaster;

class POSSTAGCustomerMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_customer_masters', $pOSSTAGCustomerMaster
        );

        $this->assertApiResponse($pOSSTAGCustomerMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_customer_masters/'.$pOSSTAGCustomerMaster->id
        );

        $this->assertApiResponse($pOSSTAGCustomerMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->create();
        $editedPOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_customer_masters/'.$pOSSTAGCustomerMaster->id,
            $editedPOSSTAGCustomerMaster
        );

        $this->assertApiResponse($editedPOSSTAGCustomerMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_customer_masters/'.$pOSSTAGCustomerMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_customer_masters/'.$pOSSTAGCustomerMaster->id
        );

        $this->response->assertStatus(404);
    }
}
