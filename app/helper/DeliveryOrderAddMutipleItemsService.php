<?php
/**
 * =============================================
 * -- File Name : inventory.php
 * -- Project Name : ERP
 * -- Module Name :  email class
 * -- Author : Mohamed Fayas
 * -- Create date : 15 - August 2018
 * -- Description : This file contains the all the common inventory function
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Repositories\PurchaseRequestDetailsRepository;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\SupplierCurrency;
use App\Models\Unit;
use App\Models\SupplierMaster;
use App\Models\ItemMaster;
use App\Models\ProcumentOrder;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseOrderDetails;
use App\Models\User;
use App\Models\Employee;
use App\Models\PurchaseRequest;
use App\helper\CommonJobService;
use App\Models\QuotationDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\PurchaseRequestDetails;
use Illuminate\Support\Facades\DB;
use Response;
use App\Repositories\QuotationDetailsRepository;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class DeliveryOrderAddMutipleItemsService
{
    private $purchaseRequestDetailsRepository;
    private $quotationDetailsRepository;
    
    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo, QuotationDetailsRepository $quotationDetailsRepository)
    {
        $this->$quotationDetailsRepository = $quotationDetailsRepository;
    }

    public static function  addMultipleItems($records,$deliveryOrder,$db,$authID) {


        DeliveryOrderDetail::insert($records);

        Log::info($records);

    }


}