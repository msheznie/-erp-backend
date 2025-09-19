<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LogisticModeOfImportTranslations;

class LogisticModeOfImportTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/logistic_mode_of_import_translations', $logisticModeOfImportTranslations
        );

        $this->assertApiResponse($logisticModeOfImportTranslations);
    }

    /**
     * @test
     */
    public function test_read_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/logistic_mode_of_import_translations/'.$logisticModeOfImportTranslations->id
        );

        $this->assertApiResponse($logisticModeOfImportTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->create();
        $editedLogisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/logistic_mode_of_import_translations/'.$logisticModeOfImportTranslations->id,
            $editedLogisticModeOfImportTranslations
        );

        $this->assertApiResponse($editedLogisticModeOfImportTranslations);
    }

    /**
     * @test
     */
    public function test_delete_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/logistic_mode_of_import_translations/'.$logisticModeOfImportTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/logistic_mode_of_import_translations/'.$logisticModeOfImportTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
