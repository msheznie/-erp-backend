<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CurrencyConversionDetail;

class CurrencyConversionDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/currency_conversion_details', $currencyConversionDetail
        );

        $this->assertApiResponse($currencyConversionDetail);
    }

    /**
     * @test
     */
    public function test_read_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/currency_conversion_details/'.$currencyConversionDetail->id
        );

        $this->assertApiResponse($currencyConversionDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->create();
        $editedCurrencyConversionDetail = factory(CurrencyConversionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/currency_conversion_details/'.$currencyConversionDetail->id,
            $editedCurrencyConversionDetail
        );

        $this->assertApiResponse($editedCurrencyConversionDetail);
    }

    /**
     * @test
     */
    public function test_delete_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/currency_conversion_details/'.$currencyConversionDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/currency_conversion_details/'.$currencyConversionDetail->id
        );

        $this->response->assertStatus(404);
    }
}
