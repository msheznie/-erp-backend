<?php


namespace App\Services;

use App\helper\Helper;
use App\Models\BookInvSuppMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService
{

    public function __construct()
    {

    }

    public function getInvoicesList(Request $request, $supplierID = 0)
    {

        $input = $request->all();
        $per_page = $request->input('extra.per_page');
        $page = $request->input('extra.page');
        $search = $request->input('search.value');
        $search = $request->input('search.value');
        $filters = $request->input('extra');
        $query = $this->buildInvoiceQuery($supplierID, $search, $filters);
        return DataTables::eloquent($query)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bookingInvCode', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getInvoiceDetailsById($id, $supplierID)
    {
        return BookInvSuppMaster::select(
            [
                'bookingSuppMasInvAutoID',
                'companySystemID',
                'bookingInvCode',
                'documentSystemID',
                'supplierInvoiceNo',
                'secondaryRefNo',
                'createdDateTime',
                'createdDateAndTime',
                'createdUserSystemID',
                'comments',
                'bookingDate',
                'supplierID',
                'confirmedDate',
                'approvedDate',
                'localCurrencyID',
                'supplierTransactionCurrencyID',
                'companyReportingCurrencyID',
                'bookingAmountTrans',
                'cancelYN',
                'timesReferred',
                'refferedBackYN',
                'confirmedByEmpSystemID',
                'confirmedYN',
                'documentType',
                'approved',
                'supplierInvoiceDate',
                'employeeID',
                'modifiedUserSystemID',
                'canceledByEmpSystemID'
            ])
            ->where('bookingSuppMasInvAutoID', $id)
            ->where('supplierID', $supplierID)
            ->where('confirmedYN', 1)
            ->where('cancelYN', 0)
            ->whereIn('documentType', [0,1,2,3])
            ->with([
                'detail' => function ($q) {
                    $q->select(
                        [
                            "bookingSupInvoiceDetAutoID",
                            "bookingSuppMasInvAutoID",
                            "unbilledgrvAutoID",
                            "supplierID",
                            "purchaseOrderID",
                            "grvAutoID",
                            "grvType",
                            "supplierTransactionCurrencyID",
                            "supplierTransactionCurrencyER",
                            "companyReportingCurrencyID",
                            "companyReportingER",
                            "localCurrencyID",
                            "localCurrencyER",
                            "supplierInvoOrderedAmount",
                            "supplierInvoAmount",
                            "transSupplierInvoAmount",
                            "localSupplierInvoAmount",
                            "rptSupplierInvoAmount",
                            "totTransactionAmount",
                            "totLocalAmount",
                            "totRptAmount",
                            "VATAmount",
                            "VATAmountLocal",
                            "VATAmountRpt",
                            "isAddon",
                            "invoiceBeforeGRVYN"
                        ]
                    );
                },
              'approved_by' => function ($query) {
                $query->select(['employeeSystemID',
                                'approvedDate',
                                'approvedYN',
                                'documentSystemCode'])
                    ->with(['employee' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                }])
                ->where('documentSystemID', 11);
               },
                'company' => function ($q) {
                    $q->select(['companySystemID', 'CompanyID', 'CompanyName', 'CompanyAddress', 'logoPath', 'masterCompanySystemIDReorting']);
                },
                'transactioncurrency' => function ($q) {
                    $q->select(
                        [
                            "currencyID",
                            "CurrencyName",
                            "CurrencyCode",
                            "DecimalPlaces"
                        ]
                    );
                },
                'localcurrency' => function ($q) {
                    $q->select(
                        [
                            "currencyID",
                            "CurrencyName",
                            "CurrencyCode",
                            "DecimalPlaces"
                        ]
                    );
                },
                'rptcurrency' => function ($q) {
                    $q->select(
                        [
                            "currencyID",
                            "CurrencyName",
                            "CurrencyCode",
                            "DecimalPlaces"
                        ]
                    );
                },
                'supplier' => function ($q) {
                    $q->select(
                        [
                            "supplierCodeSystem",
                            "primarySupplierCode",
                            "supplierName"
                        ]
                    );
                },
                'confirmed_by' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                },
                'created_by' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                },
                'modified_by' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                },
                'cancelled_by' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                },
                'audit_trial.modified_by' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                }])
            ->first();
    }

    public function buildInvoiceQuery($supplierID, $search = null, $filters = null)
    {
        $query = BookInvSuppMaster::select([
            'bookingSuppMasInvAutoID',
            'companySystemID',
            'bookingInvCode',
            'documentSystemID',
            'supplierInvoiceNo',
            'secondaryRefNo',
            'createdDateTime',
            'createdDateAndTime',
            'createdUserSystemID',
            'comments',
            'bookingDate',
            'supplierID',
            'confirmedDate',
            'approvedDate',
            'supplierTransactionCurrencyID',
            'bookingAmountTrans',
            'cancelYN',
            'timesReferred',
            'refferedBackYN',
            'confirmedYN',
            'documentType',
            'approved',
            'supplierInvoiceDate'
        ])
            ->with([
                'created_by:employeeSystemID,empID,empName,empFullName',
                'transactioncurrency:currencyID,CurrencyName,CurrencyCode,DecimalPlaces',
                'supplier:supplierCodeSystem,primarySupplierCode,supplierName'
            ])
            ->with(
                [
                    'paymentDetail' => function ($q) {
                        $q->select('bookingInvSystemCode', 'supplierPaymentAmount', 'PayMasterAutoId')
                            ->whereHas('payment_master', function ($q2) {
                                $q2->where('approved', -1);
                            });
                    }
                ])
            ->where('supplierID', $supplierID)
            ->where('confirmedYN', 1)
            ->where('cancelYN', 0)
            ->whereIn('documentType', [0,1,2,3])
            ->orderBy('bookingSuppMasInvAutoID', 'desc');

        if($filters)
        {
            $createdType = $filters['createdType'] ?? null;
            $createdDate = $filters['createdDate'] ?? null;
            $amountType = $filters['amountType'] ?? null;
            $amount = $filters['amount'] ?? null;
            if ($createdType && $createdDate) {
                $createdDateFormatted = Carbon::parse($createdDate)->format('Y-m-d');
                switch ($createdType) {
                    case 1: //on
                        $query->whereDate('createdDateAndTime', '=', $createdDateFormatted);
                        break;

                    case 2: //before
                        $query->whereDate('createdDateAndTime', '<', $createdDateFormatted);
                        break;

                    case 3: //after
                        $query->whereDate('createdDateAndTime', '>', $createdDateFormatted);
                        break;
                    default:
                        break;
                }
            }

            if ($amountType && $amount !== null) {
                switch ($amountType) {
                    case 1: //Equal
                        $query->where('bookingAmountTrans', '=', $amount);
                        break;
                    case 2: //More Than
                        $query->where('bookingAmountTrans', '>', $amount);
                        break;
                    case 3: //Less Than
                        $query->where('bookingAmountTrans', '<', $amount);
                        break;
                    default:
                        break;
                }
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query->where(function ($query) use ($search) {
                $query->orWhere('supplierInvoiceNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('approvedDate', 'LIKE', "%{$search}%")
                    ->orWhere('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('bookingDate', 'LIKE', "%{$search}%")
                    ->orWhere('createdDateAndTime', 'LIKE', "%{$search}%")
                    ->orWhere('bookingAmountTrans', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }
}
