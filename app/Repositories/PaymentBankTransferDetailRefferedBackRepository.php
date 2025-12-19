<?php

namespace App\Repositories;

use App\Models\PaymentBankTransferDetailRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentBankTransferDetailRefferedBackRepository
 * @package App\Repositories
 * @version December 11, 2018, 5:32 am UTC
 *
 * @method PaymentBankTransferDetailRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method PaymentBankTransferDetailRefferedBack find($id, $columns = ['*'])
 * @method PaymentBankTransferDetailRefferedBack first($columns = ['*'])
*/
class PaymentBankTransferDetailRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankLedgerAutoID',
        'bankRecAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'postedDate',
        'documentNarration',
        'bankID',
        'bankAccountID',
        'bankCurrency',
        'bankCurrencyER',
        'documentChequeNo',
        'documentChequeDate',
        'payeeID',
        'payeeCode',
        'payeeName',
        'payeeGLCodeID',
        'payeeGLCode',
        'supplierTransCurrencyID',
        'supplierTransCurrencyER',
        'localCurrencyID',
        'localCurrencyER',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'payAmountBank',
        'payAmountSuppTrans',
        'payAmountCompLocal',
        'payAmountCompRpt',
        'invoiceType',
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
        'bankRecYear',
        'bankRecMonth',
        'bankClearedDate',
        'bankClearedByEmpSystemID',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'paymentBankTransferID',
        'pulledToBankTransferYN',
        'chequePaymentYN',
        'chequePrintedYN',
        'chequePrintedDateTime',
        'chequePrintedByEmpSystemID',
        'chequePrintedByEmpID',
        'chequePrintedByEmpName',
        'chequeSentToTreasury',
        'chequeSentToTreasuryDate',
        'chequeSentToTreasuryByEmpSystemID',
        'chequeSentToTreasuryByEmpID',
        'chequeSentToTreasuryByEmpName',
        'timesReferred',
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
        return PaymentBankTransferDetailRefferedBack::class;
    }
}
