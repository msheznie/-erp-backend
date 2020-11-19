<?php

namespace App\Repositories;

use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\UnbilledGrvGroupBy;
use App\Models\FixedAssetMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GRVMasterRepository
 * @package App\Repositories
 * @version April 11, 2018, 12:12 pm UTC
 *
 * @method GRVMaster findWithoutFail($id, $columns = ['*'])
 * @method GRVMaster find($id, $columns = ['*'])
 * @method GRVMaster first($columns = ['*'])
*/
class GRVMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvType',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyAddress',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'grvDate',
        'grvSerialNo',
        'grvPrimaryCode',
        'grvDoRefNo',
        'grvNarration',
        'grvLocation',
        'grvDOpersonName',
        'grvDOpersonResID',
        'grvDOpersonTelNo',
        'grvDOpersonVehicleNo',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierAddress',
        'supplierTelephone',
        'supplierFax',
        'supplierEmail',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'grvConfirmedYN',
        'grvConfirmedByEmpID',
        'grvConfirmedByName',
        'grvConfirmedDate',
        'grvCancelledYN',
        'grvCancelledBy',
        'grvCancelledByName',
        'grvCancelledDate',
        'grvTotalComRptCurrency',
        'grvTotalLocalCurrency',
        'grvTotalSupplierDefaultCurrency',
        'grvTotalSupplierTransactionCurrency',
        'grvDiscountPercentage',
        'grvDiscountAmount',
        'approved',
        'approvedDate',
        'timesReferred',
        'RollLevForApp_curr',
        'invoiceBeforeGRVYN',
        'deliveryConfirmedYN',
        'interCompanyTransferYN',
        'FromCompanyID',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GRVMaster::class;
    }

    public function isGrvEligibleForCancellation($input){

        $grv = GRVMaster::find($input['grvAutoID']);

        if (empty($grv)) {
            return $array = [
                'status' => 0,
                'msg' => 'GRV not found',
            ];
        }

        if ($grv->approved != -1) {
            return $array = [
                'status' => 0,
                'msg' => 'You cannot cancel, This document not approved.',
            ];
        }

        if ($grv->grvCancelledYN == -1) {
            return $array = [
                'status' => 0,
                'msg' => 'GRV already cancelled',
            ];
        }

        $checkInAllocation = FixedAssetMaster::where('docOriginDocumentSystemID', 3)->where('docOriginSystemCode', $input['grvAutoID'])->first();
        if ($checkInAllocation) {
            return $array = [
                'status' => 0,
                'msg' => 'You cannot cancel the GRV. The GRV is already added to Asset Allocation',
            ];
        }

        $oneDetail = GRVDetails::where('grvAutoID',$input['grvAutoID'])->first();

        if (empty($oneDetail)) {
            return $array = [
                'status' => 0,
                'msg' => 'GRV Details not found',
            ];
        }

        switch ($oneDetail->itemFinanceCategoryID){
            case 1: //Inventory - Don't allow to cancel and ask user to create a purchase return for Inventory GRVs
                return $array = [
                    'status' => 0,
                    'msg' => 'You cannot cancel inventory type GRV. Please do a purchase return for this GRV',
                ];
                break;
            case 2: // Service - Don't allow to cancel the GRV, If the GRV is added to BSI
            case 3: // Don’t allow to cancel, If the GRV is added in the asset allocation or  If the GRV is added to BSI
            case 4: // Don't allow to cancel the GRV, If the GRV is added to BSI
                $isExistBSI = UnbilledGrvGroupBy::where('grvAutoID',$input['grvAutoID'])->where('selectedForBooking',-1)->exists();
                if($isExistBSI){
                    return $array = [
                        'status' => 0,
                        'msg' => 'You cannot cancel the GRV. The GRV is already added to Supplier Invoice ',
                    ];
                }

                if($oneDetail->itemFinanceCategoryID == 3){
                    $isAssetAllocationExist = GRVDetails::where('grvAutoID',$input['grvAutoID'])->where('assetAllocationDoneYN', -1)->exists();
                    if($isAssetAllocationExist){
                        return $array = [
                            'status' => 0,
                            'msg' => 'You cannot cancel the GRV. The GRV is already added to Asset Allocation',
                        ];
                    }
                    break;
                }

                break;
            default:
                return $array = [
                    'status' => 0,
                    'msg' => 'Item Finance Category ID Not found on Detail',
                ];
        }

        return $array = [
            'status' => 1,
            'msg' => 'success',
        ];
    }
}
