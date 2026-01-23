<?php

namespace App\Repositories;

use App\Models\BankLedger;
use App\Repositories\BaseRepository;

/**
 * Class BankLedgerRepository
 * @package App\Repositories
 * @version September 18, 2018, 4:08 am UTC
 *
 * @method BankLedger findWithoutFail($id, $columns = ['*'])
 * @method BankLedger find($id, $columns = ['*'])
 * @method BankLedger first($columns = ['*'])
*/
class BankLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
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
        'bankClearedDate',
        'bankClearedByEmpSystemID',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
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
        return BankLedger::class;
    }
}
