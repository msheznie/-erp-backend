<?php

namespace App\Repositories;

use App\Models\PurchaseReturn;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class PurchaseReturnRepository
 * @package App\Repositories
 * @version July 31, 2018, 6:08 am UTC
 *
 * @method PurchaseReturn findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturn find($id, $columns = ['*'])
 * @method PurchaseReturn first($columns = ['*'])
*/
class PurchaseReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'serviceLineCode',
        'documentSystemID',
        'companyID',
        'serviceLineSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'purchaseReturnDate',
        'purchaseReturnCode',
        'purchaseReturnRefNo',
        'narration',
        'purchaseReturnLocation',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'totalSupplierDefaultAmount',
        'totalSupplierTransactionAmount',
        'totalLocalAmount',
        'totalComRptAmount',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'timesReferred',
        'RollLevForApp_curr',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturn::class;
    }

    public function getAudit($id){
        return  $this->with(['created_by','confirmed_by','modified_by','location_by','company_by','details.unit','approved_by' => function ($query) {
            $query->with(['employee' =>  function($q){
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID',24);
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }

    public function purchaseReturnListQuery($request, $input, $search = '', $serviceLineSystemID, $grvLocation) {

        $purchaseReturn = PurchaseReturn::where('companySystemID', $input['companyId'])
        ->where('documentSystemID', $input['documentId'])
        ->with(['created_by', 'segment_by', 'location_by', 'supplier_by', 'currency_by','reporting_currency_by','local_currency_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturn->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('purchaseReturnLocation', $input)) {
            if ($input['purchaseReturnLocation'] && !is_null($input['purchaseReturnLocation'])) {
                $purchaseReturn->whereIn('purchaseReturnLocation', $grvLocation);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $purchaseReturn->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $purchaseReturn->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseReturn->whereMonth('purchaseReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseReturn->whereYear('purchaseReturnDate', '=', $input['year']);
            }
        }

        $purchaseReturn = $purchaseReturn->select(
            ['purhaseReturnAutoID',
                'purchaseReturnCode',
                'documentSystemID',
                'purchaseReturnRefNo',
                'createdDateTime',
                'createdUserSystemID',
                'narration',
                'purchaseReturnLocation',
                'purchaseReturnDate',
                'supplierID',
                'serviceLineSystemID',
                'confirmedDate',
                'approvedDate',
                'supplierTransactionCurrencyID',
                'companyReportingCurrencyID',
                'localCurrencyID',
                'timesReferred',
                'refferedBackYN',
                'confirmedYN',
                'approved'
            ]);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturn = $purchaseReturn->where(function ($query) use ($search) {
                $query->where('purchaseReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return $purchaseReturn;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Doc Code'] = $val->purchaseReturnCode;
                $data[$x]['Segment'] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x]['Reference No'] = $val->purchaseReturnRefNo;
                $data[$x]['Date'] = \Helper::dateFormat($val->purchaseReturnDate);
                $data[$x]['Supplier Code'] = $val->supplier_by? $val->supplier_by->primarySupplierCode : '';
                $data[$x]['Supplier Name'] = $val->supplier_by? $val->supplier_by->supplierName : '';
                $data[$x]['Location'] = $val->location_by? $val->location_by->wareHouseDescription : '';
                $data[$x]['Narration'] = $val->narration;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x]['Confirmed at'] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x]['Approved at'] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x]['Status'] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
