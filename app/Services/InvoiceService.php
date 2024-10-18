<?php


namespace App\Services;


use App\Models\BookInvSuppMaster;
use Illuminate\Http\Request;
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

        $query = BookInvSuppMaster::select(
            ['bookingSuppMasInvAutoID',
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
            ->with(['created_by' => function ($q) {
                $q->select(
                    [
                        "employeeSystemID",
                        "empID",
                        "empName",
                        "empFullName"
                    ]
                );
            }, 'transactioncurrency' => function ($q) {
                $q->select(
                    [
                        "currencyID",
                        "CurrencyName",
                        "CurrencyCode",
                        "DecimalPlaces"
                    ]
                );
            }, 'supplier' => function ($q) {
                $q->select(
                    [
                        "supplierCodeSystem",
                        "primarySupplierCode",
                        "supplierName"
                    ]
                );
            }])
            ->where('supplierID', $supplierID)
            ->where('approved', -1)
            ->where('cancelYN', 0)
            ->where('documentType', 0)
            ->orderBy('bookingSuppMasInvAutoID', 'desc');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('supplierInvoiceNo', 'LIKE', "%{$search}%");
                $query->orWhere('comments', 'LIKE', "%{$search}%");
                $query->orWhere('approvedDate', 'LIKE', "%{$search}%");
                $query->orWhere('bookingInvCode', 'LIKE', "%{$search}%");
                $query->orWhere('bookingDate', 'LIKE', "%{$search}%");
                $query->orWhere('createdDateAndTime', 'LIKE', "%{$search}%");
                $query->orWhere('bookingAmountTrans', 'LIKE', "%{$search}%");
            });
        }
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
            ->where('approved', -1)
            ->where('cancelYN', 0)
            ->where('documentType', 0)
            ->with(['detail', 'approved_by' => function ($query) {
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
            }, 'company',
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
}
