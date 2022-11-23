<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGPaymentGlConfigDetail;

class POSSTAGPaymentGlConfigDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_payment_gl_config_details', $pOSSTAGPaymentGlConfigDetail
        );

        $this->assertApiResponse($pOSSTAGPaymentGlConfigDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_payment_gl_config_details/'.$pOSSTAGPaymentGlConfigDetail->id
        );

        $this->assertApiResponse($pOSSTAGPaymentGlConfigDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->create();
        $editedPOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_payment_gl_config_details/'.$pOSSTAGPaymentGlConfigDetail->id,
            $editedPOSSTAGPaymentGlConfigDetail
        );

        $this->assertApiResponse($editedPOSSTAGPaymentGlConfigDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_payment_gl_config_details/'.$pOSSTAGPaymentGlConfigDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_payment_gl_config_details/'.$pOSSTAGPaymentGlConfigDetail->id
        );

        $this->response->assertStatus(404);
    }
}
