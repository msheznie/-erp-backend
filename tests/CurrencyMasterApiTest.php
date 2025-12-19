<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyMasterApiTest extends TestCase
{
    use MakeCurrencyMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCurrencyMaster()
    {
        $currencyMaster = $this->fakeCurrencyMasterData();
        $this->json('POST', '/api/v1/currencyMasters', $currencyMaster);

        $this->assertApiResponse($currencyMaster);
    }

    /**
     * @test
     */
    public function testReadCurrencyMaster()
    {
        $currencyMaster = $this->makeCurrencyMaster();
        $this->json('GET', '/api/v1/currencyMasters/'.$currencyMaster->id);

        $this->assertApiResponse($currencyMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCurrencyMaster()
    {
        $currencyMaster = $this->makeCurrencyMaster();
        $editedCurrencyMaster = $this->fakeCurrencyMasterData();

        $this->json('PUT', '/api/v1/currencyMasters/'.$currencyMaster->id, $editedCurrencyMaster);

        $this->assertApiResponse($editedCurrencyMaster);
    }

    /**
     * @test
     */
    public function testDeleteCurrencyMaster()
    {
        $currencyMaster = $this->makeCurrencyMaster();
        $this->json('DELETE', '/api/v1/currencyMasters/'.$currencyMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/currencyMasters/'.$currencyMaster->id);

        $this->assertResponseStatus(404);
    }
}
