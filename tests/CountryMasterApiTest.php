<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryMasterApiTest extends TestCase
{
    use MakeCountryMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCountryMaster()
    {
        $countryMaster = $this->fakeCountryMasterData();
        $this->json('POST', '/api/v1/countryMasters', $countryMaster);

        $this->assertApiResponse($countryMaster);
    }

    /**
     * @test
     */
    public function testReadCountryMaster()
    {
        $countryMaster = $this->makeCountryMaster();
        $this->json('GET', '/api/v1/countryMasters/'.$countryMaster->id);

        $this->assertApiResponse($countryMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCountryMaster()
    {
        $countryMaster = $this->makeCountryMaster();
        $editedCountryMaster = $this->fakeCountryMasterData();

        $this->json('PUT', '/api/v1/countryMasters/'.$countryMaster->id, $editedCountryMaster);

        $this->assertApiResponse($editedCountryMaster);
    }

    /**
     * @test
     */
    public function testDeleteCountryMaster()
    {
        $countryMaster = $this->makeCountryMaster();
        $this->json('DELETE', '/api/v1/countryMasters/'.$countryMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/countryMasters/'.$countryMaster->id);

        $this->assertResponseStatus(404);
    }
}
