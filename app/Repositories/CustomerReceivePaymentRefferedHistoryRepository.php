<?php

namespace App\Repositories;

use App\Models\CustomerReceivePaymentRefferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class CustomerReceivePaymentRefferedHistoryRepository
 * @package App\Repositories
 * @version November 21, 2018, 10:35 am UTC
 *
 * @method CustomerReceivePaymentRefferedHistory findWithoutFail($id, $columns = ['*'])
 * @method CustomerReceivePaymentRefferedHistory find($id, $columns = ['*'])
 * @method CustomerReceivePaymentRefferedHistory first($columns = ['*'])
*/
class CustomerReceivePaymentRefferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custReceivePaymentAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYPeriodDateFrom',
        'companyFinancePeriodID',
        'FYEnd',
        'FYPeriodDateTo',
        'PayMasterAutoId',
        'intercompanyPaymentID',
        'intercompanyPaymentCode',
        'custPaymentReceiveCode',
        'custPaymentReceiveDate',
        'narration',
        'customerID',
        'customerGLCodeSystemID',
        'customerGLCode',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'bankID',
        'bankAccount',
        'bankCurrency',
        'bankCurrencyER',
        'payeeYN',
        'PayeeSelectEmp',
        'PayeeEmpID',
        'PayeeName',
        'PayeeCurrency',
        'custChequeNo',
        'custChequeDate',
        'custChequeBank',
        'receivedAmount',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'companyRptAmount',
        'bankAmount',
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
        'trsCollectedYN',
        'trsCollectedByEmpSystemID',
        'trsCollectedByEmpID',
        'trsCollectedByEmpName',
        'trsCollectedDate',
        'trsClearedYN',
        'trsClearedDate',
        'trsClearedByEmpSystemID',
        'trsClearedByEmpID',
        'trsClearedByEmpName',
        'trsClearedAmount',
        'bankClearedYN',
        'bankClearedAmount',
        'bankReconciliationDate',
        'bankClearedDate',
        'bankClearedByEmpSystemID',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'documentType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'expenseClaimOrPettyCash',
        'refferedBackYN',
        'timesReferred',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerReceivePaymentRefferedHistory::class;
    }
}
