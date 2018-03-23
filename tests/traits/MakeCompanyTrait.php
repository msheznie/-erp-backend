<?php

use Faker\Factory as Faker;
use App\Models\Company;
use App\Repositories\CompanyRepository;

trait MakeCompanyTrait
{
    /**
     * Create fake instance of Company and save it in database
     *
     * @param array $companyFields
     * @return Company
     */
    public function makeCompany($companyFields = [])
    {
        /** @var CompanyRepository $companyRepo */
        $companyRepo = App::make(CompanyRepository::class);
        $theme = $this->fakeCompanyData($companyFields);
        return $companyRepo->create($theme);
    }

    /**
     * Get fake instance of Company
     *
     * @param array $companyFields
     * @return Company
     */
    public function fakeCompany($companyFields = [])
    {
        return new Company($this->fakeCompanyData($companyFields));
    }

    /**
     * Get fake data of Company
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyData($companyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'CompanyID' => $fake->word,
            'CompanyName' => $fake->text,
            'CompanyNameLocalized' => $fake->word,
            'LocalName' => $fake->word,
            'MasterLevel' => $fake->randomDigitNotNull,
            'CompanyLevel' => $fake->randomDigitNotNull,
            'masterComapanyID' => $fake->word,
            'masterComapanyIDReporting' => $fake->word,
            'companyShortCode' => $fake->word,
            'orgListOrder' => $fake->randomDigitNotNull,
            'orgListSordOrder' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'listOrder' => $fake->randomDigitNotNull,
            'CompanyAddress' => $fake->text,
            'companyCountry' => $fake->word,
            'CompanyTelephone' => $fake->randomDigitNotNull,
            'CompanyFax' => $fake->randomDigitNotNull,
            'CompanyEmail' => $fake->word,
            'CompanyURL' => $fake->word,
            'SubscriptionStarted' => $fake->date('Y-m-d H:i:s'),
            'SubscriptionUpTo' => $fake->date('Y-m-d H:i:s'),
            'ContactPerson' => $fake->word,
            'ContactPersonTelephone' => $fake->randomDigitNotNull,
            'ContactPersonFax' => $fake->randomDigitNotNull,
            'ContactPersonEmail' => $fake->word,
            'registrationNumber' => $fake->word,
            'companyLogo' => $fake->word,
            'reportingCurrency' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'mainFormName' => $fake->word,
            'menuInitialImage' => $fake->word,
            'menuInitialSelectedImage' => $fake->word,
            'policyItemIssueTollerence' => $fake->randomDigitNotNull,
            'policyAddonPercentage' => $fake->randomDigitNotNull,
            'policyPOAppDayDiff' => $fake->randomDigitNotNull,
            'policyStockAdjWacCurrentYN' => $fake->randomDigitNotNull,
            'policyDepreciationRunDate' => $fake->randomDigitNotNull,
            'isGroup' => $fake->randomDigitNotNull,
            'isAttachementYN' => $fake->randomDigitNotNull,
            'reportingCriteria' => $fake->text,
            'reportingCriteriaFormQuery' => $fake->text,
            'supplierReportingCriteria' => $fake->text,
            'supplierReportingCriteriaFormQuery' => $fake->text,
            'supplierPOSavReportingCriteria' => $fake->text,
            'supplierPOSavReportingCriteriaFormQuery' => $fake->text,
            'supplierPOSpentReportingCriteriaFormQuery' => $fake->text,
            'exchangeGainLossGLCode' => $fake->word,
            'exchangeLossGLCode' => $fake->word,
            'exchangeGainGLCode' => $fake->word,
            'exchangeProvisionGLCode' => $fake->word,
            'exchangeProvisionGLCodeAR' => $fake->word,
            'isApprovalByServiceLine' => $fake->randomDigitNotNull,
            'isApprovalByServiceLineFinance' => $fake->randomDigitNotNull,
            'isTaxYN' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'isActiveGroup' => $fake->randomDigitNotNull,
            'showInCombo' => $fake->randomDigitNotNull,
            'allowBackDatedGRV' => $fake->randomDigitNotNull,
            'allowCustomerInvWithoutContractID' => $fake->randomDigitNotNull,
            'checkMaxQty' => $fake->randomDigitNotNull,
            'itemCodeMustInPR' => $fake->randomDigitNotNull,
            'op_OnOpenPopUpYN' => $fake->randomDigitNotNull,
            'showInNewRILRQHSE' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $companyFields);
    }
}
