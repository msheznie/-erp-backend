<?php

namespace App\Repositories;

use App\Models\StockAdjustment;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class StockAdjustmentRepository
 * @package App\Repositories
 * @version August 20, 2018, 11:55 am UTC
 *
 * @method StockAdjustment findWithoutFail($id, $columns = ['*'])
 * @method StockAdjustment find($id, $columns = ['*'])
 * @method StockAdjustment first($columns = ['*'])
*/
class StockAdjustmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'stockAdjustmentCode',
        'refNo',
        'stockAdjustmentDate',
        'location',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'reason',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockAdjustment::class;
    }

    public function getAudit($id){
        return  $this->with(['created_by','confirmed_by','modified_by','warehouse_by','company','details.uom','approved_by' => function ($query) {
            $query->with(['employee' =>  function($q){
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID',7);
        },'audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function stockAdjustmentListQuery($request, $input, $search = '',$grvLocation, $serviceLineSystemID,$reasons) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $stockAdjustments = StockAdjustment::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by','reason']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockAdjustments->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockAdjustments->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockAdjustments->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $stockAdjustments->whereIn('location', $grvLocation);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockAdjustments->whereMonth('stockAdjustmentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockAdjustments->whereYear('stockAdjustmentDate', '=', $input['year']);
            }
        }

        if(array_key_exists('reason', $input)) {
            if ($input['reason'] && !is_null($input['reason'])) {
                $stockAdjustments->whereIn('reason', $reasons);
            }
        }

        // if(array_key_exists('year', $input))


        $stockAdjustments = $stockAdjustments->select(
            ['stockAdjustmentAutoID',
                'stockAdjustmentCode',
                'stockAdjustmentType',
                'comment',
                'stockAdjustmentDate',
                'confirmedYN',
                'approved',
                'serviceLineSystemID',
                'documentSystemID',
                'confirmedByEmpSystemID',
                'createdUserSystemID',
                'confirmedDate',
                'approvedDate',
                'createdDateTime',
                'refNo',
                'location',
                'refferedBackYN',
                'reason'
            ]);



        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockAdjustments = $stockAdjustments->where(function ($query) use ($search) {
                $query->where('stockAdjustmentCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return $stockAdjustments;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.doc_code')] = $val->stockAdjustmentCode;
                $data[$x][trans('custom.segment')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.reference_no')] = $val->refNo;
                $data[$x][trans('custom.date')] = \Helper::dateFormat($val->stockAdjustmentDate);
                $data[$x][trans('custom.location')] = $val->warehouse_by? $val->warehouse_by->wareHouseDescription : '';
                $data[$x][trans('custom.comment')] = $val->comment;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.confirmed_at')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.approved_at')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->CancelledYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
