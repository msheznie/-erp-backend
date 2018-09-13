<?php

namespace App\Repositories;

use App\Models\MatchDocumentMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MatchDocumentMasterRepository
 * @package App\Repositories
 * @version September 11, 2018, 10:20 am UTC
 *
 * @method MatchDocumentMaster findWithoutFail($id, $columns = ['*'])
 * @method MatchDocumentMaster find($id, $columns = ['*'])
 * @method MatchDocumentMaster first($columns = ['*'])
*/
class MatchDocumentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'PayMasterAutoId',
        'documentSystemID',
        'companyID',
        'companySystemID',
        'documentID',
        'serialNo',
        'matchingDocCode',
        'matchingDocdate',
        'BPVcode',
        'BPVdate',
        'BPVNarration',
        'directPaymentPayee',
        'directPayeeCurrency',
        'BPVsupplierID',
        'supplierGLCode',
        'supplierTransCurrencyID',
        'supplierTransCurrencyER',
        'supplierDefCurrencyID',
        'supplierDefCurrencyER',
        'localCurrencyID',
        'localCurrencyER',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'payAmountBank',
        'payAmountSuppTrans',
        'payAmountSuppDef',
        'suppAmountDocTotal',
        'payAmountCompLocal',
        'payAmountCompRpt',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByEmpSystemID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'invoiceType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'matchingAmount',
        'matchBalanceAmount',
        'matchedAmount',
        'matchLocalAmount',
        'matchRptAmount',
        'matchingType',
        'isExchangematch',
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
        return MatchDocumentMaster::class;
    }
}
