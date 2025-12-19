<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCurrencyConversionHistoryTrait;
use Tests\ApiTestTrait;

class CurrencyConversionHistoryApiTest extends TestCase
{
    use MakeCurrencyConversionHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_currency_conversion_history()
    {
        $currencyConversionHistory = $this->fakeCurrencyConversionHistoryData();
        $this->response = $this->json('POST', '/api/currencyConversionHistories', $currencyConversionHistory);

        $this->assertApiResponse($currencyConversionHistory);
    }

    /**
     * @test
     */
    public function test_read_currency_conversion_history()
    {
        $currencyConversionHistory = $this->makeCurrencyConversionHistory();
        $this->response = $this->json('GET', '/api/currencyConversionHistories/'.$currencyConversionHistory->id);

        $this->assertApiResponse($currencyConversionHistory->toArray());
    }

    /**
     * @test
     */
    public function test_update_currency_conversion_history()
    {
        $currencyConversionHistory = $this->makeCurrencyConversionHistory();
        $editedCurrencyConversionHistory = $this->fakeCurrencyConversionHistoryData();

        $this->response = $this->json('PUT', '/api/currencyConversionHistories/'.$currencyConversionHistory->id, $editedCurrencyConversionHistory);

        $this->assertApiResponse($editedCurrencyConversionHistory);
    }

    /**
     * @test
     */
    public function test_delete_currency_conversion_history()
    {
        $currencyConversionHistory = $this->makeCurrencyConversionHistory();
        $this->response = $this->json('DELETE', '/api/currencyConversionHistories/'.$currencyConversionHistory->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/currencyConversionHistories/'.$currencyConversionHistory->id);

        $this->response->assertStatus(404);
    }
}
