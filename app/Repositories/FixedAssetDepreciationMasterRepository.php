<?php

namespace App\Repositories;

use App\Models\FixedAssetDepreciationMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class FixedAssetDepreciationMasterRepository
 * @package App\Repositories
 * @version October 12, 2018, 6:16 am UTC
 *
 * @method FixedAssetDepreciationMaster findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetDepreciationMaster find($id, $columns = ['*'])
 * @method FixedAssetDepreciationMaster first($columns = ['*'])
*/
class FixedAssetDepreciationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'depCode',
        'depDate',
        'depMonthYear',
        'depLocalCur',
        'depAmountLocal',
        'depRptCur',
        'depAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'createdUserID',
        'createdPCID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetDepreciationMaster::class;
    }

    public function fixedAssetDepreciationListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetCositng = FixedAssetDepreciationMaster::with(['depperiod_by' => function ($query) use ($input) {
            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,depMasterAutoID');
            $query->groupBy('depMasterAutoID');
        }])->ofCompany($subCompanies);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCositng->where('approved', $input['approved']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%");
            });
        }

        return $assetCositng;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.dep_code')] = $val->depCode;
                $data[$x][trans('custom.month')] = $val->depMonthYear;
                $data[$x][trans('custom.local_amount_omr')] = number_format($val->depAmountLocal, $val->depRptCur? $val->depRptCur : '', ".", "");
                $data[$x][trans('custom.reporting_amount_usd')] = number_format($val->depAmountRpt, $val->depRptCur? $val->depRptCur : '', ".", "");
                $data[$x][trans('custom.status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
