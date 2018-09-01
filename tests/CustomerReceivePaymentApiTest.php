<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerReceivePaymentApiTest extends TestCase
{
    use MakeCustomerReceivePaymentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerReceivePayment()
    {
        $customerReceivePayment = $this->fakeCustomerReceivePaymentData();
        $this->json('POST', '/api/v1/customerReceivePayments', $customerReceivePayment);

        $this->assertApiResponse($customerReceivePayment);
    }

    /**
     * @test
     */
    public function testReadCustomerReceivePayment()
    {
        $customerReceivePayment = $this->makeCustomerReceivePayment();
        $this->json('GET', '/api/v1/customerReceivePayments/'.$customerReceivePayment->id);

        $this->assertApiResponse($customerReceivePayment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerReceivePayment()
    {
        $customerReceivePayment = $this->makeCustomerReceivePayment();
        $editedCustomerReceivePayment = $this->fakeCustomerReceivePaymentData();

        $this->json('PUT', '/api/v1/customerReceivePayments/'.$customerReceivePayment->id, $editedCustomerReceivePayment);

        $this->assertApiResponse($editedCustomerReceivePayment);
    }

    /**
     * @test
     */
    public function testDeleteCustomerReceivePayment()
    {
        $customerReceivePayment = $this->makeCustomerReceivePayment();
        $this->json('DELETE', '/api/v1/customerReceivePayments/'.$customerReceivePayment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerReceivePayments/'.$customerReceivePayment->id);

        $this->assertResponseStatus(404);
    }
}
