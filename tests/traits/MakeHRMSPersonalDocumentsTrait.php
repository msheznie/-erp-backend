<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HRMSPersonalDocuments;
use App\Repositories\HRMSPersonalDocumentsRepository;

trait MakeHRMSPersonalDocumentsTrait
{
    /**
     * Create fake instance of HRMSPersonalDocuments and save it in database
     *
     * @param array $hRMSPersonalDocumentsFields
     * @return HRMSPersonalDocuments
     */
    public function makeHRMSPersonalDocuments($hRMSPersonalDocumentsFields = [])
    {
        /** @var HRMSPersonalDocumentsRepository $hRMSPersonalDocumentsRepo */
        $hRMSPersonalDocumentsRepo = \App::make(HRMSPersonalDocumentsRepository::class);
        $theme = $this->fakeHRMSPersonalDocumentsData($hRMSPersonalDocumentsFields);
        return $hRMSPersonalDocumentsRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSPersonalDocuments
     *
     * @param array $hRMSPersonalDocumentsFields
     * @return HRMSPersonalDocuments
     */
    public function fakeHRMSPersonalDocuments($hRMSPersonalDocumentsFields = [])
    {
        return new HRMSPersonalDocuments($this->fakeHRMSPersonalDocumentsData($hRMSPersonalDocumentsFields));
    }

    /**
     * Get fake data of HRMSPersonalDocuments
     *
     * @param array $hRMSPersonalDocumentsFields
     * @return array
     */
    public function fakeHRMSPersonalDocumentsData($hRMSPersonalDocumentsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentType' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'employeeSystemID' => $fake->randomDigitNotNull,
            'documentNo' => $fake->word,
            'docIssuedby' => $fake->word,
            'issueDate' => $fake->date('Y-m-d H:i:s'),
            'expireDate' => $fake->date('Y-m-d H:i:s'),
            'expireDate_O' => $fake->date('Y-m-d H:i:s'),
            'categoryID' => $fake->randomDigitNotNull,
            'attachmentFileName' => $fake->text,
            'isActive' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdpc' => $fake->word,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSPersonalDocumentsFields);
    }
}
