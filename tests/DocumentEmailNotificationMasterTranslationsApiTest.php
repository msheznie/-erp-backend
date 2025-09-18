<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentEmailNotificationMasterTranslations;

class DocumentEmailNotificationMasterTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_email_notification_master_translations', $documentEmailNotificationMasterTranslations
        );

        $this->assertApiResponse($documentEmailNotificationMasterTranslations);
    }

    /**
     * @test
     */
    public function test_read_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_email_notification_master_translations/'.$documentEmailNotificationMasterTranslations->id
        );

        $this->assertApiResponse($documentEmailNotificationMasterTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->create();
        $editedDocumentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_email_notification_master_translations/'.$documentEmailNotificationMasterTranslations->id,
            $editedDocumentEmailNotificationMasterTranslations
        );

        $this->assertApiResponse($editedDocumentEmailNotificationMasterTranslations);
    }

    /**
     * @test
     */
    public function test_delete_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_email_notification_master_translations/'.$documentEmailNotificationMasterTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_email_notification_master_translations/'.$documentEmailNotificationMasterTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
