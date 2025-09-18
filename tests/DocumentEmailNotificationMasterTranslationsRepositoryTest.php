<?php namespace Tests\Repositories;

use App\Models\DocumentEmailNotificationMasterTranslations;
use App\Repositories\DocumentEmailNotificationMasterTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DocumentEmailNotificationMasterTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentEmailNotificationMasterTranslationsRepository
     */
    protected $documentEmailNotificationMasterTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->documentEmailNotificationMasterTranslationsRepo = \App::make(DocumentEmailNotificationMasterTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->make()->toArray();

        $createdDocumentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepo->create($documentEmailNotificationMasterTranslations);

        $createdDocumentEmailNotificationMasterTranslations = $createdDocumentEmailNotificationMasterTranslations->toArray();
        $this->assertArrayHasKey('id', $createdDocumentEmailNotificationMasterTranslations);
        $this->assertNotNull($createdDocumentEmailNotificationMasterTranslations['id'], 'Created DocumentEmailNotificationMasterTranslations must have id specified');
        $this->assertNotNull(DocumentEmailNotificationMasterTranslations::find($createdDocumentEmailNotificationMasterTranslations['id']), 'DocumentEmailNotificationMasterTranslations with given id must be in DB');
        $this->assertModelData($documentEmailNotificationMasterTranslations, $createdDocumentEmailNotificationMasterTranslations);
    }

    /**
     * @test read
     */
    public function test_read_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->create();

        $dbDocumentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepo->find($documentEmailNotificationMasterTranslations->id);

        $dbDocumentEmailNotificationMasterTranslations = $dbDocumentEmailNotificationMasterTranslations->toArray();
        $this->assertModelData($documentEmailNotificationMasterTranslations->toArray(), $dbDocumentEmailNotificationMasterTranslations);
    }

    /**
     * @test update
     */
    public function test_update_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->create();
        $fakeDocumentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->make()->toArray();

        $updatedDocumentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepo->update($fakeDocumentEmailNotificationMasterTranslations, $documentEmailNotificationMasterTranslations->id);

        $this->assertModelData($fakeDocumentEmailNotificationMasterTranslations, $updatedDocumentEmailNotificationMasterTranslations->toArray());
        $dbDocumentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepo->find($documentEmailNotificationMasterTranslations->id);
        $this->assertModelData($fakeDocumentEmailNotificationMasterTranslations, $dbDocumentEmailNotificationMasterTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_document_email_notification_master_translations()
    {
        $documentEmailNotificationMasterTranslations = factory(DocumentEmailNotificationMasterTranslations::class)->create();

        $resp = $this->documentEmailNotificationMasterTranslationsRepo->delete($documentEmailNotificationMasterTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(DocumentEmailNotificationMasterTranslations::find($documentEmailNotificationMasterTranslations->id), 'DocumentEmailNotificationMasterTranslations should not exist in DB');
    }
}
