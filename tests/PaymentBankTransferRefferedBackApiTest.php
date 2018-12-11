<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentBankTransferRefferedBackApiTest extends TestCase
{
    use MakePaymentBankTransferRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->fakePaymentBankTransferRefferedBackData();
        $this->json('POST', '/api/v1/paymentBankTransferRefferedBacks', $paymentBankTransferRefferedBack);

        $this->assertApiResponse($paymentBankTransferRefferedBack);
    }

    /**
     * @test
     */
    public function testReadPaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->makePaymentBankTransferRefferedBack();
        $this->json('GET', '/api/v1/paymentBankTransferRefferedBacks/'.$paymentBankTransferRefferedBack->id);

        $this->assertApiResponse($paymentBankTransferRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->makePaymentBankTransferRefferedBack();
        $editedPaymentBankTransferRefferedBack = $this->fakePaymentBankTransferRefferedBackData();

        $this->json('PUT', '/api/v1/paymentBankTransferRefferedBacks/'.$paymentBankTransferRefferedBack->id, $editedPaymentBankTransferRefferedBack);

        $this->assertApiResponse($editedPaymentBankTransferRefferedBack);
    }

    /**
     * @test
     */
    public function testDeletePaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->makePaymentBankTransferRefferedBack();
        $this->json('DELETE', '/api/v1/paymentBankTransferRefferedBacks/'.$paymentBankTransferRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paymentBankTransferRefferedBacks/'.$paymentBankTransferRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
