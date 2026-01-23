<?php

namespace App\Repositories;

use App\Models\QuotationMasterRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class QuotationMasterRefferedbackRepository
 * @package App\Repositories
 * @version February 3, 2019, 11:07 am +04
 *
 * @method QuotationMasterRefferedback findWithoutFail($id, $columns = ['*'])
 * @method QuotationMasterRefferedback find($id, $columns = ['*'])
 * @method QuotationMasterRefferedback first($columns = ['*'])
*/
class QuotationMasterRefferedbackRepository extends BaseRepository
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
        return QuotationMasterRefferedback::class;
    }
}
