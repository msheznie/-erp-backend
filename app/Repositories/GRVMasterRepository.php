<?php

namespace App\Repositories;

use App\Models\BookInvSuppDet;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\UnbilledGrvGroupBy;
use App\Models\FixedAssetMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

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

    public function isGrvEligibleForCancellation($input, $type = null){

        $grv = GRVMaster::find($input['grvAutoID']);
        $document = 'cancel';

        if ($type == "reversal") {
            $document = 'reverse';
        }

        if (empty($grv)) {
            return $array = [
                'status' => 0,
                'msg' => 'GRV not found',
            ];
        }

        if ($grv->approved != -1) {
            return $array = [
                'status' => 0,
                'msg' => 'You cannot '.$document.', This document not approved.',
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
                'msg' => 'You cannot '.$document.' the GRV. The GRV is already added to Asset Allocation',
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
                    'msg' => 'You cannot '.$document.' inventory type GRV. Please do a purchase return for this GRV',
                ];
                break;
            case 2: // Service - Don't allow to cancel the GRV, If the GRV is added to BSI
            case 3: // Don’t allow to cancel, If the GRV is added in the asset allocation or  If the GRV is added to BSI
            case 4: // Don't allow to cancel the GRV, If the GRV is added to BSI
               // $isExistBSI = UnbilledGrvGroupBy::where('grvAutoID',$input['grvAutoID'])->where('selectedForBooking',-1)->exists();
                $isExistBSI = BookInvSuppDet::where('grvAutoID',$input['grvAutoID'])->exists();
                if($isExistBSI){
                    return $array = [
                        'status' => 0,
                        'msg' => 'You cannot '.$document.' the GRV. The GRV is already added to Supplier Invoice ',
                    ];
                }

                if($oneDetail->itemFinanceCategoryID == 3){
                    $isAssetAllocationExist = GRVDetails::where('grvAutoID',$input['grvAutoID'])->where('assetAllocationDoneYN', -1)->exists();
                    if($isAssetAllocationExist){
                        return $array = [
                            'status' => 0,
                            'msg' => 'You cannot '.$document.' the GRV. The GRV is already added to Asset Allocation',
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

    public function grvListQuery($request, $input, $search = '', $grvLocation, $serviceLineSystemID, $projectID) {

        $grvMaster = GRVMaster::where('companySystemID', $input['companyId']);
        $grvMaster->where('documentSystemID', $input['documentId']);
        $grvMaster->with(['created_by' => function ($query) {
        }, 'segment_by' => function ($query) {
        }, 'location_by' => function ($query) {
        }, 'supplier_by' => function ($query) {
        }, 'currency_by' => function ($query) {
        }, 'grvtype_by' => function ($query) {
        }, 'project' => function ($query) {
        }]);


        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $createdBy = collect($input['createdBy'])->pluck('id')->toArray();
                $grvMaster->whereIn('createdUserSystemID', $createdBy);
            }

        }
        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $grvMaster->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('projectID', $input)) {
            if ($input['projectID'] && !is_null($input['projectID'])) {
                $grvMaster->whereIn('projectID', $projectID);
            }
        }

        if (array_key_exists('grvLocation', $input)) {
            if ($input['grvLocation'] && !is_null($input['grvLocation'])) {
                $grvMaster->whereIn('grvLocation', $grvLocation);
            }
        }

        if (array_key_exists('grvCancelledYN', $input)) {
            if (($input['grvCancelledYN'] == 0 || $input['grvCancelledYN'] == -1) && !is_null($input['grvCancelledYN'])) {
                $grvMaster->where('grvCancelledYN', $input['grvCancelledYN']);
            }
        }

        if (array_key_exists('grvConfirmedYN', $input)) {
            if (($input['grvConfirmedYN'] == 0 || $input['grvConfirmedYN'] == 1) && !is_null($input['grvConfirmedYN'])) {
                $grvMaster->where('grvConfirmedYN', $input['grvConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $grvMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $grvMaster->whereMonth('grvDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $grvMaster->whereYear('grvDate', '=', $input['year']);
            }
        }

        if (array_key_exists('grvTypeID', $input)) {
            if ($input['grvTypeID'] && !is_null($input['grvTypeID'])) {
                $grvMaster->where('grvTypeID', $input['grvTypeID']);
            }
        }

        $grvMaster = $grvMaster->select(
            [   'erp_grvmaster.grvAutoID',
                'erp_grvmaster.grvPrimaryCode',
                'erp_grvmaster.documentSystemID',
                'erp_grvmaster.grvDoRefNo',
                'erp_grvmaster.createdDateTime',
                'erp_grvmaster.createdUserSystemID',
                'erp_grvmaster.grvNarration',
                'erp_grvmaster.grvLocation',
                'erp_grvmaster.grvDate',
                'erp_grvmaster.supplierID',
                'erp_grvmaster.serviceLineSystemID',
                'erp_grvmaster.grvConfirmedDate',
                'erp_grvmaster.approvedDate',
                'erp_grvmaster.postedDate',
                'erp_grvmaster.supplierTransactionCurrencyID',
                'erp_grvmaster.grvTotalSupplierTransactionCurrency',
                'erp_grvmaster.grvCancelledYN',
                'erp_grvmaster.timesReferred',
                'erp_grvmaster.grvConfirmedYN',
                'erp_grvmaster.approved',
                'erp_grvmaster.grvLocation',
                'erp_grvmaster.refferedBackYN',
                'erp_grvmaster.grvTypeID',
                'erp_grvmaster.projectID',
                'erp_grvmaster.companySystemID',
                'erp_grvmaster.companySystemID'
            ]);
            
            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $grvMaster = $grvMaster->where(function ($query) use ($search) {
                    $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                        ->orWhere('grvNarration', 'LIKE', "%{$search}%")
                        ->orWhere('supplierName', 'LIKE', "%{$search}%");
                });
            }
            return $grvMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['GRV Code'] = $val->grvPrimaryCode;
                $data[$x]['Type'] = $val->grvtype_by? $val->grvtype_by->des : '';
                $data[$x]['Segment'] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x]['Reference No'] = $val->grvDoRefNo;
                $data[$x]['GRV Date'] = \Helper::dateFormat($val->grvDate);
                $data[$x]['Supplier Code'] = $val->supplier_by? $val->supplier_by->primarySupplierCode : '';
                $data[$x]['Supplier Name'] = $val->supplier_by? $val->supplier_by->supplierName : '';
                $data[$x]['Location'] = $val->location_by? $val->location_by->wareHouseDescription : '';
                $data[$x]['Narration'] = $val->grvNarration;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created Date'] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x]['Confirmed Date'] = \Helper::convertDateWithTime($val->grvConfirmedDate);
                $data[$x]['Approved Date'] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x]['Currency'] = $val->supplierTransactionCurrencyID? ($val->currency_by? $val->currency_by->CurrencyCode : '') : '';
                $data[$x]['Amount'] = number_format($val->grvTotalSupplierTransactionCurrency, $val->currency_by? $val->currency_by->DecimalPlaces : '', ".", "");
                $data[$x]['Status'] = StatusService::getStatus($val->grvCancelledYN, NULL, $val->grvConfirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
