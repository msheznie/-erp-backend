<?php

namespace App\Repositories;

use App\Models\QuotationMasterVersion;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class QuotationMasterVersionRepository
 * @package App\Repositories
 * @version January 29, 2019, 12:57 pm +04
 *
 * @method QuotationMasterVersion findWithoutFail($id, $columns = ['*'])
 * @method QuotationMasterVersion find($id, $columns = ['*'])
 * @method QuotationMasterVersion first($columns = ['*'])
*/
class QuotationMasterVersionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotationMasterID',
        'documentSystemID',
        'documentID',
        'quotationCode',
        'serialNumber',
        'documentDate',
        'documentExpDate',
        'salesPersonID',
        'versionNo',
        'referenceNo',
        'narration',
        'Note',
        'contactPersonName',
        'contactPersonNumber',
        'customerSystemCode',
        'customerCode',
        'customerName',
        'customerAddress',
        'customerTelephone',
        'customerFax',
        'customerEmail',
        'customerReceivableAutoID',
        'customerReceivableSystemGLCode',
        'customerReceivableGLAccount',
        'customerReceivableDescription',
        'customerReceivableType',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalAmount',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyExchangeRate',
        'customerCurrencyAmount',
        'customerCurrencyDecimalPlaces',
        'isDeleted',
        'deletedEmpID',
        'deletedDate',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedEmpSystemID',
        'approvedbyEmpID',
        'approvedbyEmpName',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'closedYN',
        'closedDate',
        'closedReason',
        'companySystemID',
        'companyID',
        'createdUserSystemID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedUserSystemID',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'salesType'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationMasterVersion::class;
    }
}
