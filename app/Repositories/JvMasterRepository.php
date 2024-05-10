<?php

namespace App\Repositories;

use App\Models\JvMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class JvMasterRepository
 * @package App\Repositories
 * @version September 25, 2018, 7:43 am UTC
 *
 * @method JvMaster findWithoutFail($id, $columns = ['*'])
 * @method JvMaster find($id, $columns = ['*'])
 * @method JvMaster first($columns = ['*'])
*/
class JvMasterRepository extends BaseRepository
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
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'JVcode',
        'JVdate',
        'recurringjvMasterAutoId',
        'recurringMonth',
        'recurringYear',
        'JVNarration',
        'currencyID',
        'currencyER',
        'rptCurrencyID',
        'rptCurrencyER',
        'empID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'jvType',
        'isReverseAccYN',
        'timesReferred',
        'isRelatedPartyYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'reversalDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return JvMaster::class;
    }

    public function jvMasterListQuery($request, $input, $search = '') {

        $invMaster = JvMaster::where('companySystemID', $input['companySystemID']);
        //$invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with(['created_by', 'transactioncurrency', 'detail' => function ($query) {
            $query->selectRaw('COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,jvMasterAutoId');
            $query->groupBy('jvMasterAutoId');
        }]);

        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $createdBy = collect($input['createdBy'])->pluck('id')->toArray();
                $invMaster->whereIn('createdUserSystemID', $createdBy);
            }

        }

        if (array_key_exists('jvType', $input)) {
            if (($input['jvType'] == 0 || $input['jvType'] == 1 || $input['jvType'] == 2 || $input['jvType'] == 3 || $input['jvType'] == 4 || $input['jvType'] == 5) && !is_null($input['jvType'])) {
                $invMaster->where('jvType', $input['jvType']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('JVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('JVdate', '=', $input['year']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('JVcode', 'LIKE', "%{$search}%")
                    ->orWhere('JVNarration', 'LIKE', "%{$search}%");
            });
        }

        return $invMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['JV Code'] = $val->JVcode;
                $data[$x]['Type'] = StatusService::getjvType($val->jvType);
                $data[$x]['JV Date'] = \Helper::dateFormat($val->JVdate);
                $data[$x]['Narration'] = $val->JVNarration;
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
