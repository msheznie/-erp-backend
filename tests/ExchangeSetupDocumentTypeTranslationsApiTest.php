<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExchangeSetupDocumentTypeTranslations;

class ExchangeSetupDocumentTypeTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/exchange_setup_document_type_translations', $exchangeSetupDocumentTypeTranslations
        );

        $this->assertApiResponse($exchangeSetupDocumentTypeTranslations);
    }

    /**
     * @test
     */
    public function test_read_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/exchange_setup_document_type_translations/'.$exchangeSetupDocumentTypeTranslations->id
        );

        $this->assertApiResponse($exchangeSetupDocumentTypeTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->create();
        $editedExchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/exchange_setup_document_type_translations/'.$exchangeSetupDocumentTypeTranslations->id,
            $editedExchangeSetupDocumentTypeTranslations
        );

        $this->assertApiResponse($editedExchangeSetupDocumentTypeTranslations);
    }

    /**
     * @test
     */
    public function test_delete_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/exchange_setup_document_type_translations/'.$exchangeSetupDocumentTypeTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/exchange_setup_document_type_translations/'.$exchangeSetupDocumentTypeTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
