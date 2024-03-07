<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PaymentTermTemplateAssigned;

class PaymentTermTemplateAssignedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/payment_term_template_assigneds', $paymentTermTemplateAssigned
        );

        $this->assertApiResponse($paymentTermTemplateAssigned);
    }

    /**
     * @test
     */
    public function test_read_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/payment_term_template_assigneds/'.$paymentTermTemplateAssigned->id
        );

        $this->assertApiResponse($paymentTermTemplateAssigned->toArray());
    }

    /**
     * @test
     */
    public function test_update_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->create();
        $editedPaymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/payment_term_template_assigneds/'.$paymentTermTemplateAssigned->id,
            $editedPaymentTermTemplateAssigned
        );

        $this->assertApiResponse($editedPaymentTermTemplateAssigned);
    }

    /**
     * @test
     */
    public function test_delete_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/payment_term_template_assigneds/'.$paymentTermTemplateAssigned->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/payment_term_template_assigneds/'.$paymentTermTemplateAssigned->id
        );

        $this->response->assertStatus(404);
    }
}
