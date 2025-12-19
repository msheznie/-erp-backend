<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\DocumentManagement;
use App\Repositories\DocumentManagementRepository;

trait MakeDocumentManagementTrait
{
    /**
     * Create fake instance of DocumentManagement and save it in database
     *
     * @param array $documentManagementFields
     * @return DocumentManagement
     */
    public function makeDocumentManagement($documentManagementFields = [])
    {
        /** @var DocumentManagementRepository $documentManagementRepo */
        $documentManagementRepo = \App::make(DocumentManagementRepository::class);
        $theme = $this->fakeDocumentManagementData($documentManagementFields);
        return $documentManagementRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentManagement
     *
     * @param array $documentManagementFields
     * @return DocumentManagement
     */
    public function fakeDocumentManagement($documentManagementFields = [])
    {
        return new DocumentManagement($this->fakeDocumentManagementData($documentManagementFields));
    }

    /**
     * Get fake data of DocumentManagement
     *
     * @param array $documentManagementFields
     * @return array
     */
    public function fakeDocumentManagementData($documentManagementFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'bigginingSerialNumber' => $fake->randomDigitNotNull,
            'year' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'financeYearBigginingDate' => $fake->date('Y-m-d H:i:s'),
            'financeYearEndDate' => $fake->date('Y-m-d H:i:s'),
            'numberOfSerialNoDigits' => $fake->randomDigitNotNull,
            'docRefNo' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $documentManagementFields);
    }
}
