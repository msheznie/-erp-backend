<?php

namespace App\Repositories;

use App\Models\QuotationMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class QuotationMasterRepository
 * @package App\Repositories
 * @version January 22, 2019, 1:56 pm +04
 *
 * @method QuotationMaster findWithoutFail($id, $columns = ['*'])
 * @method QuotationMaster find($id, $columns = ['*'])
 * @method QuotationMaster first($columns = ['*'])
*/
class QuotationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
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
        'RollLevForApp_curr',
        'closedYN',
        'closedDate',
        'closedReason',
        'companySystemID',
        'companyID',
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
        return QuotationMaster::class;
    }
}
