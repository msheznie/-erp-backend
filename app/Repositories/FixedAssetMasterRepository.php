<?php

namespace App\Repositories;

use App\Models\FixedAssetMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\Models\GRVDetails;
use App\helper\StatusService;

/**
 * Class FixedAssetMasterRepository
 * @package App\Repositories
 * @version September 27, 2018, 5:37 am UTC
 *
 * @method FixedAssetMaster findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetMaster find($id, $columns = ['*'])
 * @method FixedAssetMaster first($columns = ['*'])
*/
class FixedAssetMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'docOriginSystemCode',
        'docOrigin',
        'docOriginDetailID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'faAssetDept',
        'serialNo',
        'itemCode',
        'faCode',
        'faBarcode',
        'assetCodeS',
        'faUnitSerialNo',
        'assetDescription',
        'COMMENTS',
        'groupTO',
        'dateAQ',
        'dateDEP',
        'depMonth',
        'DEPpercentage',
        'faCatID',
        'faSubCatID',
        'faSubCatID2',
        'faSubCatID3',
        'COSTUNIT',
        'costUnitRpt',
        'salvage_value',
        'salvage_value_rpt',
        'AUDITCATOGARY',
        'PARTNUMBER',
        'MANUFACTURE',
        'IMAGE',
        'UNITASSIGN',
        'UHITASSHISTORY',
        'USEDBY',
        'USEBYHISTRY',
        'LOCATION',
        'currentLocation',
        'LOCATIONHISTORY',
        'selectedForDisposal',
        'DIPOSED',
        'disposedDate',
        'assetdisposalMasterAutoID',
        'RESONDISPO',
        'CASHDISPOSAL',
        'COSTATDISP',
        'ACCDEPDIP',
        'PROFITLOSSDIS',
        'TECHNICAL_HISTORY',
        'COSTGLCODE',
        'COSTGLCODEdes',
        'ACCDEPGLCODE',
        'ACCDEPGLCODEdes',
        'DEPGLCODE',
        'DEPGLCODEdes',
        'DISPOGLCODE',
        'DISPOGLCODEdes',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedDate',
        'approved',
        'approvedDate',
        'lastVerifiedDate',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'selectedYN',
        'itemPath',
        'itemPicture',
        'assetType',
        'supplierIDRentedAsset',
        'tempRecord',
        'toolsCondition',
        'selectedforJobYN',
        'timestamp',
        'empID',
        'assetCostingUploadID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetMaster::class;
    }

    public function fixedAssetMasterListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetAllocation = GRVDetails::with(['grv_master' => function ($q) use ($search) {
                                                $q->where('grvConfirmedYN', 1);
                                                $q->where('approved', -1);
                                                $q->where('grvCancelledYN', 0);
                                                if ($search) {
                                                    $search = str_replace("\\", "\\\\", $search);
                                                    $q->where('grvPrimaryCode', 'LIKE', "%{$search}%");
                                                }
                                            }, 'item_by', 'localcurrency', 'rptcurrency'])
                                            ->whereHas('item_by', function ($q) {
                                                $q->where('financeCategoryMaster', 3);
                                            })->whereHas('grv_master', function ($q) use ($search) {
                                                $q->where('grvConfirmedYN', 1);
                                                $q->where('approved', -1);
                                                $q->where('grvCancelledYN', 0);
                                                if ($search) {
                                                    $search = str_replace("\\", "\\\\", $search);
                                                    $q->where('grvPrimaryCode', 'LIKE', "%{$search}%");
                                                }
                                            })->whereHas('localcurrency', function ($q) {
                                            })->whereHas('rptcurrency', function ($q) {
                                            })->whereIN('companySystemID', $subCompanies)->where('assetAllocationDoneYN', 0);


        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $assetAllocation->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetAllocation->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetAllocation->where('approved', $input['approved']);
            }
        }
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetAllocation = $assetAllocation->where(function ($query) use ($search) {
                $query->where('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }


        return $assetAllocation;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.doc_no')] = $val->grv_master? $val->grv_master->grvPrimaryCode : '';
                $data[$x][trans('custom.doc_description')] = $val->itemDescription;
                $data[$x][trans('custom.doc_date')] = $val->grv_master? (\Helper::dateFormat($val->grv_master->approvedDate)) : '';
                $data[$x][trans('custom.qty')] = $val->noQty;
                $data[$x][trans('custom.amount_unit_local')] = number_format($val->landingCost_LocalCur, $val->localcurrency? $val->localcurrency->DecimalPlaces : '', ".", "");
                $data[$x][trans('custom.amount_unit_reporting')] = number_format($val->landingCost_RptCur, $val->localcurrency? $val->localcurrency->DecimalPlaces : '', ".", "");

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
