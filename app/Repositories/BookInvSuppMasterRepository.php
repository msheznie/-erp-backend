<?php

namespace App\Repositories;

use App\Models\BookInvSuppMaster;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\helper\StatusService;
use Illuminate\Http\Request;
/**
 * Class BookInvSuppMasterRepository
 * @package App\Repositories
 * @version August 8, 2018, 6:48 am UTC
 *
 * @method BookInvSuppMaster findWithoutFail($id, $columns = ['*'])
 * @method BookInvSuppMaster find($id, $columns = ['*'])
 * @method BookInvSuppMaster first($columns = ['*'])
*/
class BookInvSuppMasterRepository extends BaseRepository
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
        'bookingInvCode',
        'bookingDate',
        'comments',
        'secondaryRefNo',
        'supplierID',
        'supplierGLCode',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'documentType',
        'timesReferred',
        'RollLevForApp_curr',
        'interCompanyTransferYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'canceledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BookInvSuppMaster::class;
    }

    public function bookInvSuppListQuery($request, $input, $search = '', $supplierID, $projectID) {

        \DB::enableQueryLog();
        $invMaster = BookInvSuppMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with('created_by', 'transactioncurrency', 'supplier', 'employee' ,'project','localcurrency','rptcurrency');

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $invMaster->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $createdBy = collect($input['createdBy'])->pluck('id')->toArray();
                $invMaster->whereIn('createdUserSystemID', $createdBy);
            }

        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('documentType', $input)) {
            if (($input['documentType'] == 0 || $input['documentType'] == 1 || $input['documentType'] == 2 || $input['documentType'] == 3 || $input['documentType'] == 4) && !is_null($input['documentType'])) {
                $invMaster->where('documentType', $input['documentType']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('bookingDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('bookingDate', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $invMaster->whereIn('supplierID', $supplierID);
            }
        }

        if (array_key_exists('projectID', $input)) {
            if ($input['projectID'] && !is_null($input['projectID'])) {
                $invMaster->whereIn('projectID', $projectID);
            }
        }

        $invMaster = $invMaster->select(
            ['erp_bookinvsuppmaster.bookingSuppMasInvAutoID',
                'erp_bookinvsuppmaster.bookingInvCode',
                'erp_bookinvsuppmaster.documentSystemID',
                'erp_bookinvsuppmaster.supplierInvoiceNo',
                'erp_bookinvsuppmaster.secondaryRefNo',
                'erp_bookinvsuppmaster.createdDateTime',
                'erp_bookinvsuppmaster.createdDateAndTime',
                'erp_bookinvsuppmaster.createdUserSystemID',
                'erp_bookinvsuppmaster.comments',
                'erp_bookinvsuppmaster.bookingDate',
                'erp_bookinvsuppmaster.supplierID',
                'erp_bookinvsuppmaster.projectID',
                'erp_bookinvsuppmaster.employeeID',
                'erp_bookinvsuppmaster.confirmedDate',
                'erp_bookinvsuppmaster.approvedDate',
                'erp_bookinvsuppmaster.supplierTransactionCurrencyID',
                'erp_bookinvsuppmaster.localCurrencyID',
                'erp_bookinvsuppmaster.companyReportingCurrencyID',
                'erp_bookinvsuppmaster.bookingAmountTrans',
                'erp_bookinvsuppmaster.bookingAmountLocal',
                'erp_bookinvsuppmaster.bookingAmountRpt',
                'erp_bookinvsuppmaster.cancelYN',
                'erp_bookinvsuppmaster.timesReferred',
                'erp_bookinvsuppmaster.refferedBackYN',
                'erp_bookinvsuppmaster.confirmedYN',
                'erp_bookinvsuppmaster.documentType',
                'erp_bookinvsuppmaster.approved',
                'erp_bookinvsuppmaster.supplierInvoiceNo',
                'erp_bookinvsuppmaster.postedDate',
                'erp_bookinvsuppmaster.supplierInvoiceDate',
                'erp_bookinvsuppmaster.isBulkItemJobRun'
            ]);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $invMaster = $invMaster->where(function ($query) use ($search, $search_without_comma) {
                $query->where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierInvoiceNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('bookingAmountTrans', 'LIKE', "%{$search_without_comma}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('supplierName', 'like', "%{$search}%")
                            ->orWhere('primarySupplierCode', 'LIKE', "%{$search}%");
                    })->orWhereHas('employee', function ($query) use ($search) {
                        $query->where('empName', 'like', "%{$search}%")
                            ->orWhere('empID', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $invMaster;
    }

    public function setExportExcelData($dataSet,Request $request) {

        $local = $request->get('lang');
        if(!empty($local)) {
            app()->setLocale($local);
        }

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][__('custom.invoice_code')] = $val->bookingInvCode;
                $data[$x][__('custom.type')] = $val->documentType === 0? 'Supplier PO Invoice' : 'Supplier Direct Invoice';
                $data[$x]['Supplier'] = $val->supplier? $val->supplier->supplierName : '';
                $data[$x]['Invoice No'] = $val->supplierInvoiceNo;
                $data[$x]['Booking Invoice Date'] = \Helper::dateFormat($val->bookingDate);
                $data[$x]['Comments'] = $val->comments;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::convertDateWithTime($val->createdDateAndTime);
                $data[$x]['Confirmed on'] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x]['Approved on'] = \Helper::convertDateWithTime($val->approvedDate);
 
                $data[$x]['Transaction Currency'] = $val->supplierTransactionCurrencyID? ($val->transactioncurrency? $val->transactioncurrency->CurrencyCode : '') : '';
                $data[$x]['Transaction Amount'] = $val->transactioncurrency? number_format($val->bookingAmountTrans,  $val->transactioncurrency->DecimalPlaces, ".", "") : '';
                $data[$x]['Local Currency'] = $val->localCurrencyID? ($val->localcurrency? $val->localcurrency->CurrencyCode : '') : '';
                $data[$x]['Local Amount'] = $val->localcurrency? number_format($val->bookingAmountLocal,  $val->localcurrency->DecimalPlaces, ".", "") : '';
                $data[$x]['Reporting Currency'] = $val->companyReportingCurrencyID? ($val->rptcurrency? $val->rptcurrency->CurrencyCode : '') : '';
                $data[$x]['Reporting Amount'] = $val->rptcurrency? number_format($val->bookingAmountRpt,  $val->rptcurrency->DecimalPlaces, ".", "") : '';

                $data[$x]['Status'] = StatusService::getStatus($val->cancelYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

    public function getInvoiceDetails($id)
    {
        return BookInvSuppMaster::getInvoiceDetails($id);
    }
}
