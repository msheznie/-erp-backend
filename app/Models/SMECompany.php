<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMECompany",
 *      required={""},
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_link_id",
 *          description="if it is a school then school master id ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="branch_link_id",
 *          description="if it is school then branch id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="productID",
 *          description="1 - Spur  2- Gears Standard",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_code",
 *          description="company_code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_name",
 *          description="company_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_start_date",
 *          description="company_start_date",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="company_url",
 *          description="company_url",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_logo",
 *          description="company_logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_secondary_logo",
 *          description="company_secondary_logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_default_currencyID",
 *          description="company_default_currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_default_currency",
 *          description="company_default_currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_default_decimal",
 *          description="company_default_decimal",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="company_reporting_currencyID",
 *          description="company_reporting_currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_reporting_currency",
 *          description="company_reporting_currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_reporting_decimal",
 *          description="company_reporting_decimal",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="company_email",
 *          description="company_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_phone",
 *          description="company_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyPrintName",
 *          description="companyPrintName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyPrintAddress",
 *          description="companyPrintAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyPrintTelephone",
 *          description="companyPrintTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyPrintOther",
 *          description="companyPrintOther",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyPrintTagline",
 *          description="companyPrintTagline",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="registration_no",
 *          description="registration_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_address1",
 *          description="company_address1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_address2",
 *          description="company_address2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_city",
 *          description="company_city",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_province",
 *          description="company_province",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_postalcode",
 *          description="company_postalcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="stateID",
 *          description="stateID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_country",
 *          description="company_country",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="legalName",
 *          description="legalName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isVatEligible",
 *          description="isVatEligible",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatIdNo",
 *          description="vatIdNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="textIdentificationNo",
 *          description="textIdentificationNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxCardNo",
 *          description="taxCardNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="textYear",
 *          description="textYear",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="industryID",
 *          description="industryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="industry",
 *          description="industry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mfqIndustryID",
 *          description="mfqIndustryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="default_segment",
 *          description="default_segment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="default_segment_id",
 *          description="default_segment_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supportURL",
 *          description="supportURL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="noOfUsers",
 *          description="noOfUsers",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYear",
 *          description="companyFinanceYear",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="FYBegin",
 *          description="FYBegin",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="FYEnd",
 *          description="FYEnd",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FYPeriodDateFrom",
 *          description="FYPeriodDateFrom",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="FYPeriodDateTo",
 *          description="FYPeriodDateTo",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="pos_isFinanceEnables",
 *          description="pos_isFinanceEnables",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isBuyBackEnabled",
 *          description="isBuyBackEnabled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyType",
 *          description="1- FIN 2 - PVT",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pvtCompanyID",
 *          description="pvtCompanyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="defaultTimezoneID",
 *          description="defaultTimezoneID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localposaccesstoken",
 *          description="Local POS system pull request validation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SMECompany extends Model
{

    public $table = 'srp_erp_company';

    protected $primaryKey = 'company_id';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'company_link_id' => 'integer',
        'branch_link_id' => 'integer',
        'productID' => 'integer',
        'company_code' => 'string',
        'company_name' => 'string',
        'company_start_date' => 'date',
        'company_url' => 'string',
        'company_logo' => 'string',
        'company_secondary_logo' => 'string',
        'company_default_currencyID' => 'integer',
        'company_default_currency' => 'string',
        'company_default_decimal' => 'integer',
        'company_reporting_currencyID' => 'integer',
        'company_reporting_currency' => 'string',
        'company_reporting_decimal' => 'boolean',
        'company_email' => 'string',
        'company_phone' => 'string',
        'companyPrintName' => 'string',
        'companyPrintAddress' => 'string',
        'companyPrintTelephone' => 'string',
        'companyPrintOther' => 'string',
        'companyPrintTagline' => 'string',
        'registration_no' => 'string',
        'company_address1' => 'string',
        'company_address2' => 'string',
        'company_city' => 'string',
        'company_province' => 'string',
        'company_postalcode' => 'string',
        'countryID' => 'integer',
        'stateID' => 'integer',
        'company_country' => 'string',
        'legalName' => 'string',
        'isVatEligible' => 'integer',
        'vatIdNo' => 'string',
        'textIdentificationNo' => 'string',
        'taxCardNo' => 'string',
        'textYear' => 'string',
        'industryID' => 'integer',
        'industry' => 'string',
        'mfqIndustryID' => 'integer',
        'default_segment' => 'string',
        'default_segment_id' => 'integer',
        'supportURL' => 'string',
        'noOfUsers' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinanceYear' => 'string',
        'FYBegin' => 'date',
        'FYEnd' => 'date',
        'companyFinancePeriodID' => 'integer',
        'FYPeriodDateFrom' => 'date',
        'FYPeriodDateTo' => 'date',
        'pos_isFinanceEnables' => 'integer',
        'isBuyBackEnabled' => 'integer',
        'companyType' => 'integer',
        'pvtCompanyID' => 'integer',
        'defaultTimezoneID' => 'integer',
        'confirmedYN' => 'integer',
        'localposaccesstoken' => 'string',
        'createdUserGroup' => 'string',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'company_code' => 'required',
        'company_name' => 'required',
        'company_start_date' => 'required'
    ];

    
}
