<?php

namespace App\Jobs;

use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\Appointment;
use App\Models\BookInvSuppMaster;
use App\Repositories\BookInvSuppMasterRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Models\GRVMaster;
use App\Models\UnbilledGrvGroupBy;
use App\Repositories\BookInvSuppDetRepository;
use App\Models\PoAdvancePayment;
use App\helper\TaxService;
use App\Models\GRVDetails;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\BookInvSuppDet;
use Illuminate\Support\Facades\Storage;


class DeliveryAppointmentInvoice implements ShouldQueue
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
    public function handle(BookInvSuppMasterRepository $bookInvSuppMasterRepository,BookInvSuppDetRepository $bookInvSuppDetRepo)
    {
        DB::beginTransaction();
        try {

            $mytime =  new Carbon();
            $appoinment = Appointment::find($this->data['id']);
    
            $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $this->data['companySystemID'])
            ->whereDate('bigginingDate', '<=', $mytime)
            ->whereDate('endingDate', '>=', $mytime)
            ->first();
    
    
            if (!empty($fromCompanyFinanceYear)) {
    
                
                $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $this->data['companySystemID'])
                ->where('departmentSystemID', 1)
                ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
                ->whereDate('dateFrom', '<=', $mytime)
                ->whereDate('dateTo', '>=', $mytime)
                ->first();
    
    
                if(!empty($fromCompanyFinancePeriod)){
    
         
                    $supplierCurrencies = DB::table('suppliercurrency')
                    ->leftJoin('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                    ->where('supplierCodeSystem', '=', $appoinment->supplier_id)->first();
    
                    $detail['companySystemID'] = $this->data['companySystemID'];
                    $detail['bookingDate'] = $mytime;
                    $detail['supplierInvoiceDate'] = $mytime;
                    $detail['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                    $detail['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                    $detail['custInvoiceDirectAutoID'] = null;
                    $detail['documentType'] = 0;
                    $detail['projectID'] = null;
                    $detail['secondaryRefNo'] = '';
                    $detail['supplierInvoiceNo'] = $appoinment->primary_code;
                    $detail['comments'] = 'Created from SRM Delivery Appointment '.$appoinment->primary_code;
                    $detail['supplierID'] = $appoinment->supplier_id;
                    $detail['supplierTransactionCurrencyID'] = $supplierCurrencies->currencyID;
                    $detail['preCheck'] = true; 
                    $detail['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom; 
                    $detail['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo; 
                    $detail['retentionPercentage'] = 0;
                    $detail['createdPcID'] = gethostname();
                    $detail['createdUserID'] =  \Helper::getEmployeeID();
                    $detail['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $detail['documentSystemID'] =  11;
                    $detail['documentID'] = "SI";
    
                    $companyCurrencyConversion = \Helper::currencyConversion($this->data['companySystemID'], $supplierCurrencies->currencyID, $supplierCurrencies->currencyID, 0);
    
                    $company = Company::find($this->data['companySystemID']);
                    if ($company) {
                    $detail['companyID'] = $company->CompanyID;
                    $detail['vatRegisteredYN'] = $company->vatRegisteredYN;
                    $detail['localCurrencyID'] = $company->localCurrencyID;
                    $detail['companyReportingCurrencyID'] = $company->reportingCurrency;
                    $detail['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $detail['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    }
    
                    $lastSerial = BookInvSuppMaster::where('companySystemID', $this->data['companySystemID'])
                    ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                    ->orderBy('serialNo', 'desc')
                    ->first();
        
                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }
                    
                    $detail['serialNo'] = $lastSerialNumber;
                    $detail['supplierTransactionCurrencyER'] = 1;
                    $detail['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                    $detail['FYEnd'] = $fromCompanyFinanceYear->endingDate;
    
                    $startYear = $fromCompanyFinanceYear['bigginingDate'];
                    $finYearExp = explode('-', $startYear);
                    $finYear = $finYearExp[0];
    
                    $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $detail['bookingInvCode'] = $bookingInvCode;
                    $detail['isLocalSupplier'] = \Helper::isLocalSupplier($appoinment->supplier_id, $this->data['companySystemID']);
    
    
                    $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
                    'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
                    ->where('supplierCodeSytem', $appoinment->supplier_id)
                    ->where('companySystemID', $this->data['companySystemID'])
                    ->first();
    
    
                    if ($supplierAssignedDetail) {
                        $detail['supplierVATEligible'] = $supplierAssignedDetail->vatEligible;
                        $detail['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                        $detail['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
                        $detail['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                        $detail['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                        $detail['VATPercentage'] = $supplierAssignedDetail->VATPercentage;
                    }
                    $detail['deliveryAppoinmentID'] = $this->data['id'];
                    
                    $invoice = $bookInvSuppMasterRepository->create($detail);

                    $invoice_id = $invoice->bookingSuppMasInvAutoID;
                    if($invoice_id)
                    {
                        $this->addAttachmentList($invoice_id, $this->data['companySystemID'], $this->data['attachmentsList']);
                    }

                    $grv_Details = GRVMaster::where('deliveryAppoinmentID',$this->data['id'])->select('grvAutoID')->first();

                    $details = UnbilledGrvGroupBy::where('grvAutoID',$grv_Details->grvAutoID)->select('purchaseOrderID','companySystemID')->get();

                    foreach($details as $purchase_id)
                    {
                       
                       $informations =  $this->getUnbilledGRVDetailsForSI($purchase_id->companySystemID, $purchase_id->purchaseOrderID, $grv_Details->grvAutoID,$invoice_id);
                      
                       $pullAmount = 0;
                       foreach ($informations as $new) {
                           
                           $groupMaster = UnbilledGrvGroupBy::find($new->unbilledgrvAutoID);

                               $totalPendingAmount = 0;
                               // balance Amount
                               $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $new->unbilledgrvAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();
           
                               if ($balanceAmount) {
                                   $totalPendingAmount = ($groupMaster->totTransactionAmount - $balanceAmount->SumOftotTransactionAmount);
                               } else {
                                   $totalPendingAmount = $groupMaster->totTransactionAmount;
                               }
           
                  
                               $prDetail_arr['bookingSuppMasInvAutoID'] = $invoice_id;
                               $prDetail_arr['unbilledgrvAutoID'] = $new->unbilledgrvAutoID;
                               $prDetail_arr['companySystemID'] = $groupMaster->companySystemID;
                               $prDetail_arr['companyID'] = $groupMaster->companyID;
                               $prDetail_arr['supplierID'] = $groupMaster->supplierID;
                               $prDetail_arr['purchaseOrderID'] = $groupMaster->purchaseOrderID;
                               $prDetail_arr['grvAutoID'] = $groupMaster->grvAutoID;
                               $prDetail_arr['grvType'] = $groupMaster->grvType;
                               $prDetail_arr['supplierTransactionCurrencyID'] = $groupMaster->supplierTransactionCurrencyID;
                               $prDetail_arr['supplierTransactionCurrencyER'] = $groupMaster->supplierTransactionCurrencyER;
                               $prDetail_arr['companyReportingCurrencyID'] = $groupMaster->companyReportingCurrencyID;
                               $prDetail_arr['companyReportingER'] = $groupMaster->companyReportingER;
                               $prDetail_arr['localCurrencyID'] = $groupMaster->localCurrencyID;
                               $prDetail_arr['localCurrencyER'] = $groupMaster->localCurrencyER;
                               $prDetail_arr['supplierInvoOrderedAmount'] = $totalPendingAmount;
                               $prDetail_arr['transSupplierInvoAmount'] = $groupMaster->totTransactionAmount;
                               $prDetail_arr['localSupplierInvoAmount'] = $groupMaster->totLocalAmount;
                               $prDetail_arr['rptSupplierInvoAmount'] = $groupMaster->totRptAmount;
   
                 
                               $item = $bookInvSuppDetRepo->create($prDetail_arr);

                               $updatePRMaster = UnbilledGrvGroupBy::find($new->unbilledgrvAutoID)
                                   ->update([
                                       'selectedForBooking' => -1
                                   ]);
           
           
                               $resDetail = $this->storeSupplierInvoiceGrvDetails($new, $item->bookingSupInvoiceDetAutoID, $invoice_id, $groupMaster);
           
                               if (!$resDetail['status']) {
                               }
           
                               $pullAmount = $resDetail['data'];
                               
                               if ($pullAmount > 0) {
                                   $supplierInvoiceDetail = $item->toArray();
           
                                   $supplierInvoiceDetail['supplierInvoAmount'] = $pullAmount;      
                                   
                                   $resultUpdateDetail = $this->updateDetail($supplierInvoiceDetail, $supplierInvoiceDetail['bookingSupInvoiceDetAutoID']);
           
                                   if (!$resultUpdateDetail['status']) {
                                  
                                   } 
                               }
                           
                       }
                       
              
                    }
                    DB::commit();
                }
                else
                {
                    Log::error('From Company Finance period not found, date : ');
                }
    
            }
            else{
                Log::error('From Company Finance Year not found, date : ');
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

    public function addAttachmentList($invoiceID, $companySystemID, $attachmentsList )
    {
        $company = Company::getComanyCode($companySystemID);
        $documentCode = DocumentMaster::getDocumentData(11);

        if (!empty($attachmentsList) && is_array($attachmentsList)) {
            foreach ($attachmentsList as $attachment) {
                if (!empty($attachment) && isset($attachment['file'])) {
                    $extension = $attachment['fileType'];
                    $file = $attachment['file'];
                    $decodeFile = base64_decode($file);
                    $attachmentNameWithExtension = time() . '_Supplier_Invoice.' . $extension;
                    $path = $company->CompanyID . '/SI/' . $invoiceID . '/' . $attachmentNameWithExtension;
                    Storage::disk('s3')->put($path, $decodeFile);

                    $att = [
                        'companySystemID' => $companySystemID,
                        'companyID' => $company->CompanyID,
                        'documentSystemID' => $documentCode->documentSystemID,
                        'documentID' => $documentCode->documentID,
                        'documentSystemCode' => $invoiceID,
                        'attachmentDescription' => $attachment['attachmentDescription'] ?? '',
                        'path' => $path,
                        'originalFileName' => $attachment['originalFileName'],
                        'myFileName' => $company->CompanyID . '_' . time() . '_Supplier_Invoice.' . $extension,
                        'attachmentType' => 11,
                        'sizeInKbs' => $attachment['sizeInKbs'],
                        'isUploaded' => 1
                    ];

                    DocumentAttachments::create($att);
                }
            }
        }
    }

    public function getUnbilledGRVDetailsForSI($company_id,$po_id,$grv_id,$invoice_id)
    {
       
        $companyID = $company_id;
        $purchaseOrderID = isset($po_id) ? $po_id : 0;
        $grvAutoID = isset($grv_id) ? $grv_id : 0;

        $bookingSuppMasInvAutoID = $invoice_id;

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        // $unbilledFilter = "";
        // if ($purchaseOrderID > 0) {
        //     $unbilledFilter = 'AND unbilledMaster.purchaseOrderID = ' . $purchaseOrderID ;
        // } else if ($grvAutoID > 0) {
        //     $unbilledFilter = 'AND unbilledMaster.grvAutoID = ' . $grvAutoID ;
        // }
        $unbilledFilter = 'AND unbilledMaster.purchaseOrderID = ' . $purchaseOrderID ;
        $unbilledFilter = 'AND unbilledMaster.grvAutoID = ' . $grvAutoID ;

        $bookingDate = Carbon::parse($bookInvSuppMaster->bookingDate)->format('Y-m-d');

        $unbilledGrvGroupBy = DB::select('SELECT
        grvmaster.grvPrimaryCode,
        unbilledMaster.unbilledgrvAutoID,
        unbilledMaster.totTransactionAmount,
        unbilledMaster.companySystemID,
        unbilledMaster.grvAutoID,
        unbilledMaster.purchaseOrderID,
        unbilledMaster.supplierID,
        unbilledMaster.logisticYN,
        currency.DecimalPlaces,
        IFNULL(bookdetail.SumOftotTransactionAmount,0) as invoicedAmount,
        (unbilledMaster.totTransactionAmount - (IFNULL(bookdetail.SumOftotTransactionAmount,0))) as balanceAmount,
        (unbilledMaster.totTransactionAmount - (IFNULL(bookdetail.SumOftotTransactionAmount,0))) as balanceAmountCheck
        FROM
            erp_unbilledgrvgroupby AS unbilledMaster
        INNER JOIN currencymaster AS currency ON unbilledMaster.supplierTransactionCurrencyID = currency.currencyID
        INNER JOIN erp_grvmaster AS grvmaster ON unbilledMaster.grvAutoID = grvmaster.grvAutoID
        AND grvmaster.interCompanyTransferYN = 0
        LEFT JOIN (
            SELECT
                erp_bookinvsuppdet.unbilledgrvAutoID,
                IFNULL(
                    Sum(
                        erp_bookinvsuppdet.totTransactionAmount
                    ),
                    0
                ) AS SumOftotTransactionAmount
            FROM
                erp_bookinvsuppdet
            GROUP BY
                erp_bookinvsuppdet.unbilledgrvAutoID
        ) AS bookdetail ON bookdetail.unbilledgrvAutoID = unbilledMaster.unbilledgrvAutoID
        WHERE
            unbilledMaster.companySystemID = ' . $companyID . '
        AND unbilledMaster.fullyBooked <> 2
        AND unbilledMaster.selectedForBooking = 0
        AND unbilledMaster.purhaseReturnAutoID IS NULL
        AND unbilledMaster.supplierID = ' . $bookInvSuppMaster->supplierID . '
        AND unbilledMaster.supplierTransactionCurrencyID = ' . $bookInvSuppMaster->supplierTransactionCurrencyID . '
        AND DATE_FORMAT(unbilledMaster.grvDate,"%Y-%m-%d") <= "' . $bookingDate . '"
        ' . $unbilledFilter . '
        HAVING ROUND(
                    unbilledMaster.totTransactionAmount,
                    currency.DecimalPlaces
                ) > 0 AND ROUND(balanceAmount,currency.DecimalPlaces) > 0');

        $company = Company::where('companySystemID', $bookInvSuppMaster->companySystemID)->first();
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $bookInvSuppMaster->supplierID)
                                                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                                    ->first();
        $valEligible = false;
        if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
            $valEligible = true;
        }

        foreach ($unbilledGrvGroupBy as $key => $value) {
            if ($value->logisticYN) {
                $pulledQry = DB::table('erp_bookinvsupp_item_det')
                                ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, logisticID")
                                ->where(function($query) {
                                    $query->where('logisticID', 0)
                                          ->orWhereNotNull('logisticID');
                                })
                                ->groupBy('logisticID');

                $returnedLogistic = DB::table('purchase_return_logistic')
                                        ->selectRaw("(SUM(logisticAmountTrans) + SUM(logisticVATAmount)) as prLogisticAmount, poAdvPaymentID")
                                        ->groupBy('poAdvPaymentID');

                $grvDetails = PoAdvancePayment::selectRaw("0 as grvDetailsID, erp_purchaseorderadvpayment.poAdvPaymentID as logisticID, itemmaster.primaryCode as itemPrimaryCode, itemmaster.itemDescription as itemDescription, erp_tax_vat_sub_categories.mainCategory as vatMasterCategoryID, erp_purchaseorderadvpayment.vatSubCategoryID, 0 as exempt_vat_portion, ROUND(((reqAmountTransCur_amount) + (erp_purchaseorderadvpayment.VATAmount)),7) as transactionAmount, ROUND(((reqAmountInPORptCur) + (erp_purchaseorderadvpayment.VATAmountRpt)),7) as rptAmount, ROUND(((reqAmountInPOLocalCur) + (erp_purchaseorderadvpayment.VATAmountLocal)),7) as localAmount, ROUND(((reqAmountTransCur_amount) + (erp_purchaseorderadvpayment.VATAmount) - IFNULL(pulledQry.SumOftotTransactionAmount,0) - IFNULL(returnedLogistic.prLogisticAmount,0)),7) as balanceAmount, ROUND(((reqAmountTransCur_amount) + (erp_purchaseorderadvpayment.VATAmount) - IFNULL(pulledQry.SumOftotTransactionAmount,0) - IFNULL(returnedLogistic.prLogisticAmount,0)),7) as balanceAmountCheck, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount")
                                                    ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                                    ->leftJoin('erp_tax_vat_sub_categories', 'erp_purchaseorderadvpayment.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                    ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                                    ->join('erp_addoncostcategories', 'erp_purchaseorderadvpayment.logisticCategoryID', '=', 'erp_addoncostcategories.idaddOnCostCategories')
                                                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'erp_addoncostcategories.itemSystemCode')
                                                    ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                                        $join->mergeBindings($pulledQry)
                                                             ->on('pulledQry.logisticID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                    ->leftJoin(\DB::raw("({$returnedLogistic->toSql()}) as returnedLogistic"), function($join) use ($returnedLogistic){
                                                        $join->mergeBindings($returnedLogistic)
                                                             ->on('returnedLogistic.poAdvPaymentID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                    ->where('erp_purchaseorderadvpayment.grvAutoID', $value->grvAutoID)
                                                    ->when($purchaseOrderID > 0, function($query) use($value){
                                                        $query->where('poID', $value->purchaseOrderID);
                                                    })
                                                    ->where('erp_purchaseorderadvpayment.supplierID',$value->supplierID)
                                                    ->groupBy('erp_purchaseorderadvpayment.poAdvPaymentID');

                $grv_details = $grvDetails->get();

                $value->balanceAmount = collect($grv_details)->sum('balanceAmount');
                $value->balanceAmountCheck = collect($grv_details)->sum('balanceAmount');

                $value->grv_details = $grv_details;
            } else {
                $rcmActivated = TaxService::isGRVRCMActivation($value->grvAutoID);

                $pulledQry = DB::table('erp_bookinvsupp_item_det')
                                ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, grvDetailsID")
                                ->groupBy('grvDetailsID');



                $grvDetails = GRVDetails::when($purchaseOrderID > 0, function($query) use($value){
                                            $query->where('purchaseOrderMastertID', $value->purchaseOrderID);
                                        })
                                       ->where('grvAutoID', $value->grvAutoID)
                                       ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                            $join->mergeBindings($pulledQry)
                                                 ->on('pulledQry.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID');
                                       });

                if ($valEligible && !$rcmActivated) {
                    $grvDetails = $grvDetails->selectRaw('erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, vatMasterCategoryID, vatSubCategoryID, exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty) + (VATAmountRpt*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty) + (VATAmountRpt*noQty)),7) as localAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) / noQty) * returnQty)),7) as balanceAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) / noQty) * returnQty)),7) as balanceAmountCheck, 0 as logisticID, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount, noQty, returnQty');
                    
                    $grv_details = $grvDetails->get();
                
                    foreach ($grv_details as $key1 => $value1) {
                        $res = TaxService::processGRVDetailVATForUnbilled($value1->grvDetailsID);

                        $value1->transactionAmount = $res['totalTransAmount'];
                        $value1->rptAmount = $res['totalRptAmount'];
                        $value1->localAmount = $res['totalLocalAmount'];

                        $value1->balanceAmount = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                        $value1->balanceAmountCheck = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                    }
                } else {
                    $grvDetails = $grvDetails->selectRaw('erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, vatMasterCategoryID, vatSubCategoryID, exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty)),7) as localAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) / noQty) * returnQty)),7) as balanceAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) / noQty) * returnQty)),7) as balanceAmountCheck, 0 as logisticID, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount, noQty, returnQty');
                    
                    $grv_details = $grvDetails->get();


                    foreach ($grv_details as $key1 => $value1) {
                        $res = TaxService::processGRVDetailVATForUnbilled($value1->grvDetailsID);

                        $value1->transactionAmount = $value1->transactionAmount - $res['exemptVATTrans'];
                        $value1->rptAmount = $value1->rptAmount - $res['exemptVATRpt'];
                        $value1->localAmount = $value1->localAmount - $res['exemptVATLocal'];

                        $value1->balanceAmount = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                        $value1->balanceAmountCheck = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                    }
                }
                


                $value->balanceAmount = collect($grv_details)->sum('balanceAmount');
                $value->balanceAmountCheck = collect($grv_details)->sum('balanceAmount');

                $grv_details = collect($grv_details)->where('balanceAmount', '>', 0)->all();

                $value->grv_details = $grv_details;
            }
        }


        $unbilledData = [];
        foreach ($unbilledGrvGroupBy as $key => $value) {
            $temp = [];
            $temp = $value;

            if (isset($value->grv_details)) {
                $grvDetailsData = [];
                foreach ($value->grv_details as $key1 => $value1) {
                    $grvDetailsData[] = $value1;
                }

                $temp->grv_details = $grvDetailsData;
            }

            $unbilledData[] = $temp;
        }

        return $unbilledData;

    }


    public function storeSupplierInvoiceGrvDetails($unbilledData, $bookingSupInvoiceDetAutoID, $bookingSuppMasInvAutoID, $groupMaster)
    {
    
            $totalPullAmount = 0;
            foreach ($unbilledData->grv_details as $key => $value) {
                $unbilledData->grv_details[$key]['supplierInvoAmount'] = $value['balanceAmountCheck'];
                $totalPullAmount += ((isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) ? $value['supplierInvoAmount'] : 0); 
    
                $totalPendingAmount = 0;
                if ($unbilledData->logisticYN) {
                    $grvDetail = PoAdvancePayment::find($value['logisticID']);
    
                    // balance Amount
                    $balanceAmount = collect(\DB::select('SELECT erp_bookinvsupp_item_det.logisticID, Sum(erp_bookinvsupp_item_det.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsupp_item_det WHERE logisticID = ' . $value['logisticID'] . ' GROUP BY erp_bookinvsupp_item_det.logisticID;'))->first();
                } else {
                    $grvDetail = GRVDetails::find($value['grvDetailsID']);
                    // balance Amount
                    $balanceAmount = collect(\DB::select('SELECT erp_bookinvsupp_item_det.grvDetailsID, Sum(erp_bookinvsupp_item_det.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsupp_item_det WHERE grvDetailsID = ' . $value['grvDetailsID'] . ' GROUP BY erp_bookinvsupp_item_det.grvDetailsID;'))->first();
                }
    
                if ($balanceAmount) {
                    $totalPendingAmount = ($value['transactionAmount'] - $balanceAmount->SumOftotTransactionAmount);
                } else {
                    $totalPendingAmount = $value['transactionAmount'];
                }
    
                $details = [
                    'bookingSupInvoiceDetAutoID' => $bookingSupInvoiceDetAutoID,
                    'bookingSuppMasInvAutoID' => $bookingSuppMasInvAutoID,
                    'unbilledgrvAutoID' => $unbilledData->unbilledgrvAutoID,
                    'companySystemID' => $unbilledData->companySystemID,
                    'grvDetailsID' => $value['grvDetailsID'],
                    'logisticID' => $value['logisticID'],
                    'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                    'vatSubCategoryID' => $value['vatSubCategoryID'],
                    'exempt_vat_portion' => $value['exempt_vat_portion'],
                    'purchaseOrderID' => $unbilledData->purchaseOrderID,
                    'grvAutoID' => $unbilledData->grvAutoID,
                    'supplierTransactionCurrencyID' => $groupMaster->supplierTransactionCurrencyID,
                    'supplierTransactionCurrencyER' => $groupMaster->supplierTransactionCurrencyER,
                    'companyReportingCurrencyID' => $groupMaster->companyReportingCurrencyID,
                    'companyReportingER' => $groupMaster->companyReportingER,
                    'localCurrencyID' => $groupMaster->localCurrencyID,
                    'localCurrencyER' => $groupMaster->localCurrencyER,
                    'supplierInvoOrderedAmount' => ($totalPendingAmount - floatval(((isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) ? $value['supplierInvoAmount'] : 0))),
                    'transSupplierInvoAmount' => $value['transactionAmount'],
                    'localSupplierInvoAmount' => $value['localAmount'],
                    'rptSupplierInvoAmount' => $value['rptAmount']
                ];
    
                if (isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) {
                    $details['supplierInvoAmount'] = floatval($value['supplierInvoAmount']);
                } else {
                    $details['supplierInvoAmount'] = 0;
                }
    
                $currency = \Helper::currencyConversion($unbilledData->companySystemID, $groupMaster->supplierTransactionCurrencyID, $groupMaster->supplierTransactionCurrencyID, $details['supplierInvoAmount']);
    
                $details['totTransactionAmount'] = $details['supplierInvoAmount'];
                $details['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
                $details['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);
    
                $totalVATAmount = ($unbilledData->logisticYN) ? $grvDetail->VATAmount : TaxService::processGRVDetailVATForUnbilled($grvDetail->grvDetailsID)['totalTransVATAmount'];
    
                 if($totalVATAmount > 0 && $value['transactionAmount'] > 0){
                    $percentage =  (floatval($details['totTransactionAmount'])/$value['transactionAmount']);
                    $VATAmount = $totalVATAmount * $percentage;
                    $currencyVat = \Helper::currencyConversion($unbilledData->companySystemID, $groupMaster->supplierTransactionCurrencyID, $groupMaster->supplierTransactionCurrencyID, $VATAmount);
                        $details['VATAmount'] = \Helper::roundValue($VATAmount);
                        $details['VATAmountLocal'] = \Helper::roundValue($currencyVat['localAmount']);
                        $details['VATAmountRpt'] = \Helper::roundValue($currencyVat['reportingAmount']);
                }
    
                $createRes = SupplierInvoiceItemDetail::create($details);
    
            }
    
            return ['status' => true, 'data' => $totalPullAmount];

    }

    public function updateDetail($input, $id)
    {
   
        $bookInvSuppDet = BookInvSuppDet::find($id);

        if (empty($bookInvSuppDet)) {
        }

        if($bookInvSuppDet->suppinvmaster && $bookInvSuppDet->suppinvmaster->confirmedYN){
        }

        $unbilledGrvGroupByMaster = UnbilledGrvGroupBy::where('unbilledgrvAutoID', $bookInvSuppDet['unbilledgrvAutoID'])
            ->first();
           

        if (empty($unbilledGrvGroupByMaster)) {
        }

        if ($input['supplierInvoAmount'] == "") {
            $input['supplierInvoAmount'] = 0;
        }

        $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $bookInvSuppDet['unbilledgrvAutoID'] . ' AND erp_bookinvsuppdet.bookingSupInvoiceDetAutoID != ' . $bookInvSuppDet->bookingSupInvoiceDetAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

        $returnAmount = 0;

        if (!$unbilledGrvGroupByMaster->logisticYN) {
            $bookInvSuppMaster = BookInvSuppMaster::find($bookInvSuppDet->bookingSuppMasInvAutoID);

            $company = Company::where('companySystemID', $bookInvSuppMaster->companySystemID)->first();
            $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $bookInvSuppMaster->supplierID)
                                                        ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                                        ->first();
            $valEligible = false;
            if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
                $valEligible = true;
            }

            $rcmActivated = TaxService::isGRVRCMActivation($unbilledGrvGroupByMaster->grvAutoID);

            $grvDetailData = GRVDetails::where('grvAutoID', $unbilledGrvGroupByMaster->grvAutoID)
                                       ->get();

            $returnAmount = 0;
            if ($valEligible && !$rcmActivated) {
                foreach ($grvDetailData as $key => $value) {
                    $grvProcessData = TaxService::processGRVDetailVATForUnbilled($value->grvDetailsID);
                    $returnAmount += round((($grvProcessData['totalTransAmount'] / $value->noQty) * $value->returnQty), 7);
                }

            } else {
                foreach ($grvDetailData as $key => $value) {
                    $returnAmount += round(($value->GRVcostPerUnitSupTransCur * $value->returnQty), 7);
                }
            }
        }
   

        if ($balanceAmount) {
            $totalPendingAmount = ($unbilledGrvGroupByMaster->totTransactionAmount - $balanceAmount->SumOftotTransactionAmount) - $returnAmount;
        } else {
            $totalPendingAmount = $unbilledGrvGroupByMaster->totTransactionAmount - $returnAmount;
        }

        $input['supplierInvoOrderedAmount'] = $totalPendingAmount - $input['supplierInvoAmount'];

        $currency = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $input['supplierInvoAmount']);

        $input['totTransactionAmount'] = $input['supplierInvoAmount'];
        $input['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
        $input['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

  
        $bookInvSuppDet = BookInvSuppDet::where('bookingSupInvoiceDetAutoID',$id)->update($input);

        //update vat

        if($unbilledGrvGroupByMaster->totalVATAmount > 0 && $unbilledGrvGroupByMaster->totTransactionAmount > 0){
            $bookInvSuppDet = BookInvSuppDet::find($id);
            $percentage =  ($bookInvSuppDet->totTransactionAmount/$unbilledGrvGroupByMaster->totTransactionAmount);
            $VATAmount = $unbilledGrvGroupByMaster->totalVATAmount * $percentage;
            $currencyVat = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $VATAmount);
            $vatData = array(
                'VATAmount' => \Helper::roundValue($VATAmount),
                'VATAmountLocal' => \Helper::roundValue($currencyVat['localAmount']),
                'VATAmountRpt' =>  \Helper::roundValue($currencyVat['reportingAmount'])
            );

            BookInvSuppDet::where('bookingSupInvoiceDetAutoID',$id)->update($vatData);
        }

        $bookInvSuppDet = BookInvSuppDet::find($id);
        // balance Amount
      
        $getTotal = BookInvSuppDet::where('unbilledgrvAutoID', $bookInvSuppDet->unbilledgrvAutoID)
            ->sum('totTransactionAmount');
       
        $updateUnbilledGrvGroupByMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID);
    
        if ($unbilledGrvGroupByMaster->totTransactionAmount == $getTotal) {

            $updateUnbilledGrvGroupByMaster->selectedForBooking = -1;
            $updateUnbilledGrvGroupByMaster->fullyBooked = 2;

        } else if ($getTotal != 0) {

            $updateUnbilledGrvGroupByMaster->selectedForBooking = -1;
            $updateUnbilledGrvGroupByMaster->fullyBooked = 1;

        } else if ($getTotal == 0) {

            $updateUnbilledGrvGroupByMaster->selectedForBooking = -1;
            $updateUnbilledGrvGroupByMaster->fullyBooked = 0;

        }
        $updateUnbilledGrvGroupByMaster->save();
      
        return ['status' => true, 'data' => $bookInvSuppDet->toArray()];

    }

}
