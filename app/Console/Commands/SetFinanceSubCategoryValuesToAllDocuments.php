<?php

namespace App\Console\Commands;
use App\helper\CommonJobService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ResetFinaceSubCategoryValuesInAllDocuments;

class SetFinanceSubCategoryValuesToAllDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:sub-category-values';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Finance Sub Category Values to All Documents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $records = [
            array(
                "table" => "erp_customerinvoiceitemdetails",
                "master" => "erp_custinvoicedirect",
                "key" => "custInvoiceDirectAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "financeGLcodeRevenueSystemID",
                        "financeGLcodeRevenue",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_customerinvoiceitemdetailsrefferedback",
                "master" => "erp_customerinvoiceitemdetails",
                "key" => "customerItemDetailID",
                "confirm" => null,
                "gParent" => "erp_custinvoicedirect",
                "gParentKey" => "custInvoiceDirectAutoID",
                "gConfirm" => "confirmedYN",
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "financeGLcodeRevenueSystemID",
                        "financeGLcodeRevenue",
                        "includePLForGRVYN"
                    )

            ),
            array(
                "table" => "erp_grvdetails",
                "master" => "erp_grvmaster",
                "key" => "grvAutoID",
                "confirm" => "grvConfirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_grvdetailsrefferedback",
                "master" => "erp_grvdetails",
                "key" => "grvDetailsID",
                "confirm" => null,
                "gParent" => "erp_grvmaster",
                "gParentKey" => "grvAutoID",
                "gConfirm" => "grvConfirmedYN",
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_inventoryreclassificationdetail",
                "master" => "erp_inventoryreclassification",
                "key" => "inventoryreclassificationID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_itemissuedetails",
                "master" => "erp_itemissuemaster",
                "key" => "itemIssueAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_itemissuedetails_refferedback",
                "master" => "erp_itemissuedetails",
                "key" => "itemIssueDetailID",
                "confirm" => null,
                "gParent" => "erp_itemissuemaster",
                "gParentKey" => "itemIssueAutoID",
                "gConfirm" => "confirmedYN",
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_itemreturndetails",
                "master" => "erp_itemreturnmaster",
                "key" => "itemReturnAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                 "columns" =>array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_requestdetails",
                "master" => "erp_request",
                "key" => "RequestID",
                "confirm" => "ConfirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => array(
                        "financeGLcodebBS",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                )
            ),
            array(
                "table" => "erp_prdetailsreferedhistory",
                "master" => "erp_purchaserequestdetails",
                "key" => "purchaseRequestID",
                "confirm" => null,
                "gParent" => "erp_purchaserequest",
                "gParentKey" => "purchaseRequestID",
                "gConfirm" => "PRConfirmedYN",
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_purchaseorderdetails",
                "master" => "erp_purchaseordermaster",
                "key" => "purchaseOrderMasterID",
                "masterKey" => "purchaseOrderID",
                "confirm" => "WO_confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                 "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_purchaseorderdetailsrefferedhistory",
                "master" => "erp_purchaseorderdetails",
                "key" => "purchaseOrderDetailsID",
                "masterKey" => "purchaseOrderID",
                "confirm" => null,
                "gParent" => "erp_purchaseordermaster",
                "gParentKey" => "purchaseOrderMasterID",
                "gConfirm" => "poConfirmedYN",
                 "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            // array(
            //     "table" => "erp_purchaseorderprocessdetails",
            //     "master" => "erp_purchaseorderdetails",
            //     "key" => "purchaseRequestID",
            //     "confirm" => null,
            //     "gParent" => "erp_purchaseordermaster",
            //     "gParentKey" => "purchaseOrderID",
            //     "gConfirm" => "WO_confirmedYN"
            // ),
            array(
                "table" => "erp_purchaserequestdetails",
                "master" => "erp_purchaserequest",
                "key" => "purchaseRequestID",
                "confirm" => "PRConfirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_purchasereturndetails",
                "master" => "erp_purchasereturnmaster",
                "key" => "purhaseReturnAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_purchasereturndetails_refferedback",
                "master" => "erp_purchasereturnmaster",
                "key" => "purhaseReturnAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => 
                    array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )                
            ),
            array(
                "table" => "erp_requestdetails_refferedback",
                "master" => "erp_requestdetails",
                "key" => "RequestDetailsID",
                "confirm" => null,
                "gParent" => "erp_request",
                "gParentKey" => "RequestID",
                "gConfirm" => "confirmedYN",
                "columns" => array(
                        "financeGLcodebBS",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_stockadjustmentdetails",
                "master" => "erp_stockadjustment",
                "key" => "stockAdjustmentAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_stockadjustmentdetails_refferedback",
                "master" => "erp_stockadjustmentdetails",
                "key" => "stockAdjustmentDetailsAutoID",
                "confirm" => null,
                "gParent" => "erp_stockadjustment",
                "gParentKey" => "stockAdjustmentAutoID",
                "gConfirm" => "confirmedYN",
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
            array(
                "table" => "erp_stock_count_details",
                "master" => "erp_stockcount",
                "key" => "stockCountAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
                
            ),
            array(
                "table" => "erp_stock_count_details_refferedback",
                "master" => "erp_stock_count_details",
                "key" => "stockCountDetailsAutoID",
                "confirm" => null,
                "gParent" => "erp_stockcount",
                "gParentKey" => "stockCountAutoID",
                "gConfirm" => "confirmedYN",
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodebBS",
                        "financeGLcodePLSystemID",
                        "financeGLcodePL",
                        "includePLForGRVYN"
                    )
            ),
           array(
                "table" => "supplier_invoice_items",
                "master" => "erp_bookinvsuppmaster",
                "key" => "bookingSuppMasInvAutoID",
                "confirm" => "confirmedYN",
                "gParent" => null,
                "gParentKey" => null,
                "gConfirm" => null,
                "columns" => array(
                        "financeGLcodebBSSystemID",
                        "financeGLcodePLSystemID",
                        "includePLForGRVYN"
                    )
                
            ),
        ];
        // $tenants = CommonJobService::tenant_list();
        
        // if(count($tenants) == 0){
        // }


        // foreach ($tenants as $tenant){
            // $tenant_database = $tenant->database;
            $tenant_database = null;

            ResetFinaceSubCategoryValuesInAllDocuments::dispatch($tenant_database,$records);
        // }

        
    }
}
