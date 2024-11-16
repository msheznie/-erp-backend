<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PaymentVoucherBankChargeDetails;

class PaymentVoucherBankChargeDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/payment_voucher_bank_charge_details', $paymentVoucherBankChargeDetails
        );

        $this->assertApiResponse($paymentVoucherBankChargeDetails);
    }

    /**
     * @test
     */
    public function test_read_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/payment_voucher_bank_charge_details/'.$paymentVoucherBankChargeDetails->id
        );

        $this->assertApiResponse($paymentVoucherBankChargeDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->create();
        $editedPaymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/payment_voucher_bank_charge_details/'.$paymentVoucherBankChargeDetails->id,
            $editedPaymentVoucherBankChargeDetails
        );

        $this->assertApiResponse($editedPaymentVoucherBankChargeDetails);
    }

    /**
     * @test
     */
    public function test_delete_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/payment_voucher_bank_charge_details/'.$paymentVoucherBankChargeDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/payment_voucher_bank_charge_details/'.$paymentVoucherBankChargeDetails->id
        );

        $this->response->assertStatus(404);
    }
}
