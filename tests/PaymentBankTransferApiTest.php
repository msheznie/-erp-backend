<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentBankTransferApiTest extends TestCase
{
    use MakePaymentBankTransferTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaymentBankTransfer()
    {
        $paymentBankTransfer = $this->fakePaymentBankTransferData();
        $this->json('POST', '/api/v1/paymentBankTransfers', $paymentBankTransfer);

        $this->assertApiResponse($paymentBankTransfer);
    }

    /**
     * @test
     */
    public function testReadPaymentBankTransfer()
    {
        $paymentBankTransfer = $this->makePaymentBankTransfer();
        $this->json('GET', '/api/v1/paymentBankTransfers/'.$paymentBankTransfer->id);

        $this->assertApiResponse($paymentBankTransfer->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaymentBankTransfer()
    {
        $paymentBankTransfer = $this->makePaymentBankTransfer();
        $editedPaymentBankTransfer = $this->fakePaymentBankTransferData();

        $this->json('PUT', '/api/v1/paymentBankTransfers/'.$paymentBankTransfer->id, $editedPaymentBankTransfer);

        $this->assertApiResponse($editedPaymentBankTransfer);
    }

    /**
     * @test
     */
    public function testDeletePaymentBankTransfer()
    {
        $paymentBankTransfer = $this->makePaymentBankTransfer();
        $this->json('DELETE', '/api/v1/paymentBankTransfers/'.$paymentBankTransfer->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paymentBankTransfers/'.$paymentBankTransfer->id);

        $this->assertResponseStatus(404);
    }
}
