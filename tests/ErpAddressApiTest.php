<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErpAddressApiTest extends TestCase
{
    use MakeErpAddressTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateErpAddress()
    {
        $erpAddress = $this->fakeErpAddressData();
        $this->json('POST', '/api/v1/erpAddresses', $erpAddress);

        $this->assertApiResponse($erpAddress);
    }

    /**
     * @test
     */
    public function testReadErpAddress()
    {
        $erpAddress = $this->makeErpAddress();
        $this->json('GET', '/api/v1/erpAddresses/'.$erpAddress->id);

        $this->assertApiResponse($erpAddress->toArray());
    }

    /**
     * @test
     */
    public function testUpdateErpAddress()
    {
        $erpAddress = $this->makeErpAddress();
        $editedErpAddress = $this->fakeErpAddressData();

        $this->json('PUT', '/api/v1/erpAddresses/'.$erpAddress->id, $editedErpAddress);

        $this->assertApiResponse($editedErpAddress);
    }

    /**
     * @test
     */
    public function testDeleteErpAddress()
    {
        $erpAddress = $this->makeErpAddress();
        $this->json('DELETE', '/api/v1/erpAddresses/'.$erpAddress->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/erpAddresses/'.$erpAddress->id);

        $this->assertResponseStatus(404);
    }
}
