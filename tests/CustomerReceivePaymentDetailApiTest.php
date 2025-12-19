<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerReceivePaymentDetailApiTest extends TestCase
{
    use MakeCustomerReceivePaymentDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->fakeCustomerReceivePaymentDetailData();
        $this->json('POST', '/api/v1/customerReceivePaymentDetails', $customerReceivePaymentDetail);

        $this->assertApiResponse($customerReceivePaymentDetail);
    }

    /**
     * @test
     */
    public function testReadCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->makeCustomerReceivePaymentDetail();
        $this->json('GET', '/api/v1/customerReceivePaymentDetails/'.$customerReceivePaymentDetail->id);

        $this->assertApiResponse($customerReceivePaymentDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->makeCustomerReceivePaymentDetail();
        $editedCustomerReceivePaymentDetail = $this->fakeCustomerReceivePaymentDetailData();

        $this->json('PUT', '/api/v1/customerReceivePaymentDetails/'.$customerReceivePaymentDetail->id, $editedCustomerReceivePaymentDetail);

        $this->assertApiResponse($editedCustomerReceivePaymentDetail);
    }

    /**
     * @test
     */
    public function testDeleteCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->makeCustomerReceivePaymentDetail();
        $this->json('DELETE', '/api/v1/customerReceivePaymentDetails/'.$customerReceivePaymentDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerReceivePaymentDetails/'.$customerReceivePaymentDetail->id);

        $this->assertResponseStatus(404);
    }
}
