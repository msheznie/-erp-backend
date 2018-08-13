<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FreeBillingMasterPerformaApiTest extends TestCase
{
    use MakeFreeBillingMasterPerformaTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->fakeFreeBillingMasterPerformaData();
        $this->json('POST', '/api/v1/freeBillingMasterPerformas', $freeBillingMasterPerforma);

        $this->assertApiResponse($freeBillingMasterPerforma);
    }

    /**
     * @test
     */
    public function testReadFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->makeFreeBillingMasterPerforma();
        $this->json('GET', '/api/v1/freeBillingMasterPerformas/'.$freeBillingMasterPerforma->id);

        $this->assertApiResponse($freeBillingMasterPerforma->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->makeFreeBillingMasterPerforma();
        $editedFreeBillingMasterPerforma = $this->fakeFreeBillingMasterPerformaData();

        $this->json('PUT', '/api/v1/freeBillingMasterPerformas/'.$freeBillingMasterPerforma->id, $editedFreeBillingMasterPerforma);

        $this->assertApiResponse($editedFreeBillingMasterPerforma);
    }

    /**
     * @test
     */
    public function testDeleteFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->makeFreeBillingMasterPerforma();
        $this->json('DELETE', '/api/v1/freeBillingMasterPerformas/'.$freeBillingMasterPerforma->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/freeBillingMasterPerformas/'.$freeBillingMasterPerforma->id);

        $this->assertResponseStatus(404);
    }
}
