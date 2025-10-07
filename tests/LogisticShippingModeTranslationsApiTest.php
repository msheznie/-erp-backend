<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LogisticShippingModeTranslations;

class LogisticShippingModeTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/logistic_shipping_mode_translations', $logisticShippingModeTranslations
        );

        $this->assertApiResponse($logisticShippingModeTranslations);
    }

    /**
     * @test
     */
    public function test_read_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/logistic_shipping_mode_translations/'.$logisticShippingModeTranslations->id
        );

        $this->assertApiResponse($logisticShippingModeTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->create();
        $editedLogisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/logistic_shipping_mode_translations/'.$logisticShippingModeTranslations->id,
            $editedLogisticShippingModeTranslations
        );

        $this->assertApiResponse($editedLogisticShippingModeTranslations);
    }

    /**
     * @test
     */
    public function test_delete_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/logistic_shipping_mode_translations/'.$logisticShippingModeTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/logistic_shipping_mode_translations/'.$logisticShippingModeTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
