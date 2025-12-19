<?php

use Faker\Factory as Faker;
use App\Models\DocumentEmailNotificationMaster;
use App\Repositories\DocumentEmailNotificationMasterRepository;

trait MakeDocumentEmailNotificationMasterTrait
{
    /**
     * Create fake instance of DocumentEmailNotificationMaster and save it in database
     *
     * @param array $documentEmailNotificationMasterFields
     * @return DocumentEmailNotificationMaster
     */
    public function makeDocumentEmailNotificationMaster($documentEmailNotificationMasterFields = [])
    {
        /** @var DocumentEmailNotificationMasterRepository $documentEmailNotificationMasterRepo */
        $documentEmailNotificationMasterRepo = App::make(DocumentEmailNotificationMasterRepository::class);
        $theme = $this->fakeDocumentEmailNotificationMasterData($documentEmailNotificationMasterFields);
        return $documentEmailNotificationMasterRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentEmailNotificationMaster
     *
     * @param array $documentEmailNotificationMasterFields
     * @return DocumentEmailNotificationMaster
     */
    public function fakeDocumentEmailNotificationMaster($documentEmailNotificationMasterFields = [])
    {
        return new DocumentEmailNotificationMaster($this->fakeDocumentEmailNotificationMasterData($documentEmailNotificationMasterFields));
    }

    /**
     * Get fake data of DocumentEmailNotificationMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentEmailNotificationMasterData($documentEmailNotificationMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->text
        ], $documentEmailNotificationMasterFields);
    }
}
