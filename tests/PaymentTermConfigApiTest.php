<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PaymentTermConfig;

class PaymentTermConfigApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/payment_term_configs', $paymentTermConfig
        );

        $this->assertApiResponse($paymentTermConfig);
    }

    /**
     * @test
     */
    public function test_read_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/payment_term_configs/'.$paymentTermConfig->id
        );

        $this->assertApiResponse($paymentTermConfig->toArray());
    }

    /**
     * @test
     */
    public function test_update_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->create();
        $editedPaymentTermConfig = factory(PaymentTermConfig::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/payment_term_configs/'.$paymentTermConfig->id,
            $editedPaymentTermConfig
        );

        $this->assertApiResponse($editedPaymentTermConfig);
    }

    /**
     * @test
     */
    public function test_delete_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/payment_term_configs/'.$paymentTermConfig->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/payment_term_configs/'.$paymentTermConfig->id
        );

        $this->response->assertStatus(404);
    }
}
