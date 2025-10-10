<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LogisticStatusTranslations;

class LogisticStatusTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/logistic_status_translations', $logisticStatusTranslations
        );

        $this->assertApiResponse($logisticStatusTranslations);
    }

    /**
     * @test
     */
    public function test_read_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/logistic_status_translations/'.$logisticStatusTranslations->id
        );

        $this->assertApiResponse($logisticStatusTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->create();
        $editedLogisticStatusTranslations = factory(LogisticStatusTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/logistic_status_translations/'.$logisticStatusTranslations->id,
            $editedLogisticStatusTranslations
        );

        $this->assertApiResponse($editedLogisticStatusTranslations);
    }

    /**
     * @test
     */
    public function test_delete_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/logistic_status_translations/'.$logisticStatusTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/logistic_status_translations/'.$logisticStatusTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
