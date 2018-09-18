<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FreeBillingApiTest extends TestCase
{
    use MakeFreeBillingTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFreeBilling()
    {
        $freeBilling = $this->fakeFreeBillingData();
        $this->json('POST', '/api/v1/freeBillings', $freeBilling);

        $this->assertApiResponse($freeBilling);
    }

    /**
     * @test
     */
    public function testReadFreeBilling()
    {
        $freeBilling = $this->makeFreeBilling();
        $this->json('GET', '/api/v1/freeBillings/'.$freeBilling->id);

        $this->assertApiResponse($freeBilling->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFreeBilling()
    {
        $freeBilling = $this->makeFreeBilling();
        $editedFreeBilling = $this->fakeFreeBillingData();

        $this->json('PUT', '/api/v1/freeBillings/'.$freeBilling->id, $editedFreeBilling);

        $this->assertApiResponse($editedFreeBilling);
    }

    /**
     * @test
     */
    public function testDeleteFreeBilling()
    {
        $freeBilling = $this->makeFreeBilling();
        $this->json('DELETE', '/api/v1/freeBillings/'.$freeBilling->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/freeBillings/'.$freeBilling->id);

        $this->assertResponseStatus(404);
    }
}
