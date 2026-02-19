<?php

namespace App\Repositories;

use App\Models\PurchaseReturnMasterRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseReturnMasterRefferedBackRepository
 * @package App\Repositories
 * @version January 25, 2021, 12:46 pm +04
 *
 * @method PurchaseReturnMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturnMasterRefferedBack find($id, $columns = ['*'])
 * @method PurchaseReturnMasterRefferedBack first($columns = ['*'])
*/
class PurchaseReturnMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purhaseReturnAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'purchaseReturnDate',
        'purchaseReturnCode',
        'purchaseReturnRefNo',
        'narration',
        'purchaseReturnLocation',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'totalSupplierDefaultAmount',
        'totalSupplierTransactionAmount',
        'totalLocalAmount',
        'totalComRptAmount',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'isInvoiceCreatedForGrv',
        'grvRecieved',
        'prClosedYN'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturnMasterRefferedBack::class;
    }
}
