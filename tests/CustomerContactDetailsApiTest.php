<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerContactDetailsApiTest extends TestCase
{
    use MakeCustomerContactDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerContactDetails()
    {
        $customerContactDetails = $this->fakeCustomerContactDetailsData();
        $this->json('POST', '/api/v1/customerContactDetails', $customerContactDetails);

        $this->assertApiResponse($customerContactDetails);
    }

    /**
     * @test
     */
    public function testReadCustomerContactDetails()
    {
        $customerContactDetails = $this->makeCustomerContactDetails();
        $this->json('GET', '/api/v1/customerContactDetails/'.$customerContactDetails->id);

        $this->assertApiResponse($customerContactDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerContactDetails()
    {
        $customerContactDetails = $this->makeCustomerContactDetails();
        $editedCustomerContactDetails = $this->fakeCustomerContactDetailsData();

        $this->json('PUT', '/api/v1/customerContactDetails/'.$customerContactDetails->id, $editedCustomerContactDetails);

        $this->assertApiResponse($editedCustomerContactDetails);
    }

    /**
     * @test
     */
    public function testDeleteCustomerContactDetails()
    {
        $customerContactDetails = $this->makeCustomerContactDetails();
        $this->json('DELETE', '/api/v1/customerContactDetails/'.$customerContactDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerContactDetails/'.$customerContactDetails->id);

        $this->assertResponseStatus(404);
    }
}
