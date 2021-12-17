<?php


namespace App\Services;


use App\Models\BookInvSuppMaster;
use Illuminate\Http\Request;

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

        return BookInvSuppMaster::select(
            ['bookingSuppMasInvAutoID',
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
                'supplierInvoiceNo',
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
            ->orderBy('bookingSuppMasInvAutoID', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);
    }

    public function getInvoiceDetailsById($id, $supplierID)
    {

        return BookInvSuppMaster::where('bookingSuppMasInvAutoID', $id)
            ->where('supplierID', $supplierID)
            ->with(['grvdetail' => function ($query) {
                $query->with('grvmaster');
            }, 'directdetail' => function ($query) {
                $query->with('segment');
            }, 'detail' => function ($query) {
                $query->with('grvmaster');
            }, 'approved_by' => function ($query) {
                $query->with(['employee' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                }]);
                $query->where('documentSystemID', 11);
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
