<?php

namespace App\Repositories;

use App\Models\SMECompany;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMECompanyRepository
 * @package App\Repositories
 * @version March 8, 2021, 3:02 pm +04
 *
 * @method SMECompany findWithoutFail($id, $columns = ['*'])
 * @method SMECompany find($id, $columns = ['*'])
 * @method SMECompany first($columns = ['*'])
*/
class SMECompanyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_link_id',
        'branch_link_id',
        'productID',
        'company_code',
        'company_name',
        'company_start_date',
        'company_url',
        'company_logo',
        'company_secondary_logo',
        'company_default_currencyID',
        'company_default_currency',
        'company_default_decimal',
        'company_reporting_currencyID',
        'company_reporting_currency',
        'company_reporting_decimal',
        'company_email',
        'company_phone',
        'companyPrintName',
        'companyPrintAddress',
        'companyPrintTelephone',
        'companyPrintOther',
        'companyPrintTagline',
        'registration_no',
        'company_address1',
        'company_address2',
        'company_city',
        'company_province',
        'company_postalcode',
        'countryID',
        'stateID',
        'company_country',
        'legalName',
        'isVatEligible',
        'vatIdNo',
        'textIdentificationNo',
        'taxCardNo',
        'textYear',
        'industryID',
        'industry',
        'mfqIndustryID',
        'default_segment',
        'default_segment_id',
        'supportURL',
        'noOfUsers',
        'companyFinanceYearID',
        'companyFinanceYear',
        'FYBegin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'pos_isFinanceEnables',
        'isBuyBackEnabled',
        'companyType',
        'pvtCompanyID',
        'defaultTimezoneID',
        'confirmedYN',
        'localposaccesstoken',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMECompany::class;
    }
}
