<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerReceivePaymentRefferedHistoryApiTest extends TestCase
{
    use MakeCustomerReceivePaymentRefferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->fakeCustomerReceivePaymentRefferedHistoryData();
        $this->json('POST', '/api/v1/customerReceivePaymentRefferedHistories', $customerReceivePaymentRefferedHistory);

        $this->assertApiResponse($customerReceivePaymentRefferedHistory);
    }

    /**
     * @test
     */
    public function testReadCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->makeCustomerReceivePaymentRefferedHistory();
        $this->json('GET', '/api/v1/customerReceivePaymentRefferedHistories/'.$customerReceivePaymentRefferedHistory->id);

        $this->assertApiResponse($customerReceivePaymentRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->makeCustomerReceivePaymentRefferedHistory();
        $editedCustomerReceivePaymentRefferedHistory = $this->fakeCustomerReceivePaymentRefferedHistoryData();

        $this->json('PUT', '/api/v1/customerReceivePaymentRefferedHistories/'.$customerReceivePaymentRefferedHistory->id, $editedCustomerReceivePaymentRefferedHistory);

        $this->assertApiResponse($editedCustomerReceivePaymentRefferedHistory);
    }

    /**
     * @test
     */
    public function testDeleteCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->makeCustomerReceivePaymentRefferedHistory();
        $this->json('DELETE', '/api/v1/customerReceivePaymentRefferedHistories/'.$customerReceivePaymentRefferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerReceivePaymentRefferedHistories/'.$customerReceivePaymentRefferedHistory->id);

        $this->assertResponseStatus(404);
    }
}
