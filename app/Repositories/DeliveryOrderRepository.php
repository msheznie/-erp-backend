<?php

namespace App\Repositories;

use App\Models\DeliveryOrder;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\Helper;
use App\helper\StatusService;

/**
 * Class DeliveryOrderRepository
 * @package App\Repositories
 * @version May 8, 2020, 2:34 pm +04
 *
 * @method DeliveryOrder findWithoutFail($id, $columns = ['*'])
 * @method DeliveryOrder find($id, $columns = ['*'])
 * @method DeliveryOrder first($columns = ['*'])
*/
class DeliveryOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'orderType',
        'deliveryOrderCode',
        'companySystemId',
        'documentSystemId',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'deliveryOrderDate',
        'wareHouseSystemCode',
        'serviceLineSystemID',
        'referenceNo',
        'customerID',
        'salesPersonID',
        'narration',
        'notes',
        'contactPersonNumber',
        'contactPersonName',
        'transactionCurrencyID',
        'transactionCurrencyER',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrencyER',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrencyER',
        'companyReportingAmount',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedEmpSystemID',
        'approvedbyEmpID',
        'approvedbyEmpName',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'closedYN',
        'closedDate',
        'closedReason',
        'createdUserSystemID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedUserSystemID',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryOrder::class;
    }

    public function deliveryOrderListQuery($request, $input, $search = '') {

        $companyId = $request['companyId'];

        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }
        $customerID= $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');
        $deliveryOrder = DeliveryOrder::whereIn('companySystemID', $childCompanies)
        ->with(['customer','transaction_currency','local_currency','reporting_currency','created_by','segment']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $deliveryOrder->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $deliveryOrder->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month']) && $input['month'] != [0]) {
                $deliveryOrder->whereMonth('deliveryOrderDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year']) && $input['year'] != [0]) {
                $deliveryOrder->whereYear('deliveryOrderDate', '=', $input['year']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if ($input['customerID'] && !is_null($input['customerID'])) {
                $deliveryOrder->whereIn('customerID', $customerID);
            }
        }

        if (array_key_exists('orderType', $input)) {
            if ($input['orderType'] && !is_null($input['orderType']) && $input['orderType'] != [0]) {
                $deliveryOrder->where('orderType', $input['orderType']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $deliveryOrder = $deliveryOrder->where(function ($query) use ($search) {
                $query->where('deliveryOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                ->orWhereHas('customer', function ($q) use ($search){
                    $q->where('CustomerName', 'LIKE', "%{$search}%");
                });
            });
        }

        return $deliveryOrder;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Document Code'] = $val->deliveryOrderCode;
                $data[$x]['Type'] = StatusService::getDeliveryOrderType($val->orderType);
                $data[$x]['Customer Name'] = $val->customer? $val->customer->CustomerName : '';
                $data[$x]['Document Date'] = \Helper::dateFormat($val->deliveryOrderDate);
                $data[$x]['Segment'] = $val->segment? $val->segment->ServiceLineDes : '';
                $data[$x]['Comments'] = $val->narration;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::dateFormat($val->createdDateTime);
                $data[$x]['Confirmed on'] = \Helper::dateFormat($val->confirmedDate);
                $data[$x]['Approved on'] = \Helper::dateFormat($val->approvedDate);
                $data[$x]['Transaction Currency'] = $val->transaction_currency? $val->transaction_currency->CurrencyCode : '';
                $data[$x]['Transaction Amount'] = $val->transactionAmount? number_format($val->transactionAmount + $val->VATAmount, $val->transaction_currency? $val->transaction_currency->DecimalPlaces : '', ".", "") : 0;

                $data[$x]['Local Currency'] = $val->local_currency? $val->local_currency->CurrencyCode : '';
                $data[$x]['Local Amount'] = $val->companyLocalAmount? number_format($val->companyLocalAmount + $val->VATAmountLocal, $val->local_currency? $val->local_currency->DecimalPlaces : '', ".", "") : 0;
                $data[$x]['Reporting Currency'] = $val->reporting_currency? $val->reporting_currency->CurrencyCode : '';
                $data[$x]['Reporting Amount'] = $val->companyReportingAmount? number_format($val->companyReportingAmount + $val->VATAmountRpt, $val->reporting_currency? $val->reporting_currency->DecimalPlaces : '', ".", "") : 0;

                $data[$x]['Status'] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
