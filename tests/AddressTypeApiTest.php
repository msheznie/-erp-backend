<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressTypeApiTest extends TestCase
{
    use MakeAddressTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAddressType()
    {
        $addressType = $this->fakeAddressTypeData();
        $this->json('POST', '/api/v1/addressTypes', $addressType);

        $this->assertApiResponse($addressType);
    }

    /**
     * @test
     */
    public function testReadAddressType()
    {
        $addressType = $this->makeAddressType();
        $this->json('GET', '/api/v1/addressTypes/'.$addressType->id);

        $this->assertApiResponse($addressType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAddressType()
    {
        $addressType = $this->makeAddressType();
        $editedAddressType = $this->fakeAddressTypeData();

        $this->json('PUT', '/api/v1/addressTypes/'.$addressType->id, $editedAddressType);

        $this->assertApiResponse($editedAddressType);
    }

    /**
     * @test
     */
    public function testDeleteAddressType()
    {
        $addressType = $this->makeAddressType();
        $this->json('DELETE', '/api/v1/addressTypes/'.$addressType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/addressTypes/'.$addressType->id);

        $this->assertResponseStatus(404);
    }
}
