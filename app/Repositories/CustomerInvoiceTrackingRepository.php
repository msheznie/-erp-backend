<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceTracking;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceTrackingRepository
 * @package App\Repositories
 * @version February 9, 2020, 3:11 pm +04
 *
 * @method CustomerInvoiceTracking findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceTracking find($id, $columns = ['*'])
 * @method CustomerInvoiceTracking first($columns = ['*'])
*/
class CustomerInvoiceTrackingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'companyID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'customerInvoiceTrackingCode',
        'manualTrackingNo',
        'customerID',
        'contractNumber',
        'serviceLineCode',
        'comments',
        'approvalType',
        'submittedYN',
        'submittedEmpID',
        'submittedEmpName',
        'submittedDate',
        'submittedYear',
        'closeYN',
        'closedByEmpID',
        'closedByEmpName',
        'closedDate',
        'totalBatchAmount',
        'totalApprovedAmount',
        'totalRejectedAmount',
        'createdUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceTracking::class;
    }

    public function customerInvoiceTrackingListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $customerInvoiceTracking = CustomerInvoiceTracking::whereIn('companySystemID', $subCompanies)
            ->with(['detail','customer']);

        if (array_key_exists('customerID', $input)) {
            if ($input['customerID'] && !is_null($input['customerID'])) {
                $customerInvoiceTracking->where('customerID', $input['customerID']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $customerInvoiceTracking->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('contractUID', $input)) {
            if ($input['contractUID'] && !is_null($input['contractUID'])) {
                $customerInvoiceTracking->where('contractUID', $input['contractUID']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $customerInvoiceTracking->whereYear('submittedDate', $input['year']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $customerInvoiceTracking->whereMonth('submittedDate', $input['month']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $customerInvoiceTracking = $customerInvoiceTracking->where(function ($query) use ($search) {
                $query->where('manualTrackingNo', 'LIKE', "%{$search}%")
                    ->orWhere('customerInvoiceTrackingCode', 'LIKE', "%{$search}%");
            });
        }

        return $customerInvoiceTracking;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Tracking No'] = $val->customerInvoiceTrackingCode;
                $data[$x]['Batch No'] = $val->manualTrackingNo;
                $data[$x]['Customer'] = $val->customer? $val->customer->CustomerName : '';
                $data[$x]['Date'] = \Helper::dateFormat($val->submittedDate);
                $data[$x]['Month'] = date('M', strtotime($val->submittedDate));
                $data[$x]['Year'] = date('Y', strtotime($val->submittedYear));
                $data[$x]['Comments'] = $val->comments;
                $data[$x]['Batch Amount'] = $val->totalBatchAmount? number_format($val->totalBatchAmount, $val->DecimalPlaces? $val->DecimalPlaces : '', ".", "") : 0;
                $data[$x]['Approved Amount'] = $val->totalApprovedAmount? number_format($val->totalApprovedAmount, $val->DecimalPlaces? $val->DecimalPlaces : '', ".", "") : 0;
                $data[$x]['Rejected Amount'] = $val->totalRejectedAmount? number_format($val->totalRejectedAmount, $val->DecimalPlaces? $val->DecimalPlaces : '', ".", "") : 0;
                $data[$x]['Under Process'] = $val->totalBatchAmount? number_format($val->totalBatchAmount - $val->totalApprovedAmount - $val->totalRejectedAmount, $val->DecimalPlaces? $val->DecimalPlaces : '', ".", "") : 0;

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
