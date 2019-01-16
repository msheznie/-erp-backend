<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyDenominationApiTest extends TestCase
{
    use MakeCurrencyDenominationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCurrencyDenomination()
    {
        $currencyDenomination = $this->fakeCurrencyDenominationData();
        $this->json('POST', '/api/v1/currencyDenominations', $currencyDenomination);

        $this->assertApiResponse($currencyDenomination);
    }

    /**
     * @test
     */
    public function testReadCurrencyDenomination()
    {
        $currencyDenomination = $this->makeCurrencyDenomination();
        $this->json('GET', '/api/v1/currencyDenominations/'.$currencyDenomination->id);

        $this->assertApiResponse($currencyDenomination->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCurrencyDenomination()
    {
        $currencyDenomination = $this->makeCurrencyDenomination();
        $editedCurrencyDenomination = $this->fakeCurrencyDenominationData();

        $this->json('PUT', '/api/v1/currencyDenominations/'.$currencyDenomination->id, $editedCurrencyDenomination);

        $this->assertApiResponse($editedCurrencyDenomination);
    }

    /**
     * @test
     */
    public function testDeleteCurrencyDenomination()
    {
        $currencyDenomination = $this->makeCurrencyDenomination();
        $this->json('DELETE', '/api/v1/currencyDenominations/'.$currencyDenomination->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/currencyDenominations/'.$currencyDenomination->id);

        $this->assertResponseStatus(404);
    }
}
