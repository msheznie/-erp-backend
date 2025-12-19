<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyConversionApiTest extends TestCase
{
    use MakeCurrencyConversionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCurrencyConversion()
    {
        $currencyConversion = $this->fakeCurrencyConversionData();
        $this->json('POST', '/api/v1/currencyConversions', $currencyConversion);

        $this->assertApiResponse($currencyConversion);
    }

    /**
     * @test
     */
    public function testReadCurrencyConversion()
    {
        $currencyConversion = $this->makeCurrencyConversion();
        $this->json('GET', '/api/v1/currencyConversions/'.$currencyConversion->id);

        $this->assertApiResponse($currencyConversion->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCurrencyConversion()
    {
        $currencyConversion = $this->makeCurrencyConversion();
        $editedCurrencyConversion = $this->fakeCurrencyConversionData();

        $this->json('PUT', '/api/v1/currencyConversions/'.$currencyConversion->id, $editedCurrencyConversion);

        $this->assertApiResponse($editedCurrencyConversion);
    }

    /**
     * @test
     */
    public function testDeleteCurrencyConversion()
    {
        $currencyConversion = $this->makeCurrencyConversion();
        $this->json('DELETE', '/api/v1/currencyConversions/'.$currencyConversion->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/currencyConversions/'.$currencyConversion->id);

        $this->assertResponseStatus(404);
    }
}
