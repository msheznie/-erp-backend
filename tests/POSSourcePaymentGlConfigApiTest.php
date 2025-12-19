<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourcePaymentGlConfig;

class POSSourcePaymentGlConfigApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_payment_gl_configs', $pOSSourcePaymentGlConfig
        );

        $this->assertApiResponse($pOSSourcePaymentGlConfig);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_payment_gl_configs/'.$pOSSourcePaymentGlConfig->id
        );

        $this->assertApiResponse($pOSSourcePaymentGlConfig->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->create();
        $editedPOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_payment_gl_configs/'.$pOSSourcePaymentGlConfig->id,
            $editedPOSSourcePaymentGlConfig
        );

        $this->assertApiResponse($editedPOSSourcePaymentGlConfig);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_payment_gl_config()
    {
        $pOSSourcePaymentGlConfig = factory(POSSourcePaymentGlConfig::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_payment_gl_configs/'.$pOSSourcePaymentGlConfig->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_payment_gl_configs/'.$pOSSourcePaymentGlConfig->id
        );

        $this->response->assertStatus(404);
    }
}
