<?php

use Faker\Factory as Faker;
use App\Models\DocumentEmailNotificationDetail;
use App\Repositories\DocumentEmailNotificationDetailRepository;

trait MakeDocumentEmailNotificationDetailTrait
{
    /**
     * Create fake instance of DocumentEmailNotificationDetail and save it in database
     *
     * @param array $documentEmailNotificationDetailFields
     * @return DocumentEmailNotificationDetail
     */
    public function makeDocumentEmailNotificationDetail($documentEmailNotificationDetailFields = [])
    {
        /** @var DocumentEmailNotificationDetailRepository $documentEmailNotificationDetailRepo */
        $documentEmailNotificationDetailRepo = App::make(DocumentEmailNotificationDetailRepository::class);
        $theme = $this->fakeDocumentEmailNotificationDetailData($documentEmailNotificationDetailFields);
        return $documentEmailNotificationDetailRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentEmailNotificationDetail
     *
     * @param array $documentEmailNotificationDetailFields
     * @return DocumentEmailNotificationDetail
     */
    public function fakeDocumentEmailNotificationDetail($documentEmailNotificationDetailFields = [])
    {
        return new DocumentEmailNotificationDetail($this->fakeDocumentEmailNotificationDetailData($documentEmailNotificationDetailFields));
    }

    /**
     * Get fake data of DocumentEmailNotificationDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentEmailNotificationDetailData($documentEmailNotificationDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'employeeSystemID' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'sendYN' => $fake->randomDigitNotNull,
            'emailNotificationID' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $documentEmailNotificationDetailFields);
    }
}
