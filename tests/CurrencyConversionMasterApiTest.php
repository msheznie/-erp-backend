<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CurrencyConversionMaster;

class CurrencyConversionMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/currency_conversion_masters', $currencyConversionMaster
        );

        $this->assertApiResponse($currencyConversionMaster);
    }

    /**
     * @test
     */
    public function test_read_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/currency_conversion_masters/'.$currencyConversionMaster->id
        );

        $this->assertApiResponse($currencyConversionMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->create();
        $editedCurrencyConversionMaster = factory(CurrencyConversionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/currency_conversion_masters/'.$currencyConversionMaster->id,
            $editedCurrencyConversionMaster
        );

        $this->assertApiResponse($editedCurrencyConversionMaster);
    }

    /**
     * @test
     */
    public function test_delete_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/currency_conversion_masters/'.$currencyConversionMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/currency_conversion_masters/'.$currencyConversionMaster->id
        );

        $this->response->assertStatus(404);
    }
}
