<?php

namespace App\Repositories;

use App\Models\CustomerReceivePayment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerReceivePaymentRepository
 * @package App\Repositories
 * @version August 24, 2018, 11:58 am UTC
 *
 * @method CustomerReceivePayment findWithoutFail($id, $columns = ['*'])
 * @method CustomerReceivePayment find($id, $columns = ['*'])
 * @method CustomerReceivePayment first($columns = ['*'])
*/
class CustomerReceivePaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYPeriodDateFrom',
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
        'postedDate',
        'trsCollectedYN',
        'trsCollectedByEmpID',
        'trsCollectedByEmpName',
        'trsCollectedDate',
        'trsClearedYN',
        'trsClearedDate',
        'trsClearedByEmpID',
        'trsClearedByEmpName',
        'trsClearedAmount',
        'bankClearedYN',
        'bankClearedAmount',
        'bankReconciliationDate',
        'bankClearedDate',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'documentType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'expenseClaimOrPettyCash',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
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
        return CustomerReceivePayment::class;
    }
}
