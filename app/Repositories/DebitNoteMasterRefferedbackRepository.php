<?php

namespace App\Repositories;

use App\Models\DebitNoteMasterRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class DebitNoteMasterRefferedbackRepository
 * @package App\Repositories
 * @version December 3, 2018, 5:01 am UTC
 *
 * @method DebitNoteMasterRefferedback findWithoutFail($id, $columns = ['*'])
 * @method DebitNoteMasterRefferedback find($id, $columns = ['*'])
 * @method DebitNoteMasterRefferedback first($columns = ['*'])
*/
class DebitNoteMasterRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'debitNoteAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'debitNoteCode',
        'debitNoteDate',
        'comments',
        'referenceNumber',
        'invoiceNumber',
        'supplierID',
        'supplierGLCodeSystemID',
        'supplierGLCode',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'debitAmountTrans',
        'debitAmountLocal',
        'debitAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'documentType',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'createdDateAndTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DebitNoteMasterRefferedback::class;
    }
}
