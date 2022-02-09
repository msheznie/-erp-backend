<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PaymentType;

class PaymentTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_payment_type()
    {
        $paymentType = factory(PaymentType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/payment_types', $paymentType
        );

        $this->assertApiResponse($paymentType);
    }

    /**
     * @test
     */
    public function test_read_payment_type()
    {
        $paymentType = factory(PaymentType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/payment_types/'.$paymentType->id
        );

        $this->assertApiResponse($paymentType->toArray());
    }

    /**
     * @test
     */
    public function test_update_payment_type()
    {
        $paymentType = factory(PaymentType::class)->create();
        $editedPaymentType = factory(PaymentType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/payment_types/'.$paymentType->id,
            $editedPaymentType
        );

        $this->assertApiResponse($editedPaymentType);
    }

    /**
     * @test
     */
    public function test_delete_payment_type()
    {
        $paymentType = factory(PaymentType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/payment_types/'.$paymentType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/payment_types/'.$paymentType->id
        );

        $this->response->assertStatus(404);
    }
}
