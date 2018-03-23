<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerMasterApiTest extends TestCase
{
    use MakeCustomerMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerMaster()
    {
        $customerMaster = $this->fakeCustomerMasterData();
        $this->json('POST', '/api/v1/customerMasters', $customerMaster);

        $this->assertApiResponse($customerMaster);
    }

    /**
     * @test
     */
    public function testReadCustomerMaster()
    {
        $customerMaster = $this->makeCustomerMaster();
        $this->json('GET', '/api/v1/customerMasters/'.$customerMaster->id);

        $this->assertApiResponse($customerMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerMaster()
    {
        $customerMaster = $this->makeCustomerMaster();
        $editedCustomerMaster = $this->fakeCustomerMasterData();

        $this->json('PUT', '/api/v1/customerMasters/'.$customerMaster->id, $editedCustomerMaster);

        $this->assertApiResponse($editedCustomerMaster);
    }

    /**
     * @test
     */
    public function testDeleteCustomerMaster()
    {
        $customerMaster = $this->makeCustomerMaster();
        $this->json('DELETE', '/api/v1/customerMasters/'.$customerMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerMasters/'.$customerMaster->id);

        $this->assertResponseStatus(404);
    }
}
