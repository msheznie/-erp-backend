<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentBankTransferDetailRefferedBackApiTest extends TestCase
{
    use MakePaymentBankTransferDetailRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->fakePaymentBankTransferDetailRefferedBackData();
        $this->json('POST', '/api/v1/paymentBankTransferDetailRefferedBacks', $paymentBankTransferDetailRefferedBack);

        $this->assertApiResponse($paymentBankTransferDetailRefferedBack);
    }

    /**
     * @test
     */
    public function testReadPaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->makePaymentBankTransferDetailRefferedBack();
        $this->json('GET', '/api/v1/paymentBankTransferDetailRefferedBacks/'.$paymentBankTransferDetailRefferedBack->id);

        $this->assertApiResponse($paymentBankTransferDetailRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->makePaymentBankTransferDetailRefferedBack();
        $editedPaymentBankTransferDetailRefferedBack = $this->fakePaymentBankTransferDetailRefferedBackData();

        $this->json('PUT', '/api/v1/paymentBankTransferDetailRefferedBacks/'.$paymentBankTransferDetailRefferedBack->id, $editedPaymentBankTransferDetailRefferedBack);

        $this->assertApiResponse($editedPaymentBankTransferDetailRefferedBack);
    }

    /**
     * @test
     */
    public function testDeletePaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->makePaymentBankTransferDetailRefferedBack();
        $this->json('DELETE', '/api/v1/paymentBankTransferDetailRefferedBacks/'.$paymentBankTransferDetailRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paymentBankTransferDetailRefferedBacks/'.$paymentBankTransferDetailRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
