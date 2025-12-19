<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagPaymentGlConfig;

class POSStagPaymentGlConfigApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_payment_gl_configs', $pOSStagPaymentGlConfig
        );

        $this->assertApiResponse($pOSStagPaymentGlConfig);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_payment_gl_configs/'.$pOSStagPaymentGlConfig->id
        );

        $this->assertApiResponse($pOSStagPaymentGlConfig->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->create();
        $editedPOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_payment_gl_configs/'.$pOSStagPaymentGlConfig->id,
            $editedPOSStagPaymentGlConfig
        );

        $this->assertApiResponse($editedPOSStagPaymentGlConfig);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_payment_gl_config()
    {
        $pOSStagPaymentGlConfig = factory(POSStagPaymentGlConfig::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_payment_gl_configs/'.$pOSStagPaymentGlConfig->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_payment_gl_configs/'.$pOSStagPaymentGlConfig->id
        );

        $this->response->assertStatus(404);
    }
}
