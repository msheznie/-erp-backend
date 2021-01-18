<?php

namespace App\Repositories;

use App\Models\PurchaseReturn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseReturnRepository
 * @package App\Repositories
 * @version July 31, 2018, 6:08 am UTC
 *
 * @method PurchaseReturn findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturn find($id, $columns = ['*'])
 * @method PurchaseReturn first($columns = ['*'])
*/
class PurchaseReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'serviceLineCode',
        'documentSystemID',
        'companyID',
        'serviceLineSystemID',
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
        'RollLevForApp_curr',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturn::class;
    }

    public function getAudit($id){
        return  $this->with(['created_by','confirmed_by','modified_by','location_by','company_by','details.unit','approved_by' => function ($query) {
            $query->with(['employee' =>  function($q){
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID',24);
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }
}
