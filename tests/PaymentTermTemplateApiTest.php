<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PaymentTermTemplate;

class PaymentTermTemplateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/payment_term_templates', $paymentTermTemplate
        );

        $this->assertApiResponse($paymentTermTemplate);
    }

    /**
     * @test
     */
    public function test_read_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/payment_term_templates/'.$paymentTermTemplate->id
        );

        $this->assertApiResponse($paymentTermTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->create();
        $editedPaymentTermTemplate = factory(PaymentTermTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/payment_term_templates/'.$paymentTermTemplate->id,
            $editedPaymentTermTemplate
        );

        $this->assertApiResponse($editedPaymentTermTemplate);
    }

    /**
     * @test
     */
    public function test_delete_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/payment_term_templates/'.$paymentTermTemplate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/payment_term_templates/'.$paymentTermTemplate->id
        );

        $this->response->assertStatus(404);
    }
}
