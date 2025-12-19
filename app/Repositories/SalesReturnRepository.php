<?php

namespace App\Repositories;

use App\Models\SalesReturn;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\Helper;
use App\helper\StatusService;

/**
 * Class SalesReturnRepository
 * @package App\Repositories
 * @version December 21, 2020, 4:03 pm +04
 *
 * @method SalesReturn findWithoutFail($id, $columns = ['*'])
 * @method SalesReturn find($id, $columns = ['*'])
 * @method SalesReturn first($columns = ['*'])
*/
class SalesReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'returnType',
        'salesReturnCode',
        'serialNo',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'salesReturnDate',
        'wareHouseSystemCode',
        'serviceLineSystemID',
        'serviceLineCode',
        'referenceNo',
        'customerID',
        'custGLAccountSystemID',
        'custGLAccountCode',
        'custUnbilledAccountSystemID',
        'custUnbilledAccountCode',
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
        'postedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesReturn::class;
    }

    public function salesReturnListQuery($request, $input, $search = '') {

        $companyId = $request['companyId'];

        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $salesReturn = SalesReturn::whereIn('companySystemID', $childCompanies)
        ->with(['customer','transaction_currency','local_currency','reporting_currency','created_by','segment']);

        $customerID= $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $salesReturn->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $salesReturn->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month']) && $input['month'] != [0]) {
                $salesReturn->whereMonth('salesReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year']) && $input['year'] != [0]) {
                $salesReturn->whereYear('salesReturnDate', '=', $input['year']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if ($input['customerID'] && !is_null($input['customerID'])) {
                $salesReturn->whereIn('customerID', $customerID);
            }
        }

        if (array_key_exists('returnType', $input)) {
            if ($input['returnType'] && !is_null($input['returnType']) && $input['returnType'] != [0]) {
                $salesReturn->where('returnType', $input['returnType']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $salesReturn = $salesReturn->where(function ($query) use ($search) {
                $query->where('salesReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                ->orWhereHas('customer', function ($q) use ($search){
                    $q->where('CustomerName', 'LIKE', "%{$search}%");
                });
            });
        }

        return $salesReturn;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.document_code')] = $val->salesReturnCode;
                $data[$x][trans('custom.type')] = StatusService::getSalesReturnType($val->returnType);
                $data[$x][trans('custom.customer_name')] = $val->customer? $val->customer->CustomerName : '';
                $data[$x][trans('custom.document_date')] = \Helper::dateFormat($val->salesReturnDate);
                $data[$x][trans('custom.segment')] = $val->segment? $val->segment->ServiceLineDes : '';
                $data[$x][trans('custom.comments')] = $val->narration;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.created_at')] = \Helper::dateFormat($val->createdDateTime);
                $data[$x][trans('custom.confirmed_on')] = \Helper::dateFormat($val->confirmedDate);
                $data[$x][trans('custom.approved_on')] = \Helper::dateFormat($val->approvedDate);
                $data[$x][trans('custom.transaction_currency')] = $val->transaction_currency? $val->transaction_currency->CurrencyCode : '';
                $data[$x][trans('custom.transaction_amount')] = $val->transactionAmount? number_format($val->transactionAmount + $val->VATAmount, $val->transaction_currency? $val->transaction_currency->DecimalPlaces : '', ".", "") : 0;

                $data[$x][trans('custom.local_currency')] = $val->local_currency? $val->local_currency->CurrencyCode : '';
                $data[$x][trans('custom.local_amount')] = $val->companyLocalAmount? number_format($val->companyLocalAmount + $val->VATAmountLocal, $val->local_currency? $val->local_currency->DecimalPlaces : '', ".", "") : 0;
                $data[$x][trans('custom.reporting_currency')] = $val->reporting_currency? $val->reporting_currency->CurrencyCode : '';
                $data[$x][trans('custom.reporting_amount')] = $val->companyReportingAmount? number_format($val->companyReportingAmount + $val->VATAmountRpt, $val->reporting_currency? $val->reporting_currency->DecimalPlaces : '', ".", "") : 0;

                $data[$x][trans('custom.status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approvedYN, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
