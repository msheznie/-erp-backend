<?php

use Faker\Factory as Faker;
use App\Models\ReportTemplateCashBank;
use App\Repositories\ReportTemplateCashBankRepository;

trait MakeReportTemplateCashBankTrait
{
    /**
     * Create fake instance of ReportTemplateCashBank and save it in database
     *
     * @param array $reportTemplateCashBankFields
     * @return ReportTemplateCashBank
     */
    public function makeReportTemplateCashBank($reportTemplateCashBankFields = [])
    {
        /** @var ReportTemplateCashBankRepository $reportTemplateCashBankRepo */
        $reportTemplateCashBankRepo = App::make(ReportTemplateCashBankRepository::class);
        $theme = $this->fakeReportTemplateCashBankData($reportTemplateCashBankFields);
        return $reportTemplateCashBankRepo->create($theme);
    }

    /**
     * Get fake instance of ReportTemplateCashBank
     *
     * @param array $reportTemplateCashBankFields
     * @return ReportTemplateCashBank
     */
    public function fakeReportTemplateCashBank($reportTemplateCashBankFields = [])
    {
        return new ReportTemplateCashBank($this->fakeReportTemplateCashBankData($reportTemplateCashBankFields));
    }

    /**
     * Get fake data of ReportTemplateCashBank
     *
     * @param array $postFields
     * @return array
     */
    public function fakeReportTemplateCashBankData($reportTemplateCashBankFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDescription' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $reportTemplateCashBankFields);
    }
}
