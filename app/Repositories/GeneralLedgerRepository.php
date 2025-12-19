<?php

namespace App\Repositories;

use App\Models\GeneralLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GeneralLedgerRepository
 * @package App\Repositories
 * @version July 2, 2018, 6:33 am UTC
 *
 * @method GeneralLedger findWithoutFail($id, $columns = ['*'])
 * @method GeneralLedger find($id, $columns = ['*'])
 * @method GeneralLedger first($columns = ['*'])
*/
class GeneralLedgerRepository extends BaseRepository
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
        'holdingShareholder',
        'holdingPercentage',
        'nonHoldingPercentage',
        'documentConfirmedDate',
        'documentConfirmedBy',
        'documentFinalApprovedDate',
        'documentFinalApprovedBy',
        'documentNarration',
        'contractUID',
        'clientContractID',
        'supplierCodeSystem',
        'employeeSystemID',
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
        'isPdcChequeYN',
        'isAddon',
        'isAllocationJV',
        'createdDateTime',
        'createdUserID',
        'createdUserPC',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GeneralLedger::class;
    }
}
