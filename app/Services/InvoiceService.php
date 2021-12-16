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
                                ->with(['created_by' => function ($q){
                                    $q->select(
                                        [
                                            "employeeSystemID",
                                            "empID",
                                            "empName",
                                            "empFullName"
                                        ]
                                    );
                                }, 'transactioncurrency'  => function($q){
                                    $q->select(
                                        [
                                            "currencyID",
                                            "CurrencyName",
                                            "CurrencyCode",
                                            "DecimalPlaces"
                                        ]
                                    );
                                }, 'supplier' => function($q){
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
                                ->paginate($per_page, ['*'], 'page', $page);
    }
}
