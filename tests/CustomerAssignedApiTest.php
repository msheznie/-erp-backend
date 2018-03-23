<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerAssignedApiTest extends TestCase
{
    use MakeCustomerAssignedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerAssigned()
    {
        $customerAssigned = $this->fakeCustomerAssignedData();
        $this->json('POST', '/api/v1/customerAssigneds', $customerAssigned);

        $this->assertApiResponse($customerAssigned);
    }

    /**
     * @test
     */
    public function testReadCustomerAssigned()
    {
        $customerAssigned = $this->makeCustomerAssigned();
        $this->json('GET', '/api/v1/customerAssigneds/'.$customerAssigned->id);

        $this->assertApiResponse($customerAssigned->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerAssigned()
    {
        $customerAssigned = $this->makeCustomerAssigned();
        $editedCustomerAssigned = $this->fakeCustomerAssignedData();

        $this->json('PUT', '/api/v1/customerAssigneds/'.$customerAssigned->id, $editedCustomerAssigned);

        $this->assertApiResponse($editedCustomerAssigned);
    }

    /**
     * @test
     */
    public function testDeleteCustomerAssigned()
    {
        $customerAssigned = $this->makeCustomerAssigned();
        $this->json('DELETE', '/api/v1/customerAssigneds/'.$customerAssigned->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerAssigneds/'.$customerAssigned->id);

        $this->assertResponseStatus(404);
    }
}
