<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\BaseRepository;

/**
 * Class CompanyRepository
 * @package App\Repositories
 * @version February 16, 2018, 6:23 am UTC
 *
 * @method Company findWithoutFail($id, $columns = ['*'])
 * @method Company find($id, $columns = ['*'])
 * @method Company first($columns = ['*'])
*/
class CompanyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'CompanyID',
        'CompanyName',
        'CompanyNameLocalized',
        'LocalName',
        'MasterLevel',
        'CompanyLevel',
        'masterComapanyID',
        'masterComapanyIDReporting',
        'companyShortCode',
        'orgListOrder',
        'orgListSordOrder',
        'sortOrder',
        'listOrder',
        'CompanyAddress',
        'companyCountry',
        'CompanyTelephone',
        'CompanyFax',
        'CompanyEmail',
        'CompanyURL',
        'SubscriptionStarted',
        'SubscriptionUpTo',
        'ContactPerson',
        'ContactPersonTelephone',
        'ContactPersonFax',
        'ContactPersonEmail',
        'registrationNumber',
        'companyLogo',
        'reportingCurrency',
        'localCurrencyID',
        'mainFormName',
        'menuInitialImage',
        'menuInitialSelectedImage',
        'policyItemIssueTollerence',
        'policyAddonPercentage',
        'policyPOAppDayDiff',
        'policyStockAdjWacCurrentYN',
        'policyDepreciationRunDate',
        'isGroup',
        'isAttachementYN',
        'reportingCriteria',
        'reportingCriteriaFormQuery',
        'supplierReportingCriteria',
        'supplierReportingCriteriaFormQuery',
        'supplierPOSavReportingCriteria',
        'supplierPOSavReportingCriteriaFormQuery',
        'supplierPOSpentReportingCriteriaFormQuery',
        'exchangeGainLossGLCode',
        'exchangeLossGLCode',
        'exchangeGainGLCode',
        'exchangeProvisionGLCode',
        'exchangeProvisionGLCodeAR',
        'isApprovalByServiceLine',
        'isApprovalByServiceLineFinance',
        'isTaxYN',
        'isActive',
        'isActiveGroup',
        'showInCombo',
        'allowBackDatedGRV',
        'allowCustomerInvWithoutContractID',
        'checkMaxQty',
        'itemCodeMustInPR',
        'op_OnOpenPopUpYN',
        'showInNewRILRQHSE',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Company::class;
    }
}
