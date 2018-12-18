<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerMasterRefferedBackApiTest extends TestCase
{
    use MakeCustomerMasterRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->fakeCustomerMasterRefferedBackData();
        $this->json('POST', '/api/v1/customerMasterRefferedBacks', $customerMasterRefferedBack);

        $this->assertApiResponse($customerMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testReadCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->makeCustomerMasterRefferedBack();
        $this->json('GET', '/api/v1/customerMasterRefferedBacks/'.$customerMasterRefferedBack->id);

        $this->assertApiResponse($customerMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->makeCustomerMasterRefferedBack();
        $editedCustomerMasterRefferedBack = $this->fakeCustomerMasterRefferedBackData();

        $this->json('PUT', '/api/v1/customerMasterRefferedBacks/'.$customerMasterRefferedBack->id, $editedCustomerMasterRefferedBack);

        $this->assertApiResponse($editedCustomerMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->makeCustomerMasterRefferedBack();
        $this->json('DELETE', '/api/v1/customerMasterRefferedBacks/'.$customerMasterRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerMasterRefferedBacks/'.$customerMasterRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
