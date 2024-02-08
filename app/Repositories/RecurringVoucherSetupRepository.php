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
        $rrvMaster->with(['created_by', 'transactioncurrency', 'detail' => function ($query) {
            $query->selectRaw('COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,recurringVoucherAutoId');
            $query->groupBy('recurringVoucherAutoId');
        }]);

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
                $data[$x]['RRV Code'] = $val->RRVcode;
                $data[$x]['Type'] = StatusService::getrrvType($val->documentType);
                $data[$x]['Schedule'] = StatusService::getrrvSchedule($val->schedule);
                $data[$x]['Start Date'] = \Helper::dateFormat($val->startDate);
                $data[$x]['End Date'] = \Helper::dateFormat($val->endDate);
                $data[$x]['No Of Day/Month/Year'] = $val->noOfDayMonthYear;
                $data[$x]['Process Date'] = \Helper::dateFormat($val->processDate);
                $data[$x]['Document Status'] = StatusService::getrrvStatus($val->documentStatus);
                $data[$x]['Narration'] = $val->narration;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x]['Confirmed on'] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x]['Approved on'] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x]['Currency'] = $val->transactioncurrency? $val->transactioncurrency->CurrencyCode : '';
                $data[$x]['Debit Amount'] = $val->detail->count() > 0? number_format($val->detail[0]->debitSum, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "") : 0;
                $data[$x]['Credit Amount'] = $val->detail->count() > 0? number_format($val->detail[0]->creditSum, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "") : 0;
                $data[$x]['Status'] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
