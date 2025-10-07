<?php

namespace App\Repositories;

use App\helper\StatusService;
use App\Models\RecurringVoucherSetup;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RecurringVoucherSetupRepository
 * @package App\Repositories
 * @version February 2, 2024, 2:09 pm +04
 *
 * @method RecurringVoucherSetup findWithoutFail($id, $columns = ['*'])
 * @method RecurringVoucherSetup find($id, $columns = ['*'])
 * @method RecurringVoucherSetup first($columns = ['*'])
*/
class RecurringVoucherSetupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'schedule',
        'startDate',
        'endDate',
        'noOfDayMonthYear',
        'processDate',
        'documentStatus',
        'currencyID',
        'documentType',
        'narration',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RecurringVoucherSetup::class;
    }

    public function rrvMasterListQuery($request, $input, $search = '') {

        $rrvMaster = RecurringVoucherSetup::where('companySystemID', $input['companySystemID']);
        $rrvMaster->with(['created_by', 'transactioncurrency']);

        if (array_key_exists('documentType', $input)) {
            if (($input['documentType'] == 0 || $input['documentType'] == 1 || $input['documentType'] == 2) && !is_null($input['documentType'])) {
                $rrvMaster->where('documentType', $input['documentType']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $rrvMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $rrvMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $rrvMaster->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $rrvMaster = $rrvMaster->where(function ($query) use ($search) {
                $query->where('RRVcode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return $rrvMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.rrv_code')] = $val->RRVcode;
                $data[$x][trans('custom.type')] = StatusService::getrrvType($val->documentType);
                $data[$x][trans('custom.schedule')] = StatusService::getrrvSchedule($val->schedule);
                $data[$x][trans('custom.start_date')] = \Helper::dateFormat($val->startDate);
                $data[$x][trans('custom.end_date')] = \Helper::dateFormat($val->endDate);
                $data[$x][trans('custom.no_of_day_month_year')] = $val->noOfDayMonthYear;
                $data[$x][trans('custom.process_date')] = \Helper::dateFormat($val->processDate);
                $data[$x][trans('custom.document_status')] = StatusService::getrrvStatus($val->documentStatus);
                $data[$x][trans('custom.narration')] = $val->narration;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.confirmed_on')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.approved_on')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.currency')] = $val->transactioncurrency? $val->transactioncurrency->CurrencyCode : '';
                $data[$x][trans('custom.debit_amount')] = $val->detail->count() > 0? number_format($val->detail[0]->debitSum, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "") : 0;
                $data[$x][trans('custom.credit_amount')] = $val->detail->count() > 0? number_format($val->detail[0]->creditSum, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "") : 0;
                $data[$x][trans('custom.status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
