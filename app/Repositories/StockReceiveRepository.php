<?php

namespace App\Repositories;

use App\Models\StockReceive;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class StockReceiveRepository
 * @package App\Repositories
 * @version July 23, 2018, 4:46 am UTC
 *
 * @method StockReceive findWithoutFail($id, $columns = ['*'])
 * @method StockReceive find($id, $columns = ['*'])
 * @method StockReceive first($columns = ['*'])
 */
class StockReceiveRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'serialNo',
        'stockReceiveCode',
        'refNo',
        'receivedDate',
        'comment',
        'companyFromSystemID',
        'companyFrom',
        'companyToSystemID',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockReceive::class;
    }


    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by','company','location_to_by', 'location_from_by', 'details' => function ($q) {
            $q->with(['unit_by','item_by']);
        }, 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 10);
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }

    public function stockReceiveListQuery($request, $input, $search = '',$serviceLineSystemID) {

        $stockReceive = StockReceive::where('companySystemID', $input['companyId'])
        ->where('documentSystemID', $input['documentId'])
        ->with(['created_by', 'segment_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockReceive->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('locationFrom', $input)) {
            if ($input['locationFrom'] && !is_null($input['locationFrom'])) {
                $stockReceive->where('locationFrom', $input['locationFrom']);
            }
        }

        if (array_key_exists('locationTo', $input)) {
            if ($input['locationTo'] && !is_null($input['locationTo'])) {
                $stockReceive->where('locationTo', $input['locationTo']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockReceive->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockReceive->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('interCompanyTransferYN', $input)) {
            if (($input['interCompanyTransferYN'] == 0 || $input['interCompanyTransferYN'] == -1) && !is_null($input['interCompanyTransferYN'])) {
                $stockReceive->where('interCompanyTransferYN', $input['interCompanyTransferYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockReceive->whereMonth('receivedDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockReceive->whereYear('receivedDate', '=', $input['year']);
            }
        }

        $stockReceive = $stockReceive->select(
            ['stockReceiveAutoID',
                'stockReceiveCode',
                'documentSystemID',
                'refNo',
                'createdDateTime',
                'createdUserSystemID',
                'comment',
                'receivedDate',
                'serviceLineSystemID',
                'confirmedDate',
                'approvedDate',
                'timesReferred',
                'confirmedYN',
                'approved',
                'approvedDate',
                'refferedBackYN',
                'postedDate'
            ]);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockReceive = $stockReceive->where(function ($query) use ($search) {
                $query->where('stockReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('refNo', 'LIKE', "%{$search}%");
            });
        }

        return $stockReceive;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.e_stock_receive_code')] = $val->stockReceiveCode;
                $data[$x][trans('custom.e_segment')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.e_reference_no')] = $val->refNo;
                $data[$x][trans('custom.e_received_date')] = \Helper::dateFormat($val->receivedDate);
                $data[$x][trans('custom.e_comment')] = $val->comment;
                $data[$x][trans('custom.e_created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.e_created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.e_confirmed_at')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.e_approved_at')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.e_status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
