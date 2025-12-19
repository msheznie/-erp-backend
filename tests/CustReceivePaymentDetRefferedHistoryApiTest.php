<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustReceivePaymentDetRefferedHistoryApiTest extends TestCase
{
    use MakeCustReceivePaymentDetRefferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->fakeCustReceivePaymentDetRefferedHistoryData();
        $this->json('POST', '/api/v1/custReceivePaymentDetRefferedHistories', $custReceivePaymentDetRefferedHistory);

        $this->assertApiResponse($custReceivePaymentDetRefferedHistory);
    }

    /**
     * @test
     */
    public function testReadCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->makeCustReceivePaymentDetRefferedHistory();
        $this->json('GET', '/api/v1/custReceivePaymentDetRefferedHistories/'.$custReceivePaymentDetRefferedHistory->id);

        $this->assertApiResponse($custReceivePaymentDetRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->makeCustReceivePaymentDetRefferedHistory();
        $editedCustReceivePaymentDetRefferedHistory = $this->fakeCustReceivePaymentDetRefferedHistoryData();

        $this->json('PUT', '/api/v1/custReceivePaymentDetRefferedHistories/'.$custReceivePaymentDetRefferedHistory->id, $editedCustReceivePaymentDetRefferedHistory);

        $this->assertApiResponse($editedCustReceivePaymentDetRefferedHistory);
    }

    /**
     * @test
     */
    public function testDeleteCustReceivePaymentDetRefferedHistory()
    {
        $custReceivePaymentDetRefferedHistory = $this->makeCustReceivePaymentDetRefferedHistory();
        $this->json('DELETE', '/api/v1/custReceivePaymentDetRefferedHistories/'.$custReceivePaymentDetRefferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/custReceivePaymentDetRefferedHistories/'.$custReceivePaymentDetRefferedHistory->id);

        $this->assertResponseStatus(404);
    }
}
