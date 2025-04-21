<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\SegmentMaster;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\SupplierMaster;
use App\Models\GRVMaster;
use App\Models\SupplierCurrency;
use App\Models\CurrencyMaster;
use App\Models\SupplierAssigned;
use App\Repositories\GRVMasterRepository;
use App\Models\AppointmentDetails;
use App\Models\FinanceItemCategorySub;
use App\Models\PurchaseOrderDetails;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\WarehouseItems;
use App\Repositories\GRVDetailsRepository;
use App\Models\GRVDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\GrvDetailsPrn;
use App\Models\PurchaseReturn;


class DeliveryAppoinmentGRV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GRVMasterRepository $grvMasterRepo,GRVDetailsRepository $gRVDetailsRepo)
    {

        DB::beginTransaction();
        try {

            $mytime = new Carbon();

            $appoinment = Appointment::find($this->data['documentSystemCode']);
            $selected_segment = $this->data['segment'];
    
            $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $this->data['companySystemID'])
            ->whereDate('bigginingDate', '<=', $mytime)
            ->whereDate('endingDate', '>=', $mytime)
            ->first();
    
    
            if (!empty($fromCompanyFinanceYear)) {
    
                
                $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $this->data['companySystemID'])
                ->where('departmentSystemID', 10)
                ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
                ->whereDate('dateFrom', '<=', $mytime)
                ->whereDate('dateTo', '>=', $mytime)
                ->first();
    
    
                if(!empty($fromCompanyFinancePeriod)){
    
                    $supplierCurrencies = DB::table('suppliercurrency')
                    ->leftJoin('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                    ->where('supplierCodeSystem', '=', $appoinment->supplier_id)->first();
    
                   $serviceLine = SegmentMaster::where('serviceLineSystemID',$this->data['segment'])->first();
 
                    $detail['companySystemID'] = $this->data['companySystemID'];
                    $detail['stampDate'] = $mytime;
                    $detail['grvDate'] = $mytime;
                    $detail['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                    $detail['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                    $detail['grvTypeID'] = 2;
                    $detail['serviceLineSystemID'] = $this->data['segment'];
                    $detail['grvDoRefNo'] = $appoinment->primary_code;
                    $detail['grvNarration'] = 'Created from SRM Delivery Appointment '.$appoinment->primary_code;
                    $detail['grvLocation'] = $this->data['location'];
                    $detail['supplierID'] = $appoinment->supplier_id;
                    $detail['supplierTransactionCurrencyID'] = $supplierCurrencies->currencyID;
                    $detail['FYBiggin'] = $fromCompanyFinancePeriod->dateFrom;
                    $detail['FYEnd'] = $fromCompanyFinancePeriod->dateTo;              
                    $detail['createdPcID'] = gethostname();
                    $detail['createdUserID'] =  \Helper::getEmployeeID();
                    $detail['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $detail['documentSystemID'] =  3;
                    $detail['documentID'] = "GRV";
                    $detail["grvType"] = 'POG';
                    $detail["serviceLineCode"] = $serviceLine->ServiceLineCode;
    
                    $company = Company::find($this->data['companySystemID']);
                    if ($company) {
                    $detail['companyID'] = $company->CompanyID;
                    $detail['localCurrencyID'] = $company->localCurrencyID;
                    $detail['companyReportingCurrencyID'] = $company->reportingCurrency;
                    }
                    
                    $detail['vatRegisteredYN'] = 1;
                    $companyCurrencyConversion = \Helper::currencyConversion($this->data['companySystemID'], $supplierCurrencies->currencyID, $supplierCurrencies->currencyID, 0);
    
                    $detail['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $detail['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $detail['supplierTransactionER'] = 1;
    
                    $supplier = SupplierMaster::where('supplierCodeSystem', $appoinment->supplier_id)->first();
                    if ($supplier) {
                        $detail['supplierPrimaryCode'] = $supplier->primarySupplierCode;
                        $detail['supplierName'] = $supplier->supplierName;
                        $detail['supplierAddress'] = $supplier->address;
                        $detail['supplierTelephone'] = $supplier->telephone;
                        $detail['supplierFax'] = $supplier->fax;
                        $detail['supplierEmail'] = $supplier->supEmail;
                    }
    
                    $lastSerial = GRVMaster::where('companySystemID', $this->data['companySystemID'])
                    ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                    ->orderBy('grvSerialNo', 'desc')
                    ->lockForUpdate()
                    ->first();
        
                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->grvSerialNo) + 1;
                    }
                    $detail['grvSerialNo'] = $lastSerialNumber;
    
                    if ($fromCompanyFinancePeriod) {
                        $grvStartYear = $fromCompanyFinanceYear->bigginingDate;
                        $grvFinYearExp = explode('-', $grvStartYear);
                        $grvFinYear = $grvFinYearExp[0];
                    } else {
                        $grvFinYear = date("Y");
                    }
                    $document_id = "GRV";
                    $grvCode = ($company->CompanyID . '\\' . $grvFinYear . '\\' . $document_id . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $detail['grvPrimaryCode'] = $grvCode;
    
                    $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $appoinment->supplier_id)
                    ->where('isDefault', -1)
                    ->first();
        
                    if ($supplierCurrency) {
            
                        $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();
            
                        $detail['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
            
                        if ($erCurrency) {
                            $detail['supplierDefaultER'] = $erCurrency->ExchangeRate;
                        }
                    }
    
                    $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $appoinment->supplier_id)
                    ->where('companySystemID', $this->data['companySystemID'])
                    ->first();
        
                    if ($supplierAssignedDetail) {
                        $detail['liabilityAccountSysemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                        $detail['liabilityAccount'] = $supplierAssignedDetail->liabilityAccount;
                        $detail['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                        $detail['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                    }
                    $detail['deliveryAppoinmentID'] = $this->data['documentSystemCode'];
                    
                    $grvMaster = $grvMasterRepo->create($detail);
    
                    $grvAutoID =  $grvMaster->grvAutoID;
                    $GRVMaster = GRVMaster::where('grvAutoID', $grvAutoID)
                    ->first();
                   
                   
                    $appoinment_details = AppointmentDetails::whereHas('po_master',function($q) use($selected_segment){
                        $q->where('serviceLineSystemID',$selected_segment);
                    })->where('appointment_id',$appoinment->id)->with(['item'])->get();
    
                    $warehouseBinLocationPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 40)
                    ->where('companySystemID', $GRVMaster->companySystemID)
                    ->where('isYesNO', 1)
                    ->exists();

                    foreach($appoinment_details as $val)
                    {
    
                        $po_details =  PurchaseOrderDetails::find($val->po_detail_id);
                        $POMaster = ProcumentOrder::find($val->po_master_id);
                       
                        $totalAddedQty = $val->qty + $po_details->receivedQty;
                        if ($po_details->noQty == $totalAddedQty) {
                            $goodsRecievedYN = 2;
                            $GRVSelectedYN = 1;
                        } else {
                            $goodsRecievedYN = 1;
                            $GRVSelectedYN = 0;
                        }
                       
                        $detail['grvAutoID'] = $grvAutoID;
                        $detail['companySystemID'] = $GRVMaster->companySystemID;
                        $detail['companyID'] = $GRVMaster->companyID;
                        $detail['serviceLineCode'] = $GRVMaster->serviceLineCode;
                        $detail['purchaseOrderMastertID'] = $val->po_master_id;
                        $detail['purchaseOrderDetailsID'] = $val->po_detail_id;
                        $detail['itemCode'] = $val->item_id;
                        $detail['itemPrimaryCode'] = $val->item->primaryCode;
                        $detail['itemDescription'] = $val->item->itemDescription;
                        
                        $financeCategorySub = FinanceItemCategorySub::find($val->item->financeCategorySub);
                        if(isset($financeCategorySub))
                        {
                            $financeGLcodePL = $financeCategorySub->financeGLcodePL;
                            $financeGLcodePLSystemID = $financeCategorySub->financeGLcodePLSystemID;
                            $financeGLcodebBS = $financeCategorySub->financeGLcodebBS;
                            $financeGLcodebBSSystemID = $financeCategorySub->financeGLcodebBSSystemID;
                        }
                        else
                        {
                            $financeGLcodePL = null;
                            $financeGLcodePLSystemID = null;
                            $financeGLcodebBS = null;
                            $financeGLcodebBSSystemID = null;
                        }
            
                        $detail['financeGLcodebBSSystemID'] = $financeGLcodebBSSystemID;
                        $detail['financeGLcodebBS'] = $financeGLcodebBS;
                        $detail['financeGLcodePLSystemID'] = $financeGLcodePLSystemID;
                        $detail['financeGLcodePL'] = $financeGLcodePL;
                        $detail['itemFinanceCategoryID'] = $val->item->financeCategoryMaster;
                        $detail['itemFinanceCategorySubID'] = $val->item->financeCategorySub;
                        $detail['includePLForGRVYN'] =0;
                        $detail['supplierPartNumber'] = $val->item->secondaryItemCode;
                        $detail['unitOfMeasure'] = $val->item->unit;
                        $detail['noQty'] = $val->qty;
                        $detail['wasteQty'] = 0;
            
                        $detail['trackingType'] = (isset($val->trackingType)) ? $val->trackingType : null;
               
                        $warehouseItem = array();
                        if($warehouseBinLocationPolicy && $val->item->financeCategoryMaster){
                            $warehouseItemTemp = WarehouseItems::where('warehouseSystemCode',$GRVMaster->grvLocation)
                                                                 ->where('companySystemID' , $GRVMaster->companySystemID)
                                                                 ->where('itemSystemCode',$val->item_id)
                                                                 ->first();
                            if(!empty($warehouseItemTemp)){
                                $warehouseItem = $warehouseItemTemp;
                            }
                        }
            
                        $detail['prvRecievedQty'] = $po_details->receivedQty;
                        $detail['poQty'] = $po_details->noQty;
                        $totalNetcost = $po_details->GRVcostPerUnitSupTransCur * $val->qty;
                        $detail['unitCost'] = $po_details->GRVcostPerUnitSupTransCur;
                        $detail['discountPercentage'] = $po_details->discountPercentage;
                        $detail['discountAmount'] = $po_details->discountAmount;
                        $detail['netAmount'] = $totalNetcost;
                        $detail['comment'] = $po_details->comment;
                        $detail['supplierDefaultCurrencyID'] = $po_details->supplierDefaultCurrencyID;
                        $detail['supplierDefaultER'] = $po_details->supplierDefaultER;
                        $detail['supplierItemCurrencyID'] = $po_details->supplierItemCurrencyID;
                        $detail['foreignToLocalER'] = $po_details->foreignToLocalER;
                        $detail['companyReportingCurrencyID'] = $po_details->companyReportingCurrencyID;
                        $detail['companyReportingER'] = $po_details->companyReportingER;
                        $detail['localCurrencyID'] = $po_details->localCurrencyID;
                        $detail['localCurrencyER'] = $po_details->localCurrencyER;
                        $detail['addonDistCost'] = $po_details->addonDistCost;
                        $detail['GRVcostPerUnitLocalCur'] = $po_details->GRVcostPerUnitLocalCur;
                        $detail['GRVcostPerUnitSupDefaultCur'] = $po_details->GRVcostPerUnitSupDefaultCur;
                        $detail['GRVcostPerUnitSupTransCur'] = $po_details->GRVcostPerUnitSupTransCur;
                        $detail['GRVcostPerUnitComRptCur'] = $po_details->GRVcostPerUnitComRptCur;
                        $detail['landingCost_LocalCur'] =  $po_details->GRVcostPerUnitLocalCur;
                        $detail['landingCost_TransCur'] = $po_details->GRVcostPerUnitSupTransCur;
                        $detail['landingCost_RptCur'] = $po_details->GRVcostPerUnitComRptCur;
                        $detail['vatRegisteredYN'] = $POMaster->vatRegisteredYN;
                        $detail['supplierVATEligible'] = $POMaster->supplierVATEligible;
                        $detail['VATPercentage'] = $po_details->VATPercentage;
                        $detail['VATAmount'] = $po_details->VATAmount;
                        $detail['VATAmountLocal'] = $po_details->VATAmountLocal;
                        $detail['VATAmountRpt'] = $po_details->VATAmountRpt;
                        $detail['vatMasterCategoryID'] = $po_details->vatMasterCategoryID;
                        $detail['vatSubCategoryID'] = $po_details->vatSubCategoryID;
                        $detail['exempt_vat_portion'] = $po_details->exempt_vat_portion;
                        $detail['logisticsAvailable'] = $POMaster->logisticsAvailable;
                        $detail['binNumber'] = $warehouseItem ? $warehouseItem->binNumber : 0;
            
                        $detail['createdPcID'] = gethostname();
                        $detail['createdUserID'] = \Helper::getEmployeeID();
                        $detail['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            
                        $mp = isset($po_details->markupPercentage)?$po_details->markupPercentage:0;
                        $markupArray = $this->setMarkupPercentage($po_details->GRVcostPerUnitSupTransCur,$GRVMaster,$mp);
            
                        $detail['markupPercentage'] = $markupArray['markupPercentage'];
                        $detail['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
                        $detail['markupLocalAmount'] = $markupArray['markupLocalAmount'];
                        $detail['markupReportingAmount'] = $markupArray['markupReportingAmount'];
    
                        $item = $gRVDetailsRepo->create($detail);
    
                        $update = PurchaseOrderDetails::where('purchaseOrderDetailsID', $val->po_detail_id)
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $totalAddedQty]);
    
    
                        $this->checkPrnAndUpdateAsReturnedUsed($val->po_detail_id, $val->qty, $item->grvDetailsID);
                        
                        $purchaseOrderDetailTotalAmount = PurchaseOrderDetails::select(DB::raw('SUM(noQty) as detailQty,SUM(receivedQty) as receivedQty'))
                        ->where('purchaseOrderMasterID', $val->po_master_id)
                        ->first();
    
                        if ($purchaseOrderDetailTotalAmount['detailQty'] == $purchaseOrderDetailTotalAmount['receivedQty']) {
                            $updatePO = ProcumentOrder::find($val->po_master_id)
                                ->update(['poClosedYN' => 1, 'grvRecieved' => 2]);
                        } else {
                            $updatePO = ProcumentOrder::find($val->po_master_id)
                                ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                        }
            
                    }
    
                    $updateGrvMaster = GRVMaster::where('grvAutoID', $grvAutoID)
                    ->update(['pullType' => 1]);

                    $updateGrvMaster = Appointment::where('id', $this->data['documentSystemCode'])
                    ->update(['grv_create_yn' => 1,'grv' => $GRVMaster->grvPrimaryCode]);

                    DB::commit();
    
                }
                else
                {
                    Log::error('From Company Finance period not found, date : ');
                    
                }
    
            }
            else{
                Log::error('From Company Finance Year not found, date3 : ');
            }



        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }

    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }

    public function setMarkupPercentage($unitCost, $grvData , $markupPercentage=0, $markupTransAmount=0, $by = ''){
        $output['markupPercentage'] = 0;
        $output['markupTransactionAmount'] = 0;
        $output['markupLocalAmount'] = 0;
        $output['markupReportingAmount'] = 0;

        $markupAmendRestrictionPolicy = \Helper::checkRestrictionByPolicy($grvData->companySystemID,6);

        if(isset($grvData->supplierID) && $grvData->supplierID && $markupAmendRestrictionPolicy){

            $supplier= SupplierAssigned::where('supplierCodeSytem',$grvData->supplierID)
                ->where('companySystemID',$grvData->companySystemID)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();

            if($supplier->companySystemID && $supplier->isMarkupPercentage){
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $supplier->companySystemID)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO',1)
                    ->exists();

                if($hasEEOSSPolicy){

                    if($by == 'amount'){
                        $output['markupTransactionAmount'] = $markupTransAmount;
                        if($unitCost > 0 && $markupTransAmount > 0){
                            $output['markupPercentage'] = $markupTransAmount*100/$unitCost;
                        }
                    }else{
                        $percentage = ($markupPercentage != 0)? $markupPercentage:$supplier->markupPercentage;
                        if ($by == 'percentage'){
                            $percentage = $markupPercentage;
                            $output['markupPercentage'] = $percentage;
                        }
                        if($percentage != 0){
                            $output['markupPercentage'] = $percentage;
                            if($unitCost>0){

                                $output['markupTransactionAmount'] = $percentage*$unitCost/100;
                            }
                        }
                    }

                    if($output['markupTransactionAmount']>0){

                        if($grvData->supplierDefaultCurrencyID != $grvData->localCurrencyID){
                            $currencyConversion = \Helper::currencyConversion($grvData->companySystemID,$grvData->supplierDefaultCurrencyID,$grvData->localCurrencyID,$output['markupTransactionAmount']);
                            if(!empty($currencyConversion)){
                                $output['markupLocalAmount'] = $currencyConversion['documentAmount'];
                            }
                        }else{
                            $output['markupLocalAmount'] = $output['markupTransactionAmount'];
                        }

                        if($grvData->supplierDefaultCurrencyID != $grvData->companyReportingCurrencyID){
                            $currencyConversion = \Helper::currencyConversion($grvData->companySystemID,$grvData->supplierDefaultCurrencyID,$grvData->companyReportingCurrencyID,$output['markupTransactionAmount']);
                            if(!empty($currencyConversion)){
                                $output['markupReportingAmount'] = $currencyConversion['documentAmount'];
                            }
                        }else{
                            $output['markupReportingAmount'] =$output['markupTransactionAmount'];
                        }

                      
                        $output['markupTransactionAmount'] = \Helper::roundValue($output['markupTransactionAmount']);
                        $output['markupLocalAmount'] = \Helper::roundValue($output['markupLocalAmount']);
                        $output['markupReportingAmount'] = \Helper::roundValue($output['markupReportingAmount']);

                    }

                }

            }

        }

        return $output;
    }


    public function checkPrnAndUpdateAsReturnedUsed($purchaseOrderDetailsID, $newGrvQty, $grvDetailsID)
    {
        $getGrvDetails = GRVDetails::with(['prn_details' => function($query) {
                                        $query->whereRaw('noQty - receivedQty != ?', [0]);
                                   }])
                                   ->whereHas('prn_details', function($query) {
                                        $query->whereRaw('noQty - receivedQty != ?', [0]);
                                   })
                                   ->where('returnQty', '>', 0)
                                   ->where('purchaseOrderDetailsID', $purchaseOrderDetailsID)
                                   ->get();

        foreach ($getGrvDetails as $key => $value) {
            foreach ($value->prn_details as $key1 => $value1) {
                if ($newGrvQty > 0) {
                    if (($value1->noQty - $value1->receivedQty) > $newGrvQty) {
                        $receivedQty = $newGrvQty;

                        $newGrvQty = 0;
                    } else {
                        $receivedQty = $value1->noQty - $value1->receivedQty;

                        $newGrvQty = $newGrvQty - $receivedQty;
                    }


                    if ($value1->noQty == $receivedQty) {
                        $goodsRecievedYN = 2;
                        $GRVSelectedYN = 1;
                    } else {
                        $goodsRecievedYN = 1;
                        $GRVSelectedYN = 0;
                    }


                    $update = PurchaseReturnDetails::where('purhasereturnDetailID', $value1->purhasereturnDetailID)
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                    $grvDetailsPrnData = [
                        'grvDetailsID' => $grvDetailsID,
                        'purhasereturnDetailID' => $value1->purhasereturnDetailID,
                        'prnQty' => $receivedQty
                    ];

                    $createRespo = GrvDetailsPrn::insert($grvDetailsPrnData);
                    
                    $this->checkPurchaseReturnAndUpdateReturnStatus($value1->purhaseReturnAutoID);
                }
            }
        }
    }


    public function checkPurchaseReturnAndUpdateReturnStatus($purhaseReturnAutoID)
    {
      
        $purchaseOrderDetailTotalAmount = PurchaseReturnDetails::select(DB::raw('SUM(noQty) as detailQty,SUM(receivedQty) as receivedQty'))
                                                                ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
                                                                ->first();

      
        if ($purchaseOrderDetailTotalAmount['detailQty'] == $purchaseOrderDetailTotalAmount['receivedQty']) {
            $updatePO = PurchaseReturn::find($purhaseReturnAutoID)
                ->update(['prClosedYN' => 1, 'grvRecieved' => 2]);
        } else {
            $updatePO = PurchaseReturn::find($purhaseReturnAutoID)
                ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
        }
    }
}
