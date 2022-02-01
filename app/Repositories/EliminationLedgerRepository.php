<?php

namespace App\Repositories;

use App\Models\EliminationLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EliminationLedgerRepository
 * @package App\Repositories
 * @version February 1, 2022, 3:03 pm +04
 *
 * @method EliminationLedger findWithoutFail($id, $columns = ['*'])
 * @method EliminationLedger find($id, $columns = ['*'])
 * @method EliminationLedger first($columns = ['*'])
*/
class EliminationLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'masterCompanyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'documentYear',
        'documentMonth',
        'chequeNumber',
        'invoiceNumber',
        'invoiceDate',
        'chartOfAccountSystemID',
        'glCode',
        'glAccountType',
        'glAccountTypeID',
        'holdingShareholder',
        'holdingPercentage',
        'nonHoldingPercentage',
        'documentConfirmedDate',
        'documentConfirmedByEmpSystemID',
        'documentConfirmedBy',
        'documentFinalApprovedDate',
        'documentFinalApprovedByEmpSystemID',
        'documentFinalApprovedBy',
        'documentNarration',
        'contractUID',
        'clientContractID',
        'supplierCodeSystem',
        'venderName',
        'documentTransCurrencyID',
        'documentTransCurrencyER',
        'documentTransAmount',
        'documentLocalCurrencyID',
        'documentLocalCurrencyER',
        'documentLocalAmount',
        'documentRptCurrencyID',
        'documentRptCurrencyER',
        'documentRptAmount',
        'empID',
        'employeePaymentYN',
        'isRelatedPartyYN',
        'hideForTax',
        'documentType',
        'advancePaymentTypeID',
        'matchDocumentMasterAutoID',
        'isPdcChequeYN',
        'isAddon',
        'isAllocationJV',
        'contraYN',
        'contracDocCode',
        'createdDateTime',
        'createdUserID',
        'createdUserSystemID',
        'createdUserPC',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EliminationLedger::class;
    }
}
