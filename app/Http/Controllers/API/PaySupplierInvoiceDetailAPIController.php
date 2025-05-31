<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file contains the all CRUD for Pay Pay Supplier Invoice Detail
 * -- REVISION HISTORY
 * -- Date: 18 September 2018 By: Nazir Description: Added new function getMatchingPaymentDetails()
 * -- Date: 19 September 2018 By: Nazir Description: Added new function addPaymentVoucherMatchingPaymentDetail()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Requests\API\CreatePaySupplierInvoiceDetailAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceDetailAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Models\AdvancePaymentDetails;
use App\Models\BankAssign;
use App\Models\BookInvSuppDet;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\PaymentVoucherBankChargeDetails;
use App\Models\SegmentMaster;
use App\Models\TaxVatCategories;
use App\Models\DirectInvoiceDetails;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\BookInvSuppMaster;
use App\Models\EmployeeLedger;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Repositories\PaySupplierInvoiceDetailRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\DebitNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\CurrencyMaster;


/**
 * Class PaySupplierInvoiceDetailController
 * @package App\Http\Controllers\API
 */
class PaySupplierInvoiceDetailAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceDetailRepository */
    private $paySupplierInvoiceDetailRepository;
    private $userRepository;

    public function __construct(PaySupplierInvoiceDetailRepository $paySupplierInvoiceDetailRepo, UserRepository $userRepo)
    {
        $this->paySupplierInvoiceDetailRepository = $paySupplierInvoiceDetailRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetails",
     *      summary="Get a listing of the PaySupplierInvoiceDetails.",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Get all PaySupplierInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceDetail")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->all();

        return $this->sendResponse($paySupplierInvoiceDetails->toArray(), 'Pay Supplier Invoice Details retrieved successfully');
    }

    /**
     * @param CreatePaySupplierInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceDetails",
     *      summary="Store a newly created PaySupplierInvoiceDetail in storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Store PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetail")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceDetails->toArray(), 'Pay Supplier Invoice Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Display the specified PaySupplierInvoiceDetail",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Get PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'Pay Supplier Invoice Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Update the specified PaySupplierInvoiceDetail in storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Update PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetail")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();
        
        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);
        $invoi = $payMaster->invoiceType;

        if (empty($payMaster)) {
            return $this->sendError('Payment voucher not found');
        }

        if ($payMaster->confirmedYN) {
            return $this->sendError('You cannot add Supplier PO Payment Detail, this document already confirmed', 500);
        }

        $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return $this->sendError('Selected Bank is not active', 500, ['type' => 'amountmismatch']);
        }

        $bankAccount = \App\Models\BankAccount::isActive()->find($payMaster->BPVAccount);

        if (empty($bankAccount)) {
            return $this->sendError('Selected Bank Account is not active', 500, ['type' => 'amountmismatch']);
        }

        if (!$input["supplierPaymentAmount"]) {
            $input["supplierPaymentAmount"] = 0;
        }

        if ($input["isPullAmount"] == 1) {
            $input["supplierPaymentAmount"] = $paySupplierInvoiceDetail->paymentBalancedAmount;
        }

        $supplierPaidAmountSumPayment = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                                                                                ->whereHas('payment_master', function($query) use($invoi){
                                                                                                    $query->when(($invoi == 6 || $invoi == 7),function($Q1){
                                                                                                        $Q1->whereIn('invoiceType',[6,7]);
                                                                                                    })->when(($invoi != 6 && $invoi != 7),function($Q2){
                                                                                                        $Q2->where(function($query) {
                                                                                                            $query->where('invoiceType','!=',6)
                                                                                                                  ->where('invoiceType','!=',7);
                                                                                                        });
                                                                                                    });
                                                                                                 })->where(function($query){
                                                                                                    $query->where('documentSystemID', '=', NULL)
                                                                                                            ->orWhere('documentSystemID', '=', 4);
                                                                                                 })
                                                                                                 ->where('apAutoID', $input["apAutoID"])
                                                                                                ->where('payDetailAutoID', '<>', $id)
                                                                                                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                                                                                ->first();
                                                                                                
        $supplierPaidAmountSumDebit = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                                                                      ->whereHas('debite_note', function($query) use($invoi){
                                                                                                $query->when(($invoi == 6 || $invoi == 7),function($Q1){
                                                                                                    $Q1->where('type',2);
                                                                                                })->when(($invoi != 6 && $invoi != 7),function($Q2){
                                                                                                    $Q2->where('type',1);
                                                                                                });
                                                                                        })->where('documentSystemID', '=', 15)
                                                                                        ->where('apAutoID', $input["apAutoID"])
                                                                                        ->where('payDetailAutoID', '<>', $id)
                                                                                        ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                                                                        ->first();                                                                                        
   
        $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = $supplierPaidAmountSumPayment["SumOfsupplierPaymentAmount"] + $supplierPaidAmountSumDebit["SumOfsupplierPaymentAmount"];
          

       

                                                        

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input["bookingInvSystemCode"])->where('documentSystemID', $input["addedDocumentSystemID"])->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();
        

        $machAmount = 0;
        if ($matchedAmount) {
            $machAmount = $matchedAmount["SumOfmatchedAmount"];
        }

        $paymentBalancedAmount = $paySupplierInvoiceDetail->supplierInvoiceAmount - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));


        if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {
            //supplier invoice
            if ($input["supplierPaymentAmount"] > $paymentBalancedAmount) {
                return $this->sendError('Payment amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch', 'amount' => $paymentBalancedAmount]);
            }
        } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {
            //debit note
            if ($input["supplierPaymentAmount"] < $paymentBalancedAmount) {
                return $this->sendError('Payment amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch', 'amount' => $paymentBalancedAmount]);
            }
        }
       
        if($input["isRetention"] == 1 && $input["bookingInvSystemCode"]){
            $bookInvMaster = BookInvSuppMaster::find($input["bookingInvSystemCode"]);
            if($bookInvMaster && $bookInvMaster->retentionAmount != 0){
                if($bookInvMaster->documentType == 1) {

                        if ($bookInvMaster->rcmActivated) {
                            $input["retentionVatAmount"] = ($input["supplierPaymentAmount"] / $bookInvMaster->retentionAmount) * $bookInvMaster->retentionVatAmount;
                        } else {
                            $input["retentionVatAmount"] = ($input["supplierPaymentAmount"] / ($bookInvMaster->retentionAmount - $bookInvMaster->retentionVatAmount)) * $bookInvMaster->retentionVatAmount;
                        }

                }
                else if($bookInvMaster->documentType == 0) {
                    if (TaxService::isSupplierInvoiceRcmActivated($bookInvMaster->bookingSuppMasInvAutoID)) {
                        $input["retentionVatAmount"] = ($input["supplierPaymentAmount"] / $bookInvMaster->retentionAmount) * $bookInvMaster->retentionVatAmount;
                    } else {
                        $input["retentionVatAmount"] = ($input["supplierPaymentAmount"] / ($bookInvMaster->retentionAmount - $bookInvMaster->retentionVatAmount)) * $bookInvMaster->retentionVatAmount;
                    }

                }
                else{
                    $input["retentionVatAmount"] = ($input["supplierPaymentAmount"] / ($bookInvMaster->retentionAmount - $bookInvMaster->retentionVatAmount)) * $bookInvMaster->retentionVatAmount;
                }

                $input['vatMasterCategoryID'] = null;
                $input['vatSubCategoryID'] = null;

                $taxVatCategories = TaxVatCategories::where('subCatgeoryType', 1)->where('mainCategory', 2)->where('isActive', 1)->first();
                if ($taxVatCategories) {
                    $input['vatMasterCategoryID'] = $taxVatCategories->mainCategory;
                    $input['vatSubCategoryID'] = $taxVatCategories->taxVatSubCategoriesAutoID;
                }

                $input['VATAmount'] = $input['retentionVatAmount'];
                $input['VATAmountLocal'] = Helper::conversionCurrencyByER($input['supplierTransCurrencyID'],$input['localCurrencyID'],$input['retentionVatAmount'],$input['localER']);
                $input['VATAmountRpt'] = Helper::conversionCurrencyByER($input['supplierTransCurrencyID'],$input['comRptCurrencyID'],$input['retentionVatAmount'],$input['comRptER']);
                $input['VATPercentage'] = Helper::roundValue(($input['retentionVatAmount'] / $input['supplierPaymentAmount']) * 100);
            }
        }


        $supplierPaymentAmount = isset($input["supplierPaymentAmount"]) ? $input["supplierPaymentAmount"] : 0;
        try{
            $input["paymentBalancedAmount"] = $paymentBalancedAmount - $supplierPaymentAmount;
        }catch (\Exception $e){
            $input["paymentBalancedAmount"] = $paymentBalancedAmount - (float) preg_replace("/[^0-9.]/", "",$supplierPaymentAmount);
        }       

        $conversionAmount = \Helper::convertAmountToLocalRpt(4, $input["payDetailAutoID"], $input["supplierPaymentAmount"]);
        $input["paymentSupplierDefaultAmount"] = \Helper::roundValue($conversionAmount["defaultAmount"]);
        $input["paymentLocalAmount"] = $conversionAmount["localAmount"];
        $input["paymentComRptAmount"] = $conversionAmount["reportingAmount"];
        unset($input['pomaster']);

        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->update($input, $id);

        $supplierPaidAmountSumPayment = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                            ->whereHas('payment_master', function($query) use($invoi){
                                                $query->when(($invoi == 6 || $invoi == 7),function($Q1){
                                                    $Q1->whereIn('invoiceType',[6,7]);
                                                })->when(($invoi != 6 && $invoi != 7),function($Q2){
                                                    $Q2->where(function($query) {
                                                            $query->where('invoiceType','!=',6)
                                                                  ->where('invoiceType','!=',7);
                                                        });
                                                });
                                            })->where(function($query){
                                                $query->where('documentSystemID', '=', NULL)
                                                        ->orWhere('documentSystemID', '=', 4);
                                            })
                                            ->where('apAutoID', $input["apAutoID"])
                                            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                            ->first();

         $supplierPaidAmountSumDebit = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                            ->whereHas('debite_note', function($query) use($invoi){
                                                      $query->when(($invoi == 6 || $invoi == 7),function($Q1){
                                                          $Q1->where('type',2);
                                                      })->when(($invoi != 6 && $invoi != 7),function($Q2){
                                                          $Q2->where('type',1);
                                                      });
                                              })->where('documentSystemID', '=', 15)
                                              ->where('apAutoID', $input["apAutoID"])
                                              ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                              ->first();  

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input["bookingInvSystemCode"])->where('documentSystemID', $input["addedDocumentSystemID"])->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();
       

        $machAmount = 0;
        if ($matchedAmount) {
            $machAmount = $matchedAmount["SumOfmatchedAmount"];
        }

        $totalPaidAmount = ($supplierPaidAmountSumPayment["SumOfsupplierPaymentAmount"] + $supplierPaidAmountSumDebit["SumOfsupplierPaymentAmount"] + ($machAmount * -1));
    
        if ($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7) {
            if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {
                if ($totalPaidAmount == 0) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $paySupplierInvoiceDetail->supplierInvoiceAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1]);
                }
            } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {
                if ($totalPaidAmount == 0) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                }
            }
        } else {
            if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {
                if ($totalPaidAmount == 0) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $paySupplierInvoiceDetail->supplierInvoiceAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1]);
                }
            } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {
                if ($totalPaidAmount == 0) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                }
            }
        }


        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'PaySupplierInvoiceDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Remove the specified PaySupplierInvoiceDetail from storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Delete PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
            $paySupplierInvoiceDetailDelete = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);
            $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);
            $payMaster = PaySupplierInvoiceMaster::find($paySupplierInvoiceDetail->PayMasterAutoId);

            if (empty($paySupplierInvoiceDetail)) {
                return $this->sendError('Pay Supplier Invoice Detail not found');
            }

            if($paySupplierInvoiceDetail->documentID == 'PV' && $paySupplierInvoiceDetail->documentSystemID == 4){
                $payMaster = PaySupplierInvoiceMaster::find($paySupplierInvoiceDetail->PayMasterAutoId);

                if (empty($payMaster)) {
                    return $this->sendError('Payment voucher not found');
                }
                $isPaymentVoucher = true;
            } else {
                $isPaymentVoucher = false;
            }

            if($paySupplierInvoiceDetail->documentID == 'DN' && $paySupplierInvoiceDetail->documentSystemID == 15){
                $payMaster = DebitNote::find($paySupplierInvoiceDetail->PayMasterAutoId);

                if (empty($payMaster)) {
                    return $this->sendError('Debit Note not found');
                }
                $isDebitNote = true;
            } else {
                $isDebitNote = false;
            }

            if ($paySupplierInvoiceDetail->matchingDocID == 0) {
                if ($paySupplierInvoiceDetail->payment_master && $paySupplierInvoiceDetail->payment_master->confirmedYN) {
                    return $this->sendError('You cannot delete the detail, this document already confirmed', 500);
                }
            }
            $user_type = null;
            $matchDocumentMasterObj = MatchDocumentMaster::find($paySupplierInvoiceDetail->matchingDocID);
            if(isset($matchDocumentMasterObj))
            {
                $user_type = $matchDocumentMasterObj->user_type;

                if(isset($matchDocumentMasterObj['matchedAmount'])) {
                    $matchDocumentMasterObj['matchedAmount'] = $matchDocumentMasterObj->matchedAmount - $paySupplierInvoiceDetail->supplierPaymentAmount;
                }

                if(isset($matchDocumentMasterObj['matchingAmount'])) {
                    $matchDocumentMasterObj['matchedAmount'] = $matchDocumentMasterObj->matchedAmount - $paySupplierInvoiceDetail->supplierPaymentAmount;
                }
                
                $matchDocumentMasterObj->save();
            }
           

            if ($paySupplierInvoiceDetail->documentSystemID != 0) {
                if ($paySupplierInvoiceDetail->matching_master && $paySupplierInvoiceDetail->matching_master->matchingConfirmedYN) {
                    return $this->sendError('You cannot delete the detail, this document already confirmed', 500);
                }
            }

            $paySupplierInvoiceDetailDelete->delete();

            

            if($user_type == 1)
            {
                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, 
                Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->when( ( ($isPaymentVoucher && ($payMaster->invoiceType != 6 && $payMaster->invoiceType != 7)) || ($isDebitNote && ($payMaster && $payMaster->type == 1)) ), function($query) {
                        $query->whereHas('payment_master', function($query) {
                            $query->where(function($query) {
                                $query->where('invoiceType', '!=', 6)
                                      ->where('invoiceType', '!=', 7);
                            });
                        });

                  })->where('apAutoID', $paySupplierInvoiceDetail->apAutoID)
                  ->whereHas('matching_master',function($query){
                    $query->where('user_type',1);
                 })
                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                ->first();
            }
            else if($user_type == 2)
            {

                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, 
                Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->when( ( ($isPaymentVoucher && ($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7)) || ($isDebitNote && ($payMaster && $payMaster->type == 2)) ), function($query) {
                $query->whereHas('payment_master', function($query) {
                    $query->whereIn('invoiceType',[6,7]);
                });
                 })->where('apAutoID', $paySupplierInvoiceDetail->apAutoID)
                 ->whereHas('matching_master',function($query){
                    $query->where('user_type',2);
                 })
                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                ->first();

            }
            else
            {
                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, 
                       Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                ->when((($isPaymentVoucher && (isset($payMaster) && $payMaster->invoiceType == 6 || isset($payMaster) &&  $payMaster->invoiceType == 7)) || ($isDebitNote && (isset($payMaster) &&  $payMaster->type == 2))), function($query) {
                    $query->whereHas('payment_master', function($query) {
                        $query->whereIn('invoiceType',[6,7]);
                    });
                })
                ->when((($isPaymentVoucher && ((isset($payMaster) &&  $payMaster->invoiceType != 6) && (isset($payMaster) &&  $payMaster->invoiceType != 7))) || ($isDebitNote && (isset($payMaster) &&  $payMaster->type == 1))), function($query) {
                    $query->whereHas('payment_master', function($query) {
                        $query->where(function($query) {
                            $query->where('invoiceType', '!=', 6)
                                  ->where('invoiceType', '!=', 7);
                        });
                    });
                })
                ->where('apAutoID', $paySupplierInvoiceDetail->apAutoID)
                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                ->first();

            }

           

            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, 
                   Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')
                ->where('PayMasterAutoId', $paySupplierInvoiceDetail->bookingInvSystemCode)
                ->where('documentSystemID', $paySupplierInvoiceDetail->addedDocumentSystemID)
                ->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')
                ->first();


            $machAmount = 0;
            if ($matchedAmount) {
                $machAmount = $matchedAmount["SumOfmatchedAmount"];
            }

            if ($supplierPaidAmountSum) {
                $totalPaidAmount = ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));
            } else {
                $totalPaidAmount = ($machAmount * -1);
            }


            if (isset($payMaster) &&  $payMaster->invoiceType == 6 || isset($payMaster) &&  $payMaster->invoiceType == 7) {
                if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {
                    if ($totalPaidAmount == 0) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                    } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                    } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                    }
                } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {
                    if ($totalPaidAmount == 0) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                    } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                    } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                    } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                        $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                            ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                    }
                }
            } else {
                if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {
                    if($user_type == 2)
                    {
                          if ($totalPaidAmount == 0) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        }
                        
                    }
                    else 
                    {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        }
                    }
                   

              
                } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {
                  
                    if($user_type == 2)
                    {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        }
                    }
                    else 
                    {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        }

                    }
                
                }
            }


            DB::commit();
            return $this->sendResponse($id, 'Pay Supplier Invoice Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 500);
        }
    }


    public function deleteAllPOPaymentDetail(Request $request)
    {
        $payMasterAutoId = $request->PayMasterAutoId;

        DB::beginTransaction();
        try {

            $payMaster = PaySupplierInvoiceMaster::find($payMasterAutoId);

            if (empty($payMaster)) {
                return $this->sendError('Payment voucher not found');
            }

            if ($payMaster->confirmedYN) {
                return $this->sendError('You cannot delete Supplier PO Payment Detail, this document already confirmed', 500);
            }

            /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
            $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWhere(['PayMasterAutoId' => $payMasterAutoId]);

            if (empty($paySupplierInvoiceDetail)) {
                return $this->sendError('Pay Supplier Invoice Detail not found');
            }

            foreach ($paySupplierInvoiceDetail as $val) {

                $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->find($val->payDetailAutoID);
                $paySupplierInvoiceDetail->delete();

                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                                ->where('apAutoID', $val->apAutoID)
                                                ->when(($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7), function($query) {
                                                    $query->whereHas('payment_master', function($query) {
                                                        $query->whereIn('invoiceType',[6,7]);
                                                    });
                                                })
                                                ->when(($payMaster->invoiceType != 6 && $payMaster->invoiceType != 7), function($query) {
                                                    $query->whereHas('payment_master', function($query) {
                                                        $query->where(function($query) {
                                                            $query->where('invoiceType', '!=', 6)
                                                                  ->where('invoiceType', '!=', 7);
                                                        });
                                                    });
                                                })
                                                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $val->bookingInvSystemCode)->where('documentSystemID', $val->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }

                $totalPaidAmount = ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));

                if ($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7) {
                    if ($val->addedDocumentSystemID == 11) {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if (($val->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                            $updatePayment = EmployeeLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        }
                    } else if ($val->addedDocumentSystemID == 15 || $val->addedDocumentSystemID == 24) {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount < $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount > $totalPaidAmount) {
                            $updatePayment = EmployeeLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        }
                    }
                } else {
                    if ($val->addedDocumentSystemID == 11) {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if (($val->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        }
                    } else if ($val->addedDocumentSystemID == 15 || $val->addedDocumentSystemID == 24) {
                        if ($totalPaidAmount == 0) {
                            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                                ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount < $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                        } else if ($val->supplierInvoiceAmount > $totalPaidAmount) {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                                ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                        }
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($payMasterAutoId, 'Pay Supplier Invoice Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }


    public function addPOPaymentDetail(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);

        if (empty($payMaster)) {
            return $this->sendError('Payment voucher not found');
        }
        
        if ($payMaster->confirmedYN) {
            return $this->sendError('You cannot add Supplier PO Payment Detail, this document already confirmed', 500);
        }

        if ($payMaster->invoiceType == 6) {
            $result = $this->addEmployeePaymentDetail($user, $payMaster, $input);
            if (!$result['status']) {
                return $this->sendError("Error. Please check again.", 500, $result['message']);
            } else {
                return $this->sendResponse('', 'Payment details saved successfully');
            }
        }


        $isAdvancePaymentPaidChk = $input['isAdvancePaymentPaidChk'];

        DB::beginTransaction();
        try {
            $finalError = array(
                'gl_amount_not_matching' => array(),
                'already_exist' => array(),
                'more_booked' => array(),
            );

            $finalError_ap = array(
                'advance_payment_paid' => array(),
            );

            $error_count = 0;
            $error_count_ap = 0;

            foreach ($input['detailTable'] as $item) {
 
                if ($item['isChecked']) {
                    if ($isAdvancePaymentPaidChk) { // check advance payment already paid for the PO
                        if ($item['addedDocumentSystemID'] == 11) {
              
                                $invoiceDet = BookInvSuppDet::where('bookingSuppMasInvAutoID', $item['bookingInvSystemCode'])->groupBy('purchaseOrderID')->get();
                           
                                if (count($invoiceDet) > 0) {
                                    foreach ($invoiceDet as $val) {
                                        $chkRequestedAdvancePayment = PoAdvancePayment::where('poID', $val->purchaseOrderID)->groupBy('poID')->first();
                                        if ($chkRequestedAdvancePayment) {
                                            $chkPaidAdvancePayment = AdvancePaymentDetails::selectRaw('erp_advancepaymentdetails.purchaseOrderID, Sum(erp_advancepaymentdetails.paymentAmount) AS SumOfpaymentAmount, Sum(erp_advancepaymentdetails.supplierTransAmount) AS SumOfsupplierTransAmount, Sum(erp_advancepaymentdetails.localAmount) AS SumOflocalAmount, Sum(erp_advancepaymentdetails.comRptAmount) AS SumOfcomRptAmount,supplierTransCurrencyID')->with(['supplier_currency', 'purchaseorder_by'])->where('purchaseOrderID', $chkRequestedAdvancePayment->poID)->whereNotNull('erp_advancepaymentdetails.purchaseOrderID')->groupBy('erp_advancepaymentdetails.purchaseOrderID')->first();
                                            if (!empty($chkPaidAdvancePayment)) {
                                                $currencyCode = $chkPaidAdvancePayment->supplier_currency ? $chkPaidAdvancePayment->supplier_currency->CurrencyCode : '';
                                                $decimalPl = $chkPaidAdvancePayment->supplier_currency ? $chkPaidAdvancePayment->supplier_currency->DecimalPlaces : 0;
                                                $poCode = $chkPaidAdvancePayment->purchaseorder_by ? $chkPaidAdvancePayment->purchaseorder_by->purchaseOrderCode : '';
                                                array_push($finalError_ap['advance_payment_paid'], 'Please note that an advance payment of ' . $currencyCode . ' ' . number_format($chkPaidAdvancePayment->SumOfpaymentAmount, $decimalPl) . ' is paid for this supplier for the selected Purchase Order ' . $poCode);
                                                $error_count_ap++;
                                            }
    
                                        }
                                    }
                                }
                            
                        }
                    }
                    $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $item['addedDocumentSystemID'])->where('companySystemID', $item['companySystemID'])->where('documentSystemCode', $item['bookingInvSystemCode'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();
                    if ($glCheck) {
                        if (round($glCheck->SumOfdocumentLocalAmount) != 0 || round($glCheck->SumOfdocumentRptAmount) != 0) {
                            array_push($finalError['gl_amount_not_matching'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['gl_amount_not_matching'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                        $error_count++;
                    }

                    $payDetailExistSameItem = PaySupplierInvoiceDetail::where('PayMasterAutoId', $input["PayMasterAutoId"])
                        ->when(($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->whereIn('invoiceType',[6,7]);
                            });
                        })
                        ->when(($payMaster->invoiceType != 6 && $payMaster->invoiceType != 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->where(function($query) {
                                    $query->where('invoiceType', '!=', 6)
                                          ->where('invoiceType', '!=', 7);
                                });
                            });
                        })
                        ->where('apAutoID', $item['apAutoID'])
                        ->first();

                    if ($payDetailExistSameItem) {
                        array_push($finalError['already_exist'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                        $error_count++;
                    }

                    $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                        ->when(($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->whereIn('invoiceType',[6,7]);
                            });
                        })
                        ->when(($payMaster->invoiceType != 6 && $payMaster->invoiceType != 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->where(function($query) {
                                    $query->where('invoiceType', '!=', 6)
                                          ->where('invoiceType', '!=', 7);
                                });
                            });
                        })
                        ->where('apAutoID', $item['apAutoID'])
                        ->first();

                    if ($item['addedDocumentSystemID'] == 11) {
                        //supplier invoice
                        if ($payDetailMoreBooked->supplierPaymentAmount > $item['supplierInvoiceAmount']) {
                            array_push($finalError['more_booked'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                            $error_count++;
                        }
                    } else if ($item['addedDocumentSystemID'] == 15) {
                        //debit note
                        if ($payDetailMoreBooked->supplierPaymentAmount < $item['supplierInvoiceAmount']) {
                            array_push($finalError['more_booked'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                            $error_count++;
                        }
                    }
                }
            }

            $confirm_error = array('type' => 'advance_payment_paid', 'data' => $finalError_ap);
            if ($error_count_ap > 0) {
                return $this->sendError("Error. Please check again.", 500, $confirm_error);
            }

            $confirm_error = array('type' => 'gl_amount_not_matching', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("Error. Please check again.", 500, $confirm_error);
            }

            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    if(isset($new['timeStamp'])) {
                        unset($new['timeStamp'],$new['unit'],$new['vat_sub_category'],$new['created_at'], $new['updated_at']);
                    }
                    $tempArray = $new;
                    $tempArray["supplierPaymentCurrencyID"] = $payMaster["BPVbankCurrency"];
                    $tempArray["supplierPaymentER"] = $payMaster["BPVbankCurrencyER"];
                    $tempArray["paymentSupplierDefaultAmount"] = 0;
                    $tempArray["paymentLocalAmount"] = 0;
                    $tempArray["paymentComRptAmount"] = 0;
                    $tempArray["supplierPaymentAmount"] = 0;
                    $tempArray["PayMasterAutoId"] = $input["PayMasterAutoId"];
                    $tempArray['createdPcID'] = gethostname();
                    $tempArray['createdUserID'] = $user->employee['empID'];
                    $tempArray['createdUserSystemID'] = $user->employee['employeeSystemID'];
                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);
                    if ($tempArray) {
                        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($tempArray);
                        $updatePayment = AccountsPayableLedger::find($new['apAutoID'])
                            ->update(['selectedToPaymentInv' => -1]);
                    }
                }
            }
            DB::commit();
            return $this->sendResponse('', 'Payment details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function addEmployeePaymentDetail($user, $payMaster, $input)
    {
        $isAdvancePaymentPaidChk = $input['isAdvancePaymentPaidChk'];

        DB::beginTransaction();
        try {
            $finalError = array(
                'gl_amount_not_matching' => array(),
                'already_exist' => array(),
                'more_booked' => array(),
            );

            $finalError_ap = array(
                'advance_payment_paid' => array(),
            );

            $error_count = 0;
            $error_count_ap = 0;
            foreach ($input['detailTable'] as $item) {
                if ($item['isChecked']) {
                    $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $item['addedDocumentSystemID'])->where('companySystemID', $item['companySystemID'])->where('documentSystemCode', $item['bookingInvSystemCode'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                    if ($glCheck) {
                        if (round($glCheck->SumOfdocumentLocalAmount) != 0 || round($glCheck->SumOfdocumentRptAmount) != 0) {
                            array_push($finalError['gl_amount_not_matching'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['gl_amount_not_matching'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                        $error_count++;
                    }


                    $payDetailExistSameItem = PaySupplierInvoiceDetail::where('PayMasterAutoId', $input["PayMasterAutoId"])
                        ->when(($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->whereIn('invoiceType',[6,7]);
                            });
                        })
                        ->when(($payMaster->invoiceType != 6 && $payMaster->invoiceType != 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->where(function($query) {
                                    $query->where('invoiceType', '!=', 6)
                                          ->where('invoiceType', '!=', 7);
                                });
                            });
                        })
                        ->where('apAutoID', $item['id'])
                        ->first();

                    if ($payDetailExistSameItem) {
                        array_push($finalError['already_exist'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                        $error_count++;
                    }

                    $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                        ->where('apAutoID', $item['id'])
                        ->when(($payMaster->invoiceType == 6 || $payMaster->invoiceType == 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->whereIn('invoiceType',[6,7]);
                            });
                        })
                        ->when(($payMaster->invoiceType != 6 && $payMaster->invoiceType != 7), function($query) {
                            $query->whereHas('payment_master', function($query) {
                                $query->where(function($query) {
                                    $query->where('invoiceType', '!=', 6)
                                          ->where('invoiceType', '!=', 7);
                                });
                            });
                        })
                        ->first();

                    if ($item['addedDocumentSystemID'] == 11) {
                        //supplier invoice
                        if ($payDetailMoreBooked->supplierPaymentAmount > $item['supplierInvoiceAmount']) {
                            array_push($finalError['more_booked'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                            $error_count++;
                        }
                    } else if ($item['addedDocumentSystemID'] == 15) {
                        //debit note
                        if ($payDetailMoreBooked->supplierPaymentAmount < $item['supplierInvoiceAmount']) {
                            array_push($finalError['more_booked'], $item['addedDocumentID'] . ' | ' . $item['bookingInvDocCode']);
                            $error_count++;
                        }
                    }
                }
            }

            $confirm_error = array('type' => 'gl_amount_not_matching', 'data' => $finalError);
            if ($error_count > 0) {
                return ['status' => false, 'message' => $confirm_error];
            }

            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    $tempArray = $new;
                    $tempArray["apAutoID"] = $new["id"];
                    $tempArray["supplierPaymentCurrencyID"] = $payMaster["BPVbankCurrency"];
                    $tempArray["supplierPaymentER"] = $payMaster["BPVbankCurrencyER"];
                    $tempArray["paymentSupplierDefaultAmount"] = 0;
                    $tempArray["paymentLocalAmount"] = 0;
                    $tempArray["paymentComRptAmount"] = 0;
                    $tempArray["supplierPaymentAmount"] = 0;
                    $tempArray["PayMasterAutoId"] = $input["PayMasterAutoId"];
                    $tempArray['createdPcID'] = gethostname();
                    $tempArray['createdUserID'] = $user->employee['empID'];
                    $tempArray['createdUserSystemID'] = $user->employee['employeeSystemID'];
                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);
                    if ($tempArray) {
                        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($tempArray);
                        $updatePayment = EmployeeLedger::find($new['id'])
                            ->update(['selectedToPaymentInv' => -1]);
                    }
                }
            }
            DB::commit();
            return ['status' => true]; 
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => false, 'message' => $exception->getMessage()]; 
        }
    }

    function getPOPaymentDetails(Request $request)
    {
        $input = $request->all();
        $supplierPaymentVouchers = PaySupplierInvoiceDetail::with(['pomaster'])->where('PayMasterAutoId', $input['payMasterAutoId'])->where('matchingDocID', 0)->get();

        $bankChargeAndOthers = PaymentVoucherBankChargeDetails::where('payMasterAutoID',$input['payMasterAutoId'])->get();

        $finalData = [
            'supplierPaymentVoucher' => $supplierPaymentVouchers,
            'bankChargeAndOthers' => $bankChargeAndOthers
        ];
        return $this->sendResponse($finalData, 'Payment details saved successfully');
    }

    function getMatchingPaymentDetails(Request $request)
    {
        $data = PaySupplierInvoiceDetail::with(['pomaster'])->where('matchingDocID', $request->matchDocumentMasterAutoID)
            ->get();
        return $this->sendResponse($data, 'Payment details saved successfully');
    }

    public function addPaymentVoucherMatchingPaymentDetail(Request $request)
    {
        $input = $request->all();
        
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $matchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);
        $isPVHasVAT = false;

        if(PaySupplierInvoiceMaster::find($matchDocumentMasterData->PayMasterAutoId)->applyVAT)
        {
            $isPVHasVAT = true;
        }

        $user_type = $matchDocumentMasterData->user_type;

        if (empty($matchDocumentMasterData)) {
            return $this->sendError('Matching not found');
        }

        if ($matchDocumentMasterData->matchingConfirmedYN) {
            return $this->sendError('You cannot add detail, this document already confirmed', 500);
        }

        $itemExistArray = array();
        $supplierInvoiceWithoutVAT = [];
        $supplierInvoiceAlreadyAdded = [];

        //check supplier invoice all ready exist
        foreach ($input['detailTable'] as $itemExist) {
            if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {
                $siDetailExistPS = PaySupplierInvoiceDetail::where('matchingDocID', $matchDocumentMasterAutoID)
                    ->where('companySystemID', $itemExist['companySystemID'])
                    ->where('bookingInvSystemCode', $itemExist['bookingInvSystemCode'])
                    ->first();

                if (!empty($siDetailExistPS)) {
                    array_push($supplierInvoiceAlreadyAdded,"<li>".$itemExist['bookingInvDocCode']."</li>");
                }
                
                // check supplier invoice has VAT
                $allRecordsHaveVAT = false;
                $supplierInvoiceMaster  = BookInvSuppMaster::find($itemExist['bookingInvSystemCode']);
                if(!empty($supplierInvoiceMaster->directdetail))
                {
                    $allRecordsHaveVAT = $supplierInvoiceMaster->directdetail->pluck('VATAmount')->every(function ($vatAmount) {
                        return $vatAmount > 0;
                    });
                }


                if(!$allRecordsHaveVAT && ($isPVHasVAT)) {
                    array_push($supplierInvoiceWithoutVAT,"<li>".$itemExist['bookingInvDocCode']."</li>");
                }
            }
        }

        if (!empty($supplierInvoiceWithoutVAT)) {
            return $this->sendError("The supplier invoice without VAT you cannot be matched with a payment voucher that includes VAT <br/>. <ul style='list-style:none;'>".implode('',$supplierInvoiceWithoutVAT)."</ul>", 422);
        }

        if (!empty($supplierInvoiceAlreadyAdded)) {
            return $this->sendError("Selected Invoice is already added. Please check again </br>. <ul style='list-style:none'>".implode('',$supplierInvoiceAlreadyAdded)."</ul>", 422);
        }

        $notUpdatedInGL = [];

        //check record total in General Ledger table
        foreach ($input['detailTable'] as $itemExist) {

            if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $itemExist['addedDocumentSystemID'])->where('companySystemID', $itemExist['companySystemID'])->where('documentSystemCode', $itemExist['bookingInvSystemCode'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                        $itemDrt = "Selected Invoice " . $itemExist['bookingInvDocCode'] . " is not updated in general ledger. Please check again";
                        array_push($notUpdatedInGL,$itemExist['bookingInvDocCode']);
                    }
                } else {
                    $itemDrt = "Selected Invoice " . $itemExist['bookingInvDocCode'] . " is not updated in general ledger. Please check again";
                    array_push($notUpdatedInGL,$itemExist['bookingInvDocCode']);
                }
            }
        }

        if (!empty($notUpdatedInGL)) {
            return $this->sendError("Selected Invoice is not updated in general ledger. Please check again </br>. <ul style='list-style:none'>".implode('',$notUpdatedInGL)."</ul>", 422);
        }

        $moreThanBookingInvoiceAmount = [];

        foreach ($input['detailTable'] as $item) {
            if (isset($item['isChecked']) && $item['isChecked']) {
               
                if($user_type == 1)
                {
                    $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                    ->where('apAutoID', $item['apAutoID'])
                    ->whereHas('matching_master',function($query){
                        $query->where('user_type',1);
                     })
                    ->first();
                }
                else
                {
                    $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                    ->where('apAutoID', $item['id'])
                    ->whereHas('matching_master',function($query){
                        $query->where('user_type',2);
                     })
                    ->first();

                    
                }
      
               
                if ($item['addedDocumentSystemID'] == 11) {
                    //supplier invoice
                    if ($payDetailMoreBooked->supplierPaymentAmount > $item['supplierInvoiceAmount']) {

                        array_push($moreThanBookingInvoiceAmount,$item['bookingInvDocCode']);

                    }
                }
            }
        }

        if (!empty($moreThanBookingInvoiceAmount)) {
            return $this->sendError("Selected Invoice booked more than the invoice amount. </br>. <ul style='list-style:none'>".implode('',$moreThanBookingInvoiceAmount)."</ul>", 422);
        }


        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    $tempArray = $new;
                    if($user_type == 2)
                    {
                        $tempArray["apAutoID"] = $new['id'];
                        $tempArray["supplierCodeSystem"] = $new['employeeSystemID'];
                    }
                    $tempArray["supplierPaymentCurrencyID"] = $new['supplierTransCurrencyID'];
                    $tempArray["supplierPaymentER"] = $new['supplierTransER'];
                    $tempArray["paymentSupplierDefaultAmount"] = 0;
                    $tempArray["paymentLocalAmount"] = 0;
                    $tempArray["paymentComRptAmount"] = 0;
                    $tempArray["supplierPaymentAmount"] = 0;
                    $tempArray["PayMasterAutoId"] = $matchDocumentMasterData->PayMasterAutoId;
                    $tempArray["matchingDocID"] = $matchDocumentMasterAutoID;

                    $tempArray["documentID"] = $matchDocumentMasterData->documentID;
                    $tempArray["documentSystemID"] = $matchDocumentMasterData->documentSystemID;

                    $tempArray['createdPcID'] = gethostname();
                    $tempArray['createdUserID'] = $user->employee['empID'];
                    $tempArray['createdUserSystemID'] = $user->employee['employeeSystemID'];

                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);

                    if ($matchDocumentMasterData->documentSystemID == 4 && $matchDocumentMasterData->PayMasterAutoId > 0) {
                        $pvMasterData = PaySupplierInvoiceMaster::find($matchDocumentMasterData->PayMasterAutoId);

                        if ($pvMasterData->invoiceType == 5 && $pvMasterData->applyVAT == 1) {
                            $advancePaymentVATAmount = AdvancePaymentDetails::where('PayMasterAutoId', $matchDocumentMasterData->PayMasterAutoId)
                                                                            ->sum('VATAmount');

                            if ($advancePaymentVATAmount > 0) {
                                $supplierInvoice = BookInvSuppMaster::find($tempArray['bookingInvSystemCode']);

                                if ($supplierInvoice && $supplierInvoice->documentType == 0) {
                                    $checkVATTypeOfSI = SupplierInvoiceItemDetail::select('vatMasterCategoryID', 'vatSubCategoryID')
                                                                    ->where('bookingSuppMasInvAutoID', $tempArray["bookingInvSystemCode"])
                                                                    ->whereNotNull('vatMasterCategoryID')
                                                                    ->whereNotNull('vatSubCategoryID')
                                                                    ->groupBy('vatMasterCategoryID', 'vatSubCategoryID')
                                                                    ->get();

                                    
                                } else if ($supplierInvoice && $supplierInvoice->documentType == 1) {
                                    $checkVATTypeOfSI = DirectInvoiceDetails::select('vatMasterCategoryID', 'vatSubCategoryID')
                                                                    ->where('directInvoiceAutoID', $tempArray["bookingInvSystemCode"])
                                                                    ->whereNotNull('vatMasterCategoryID')
                                                                    ->whereNotNull('vatSubCategoryID')
                                                                    ->groupBy('vatMasterCategoryID', 'vatSubCategoryID')
                                                                    ->get();
                                }

                                if (isset($checkVATTypeOfSI) && count($checkVATTypeOfSI) == 1) {
                                    $vatCategoryData = collect($checkVATTypeOfSI)->first();

                                    $tempArray["vatMasterCategoryID"] = $vatCategoryData->vatMasterCategoryID;
                                    $tempArray["vatSubCategoryID"] = $vatCategoryData->vatSubCategoryID;
                                } else if (isset($checkVATTypeOfSI) && count($checkVATTypeOfSI) > 1) {
                                    $companySystemID = $tempArray['companySystemID'];
                                    $defaultVAT = TaxService::getDefaultVAT($tempArray['companySystemID']);

                                    if ($defaultVAT) {
                                        $tempArray['vatSubCategoryID'] = $defaultVAT['vatSubCategoryID'];
                                        $tempArray['vatMasterCategoryID'] = $defaultVAT['vatMasterCategoryID'];
                                    }
                                }
                            }
                        }
                    }

                    if ($tempArray) {
                        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($tempArray);
                       
                        if($user_type == 1)
                        {
                            $updatePayment = AccountsPayableLedger::find($new['apAutoID'])
                            ->update(['selectedToPaymentInv' => -1]);
                        }
                        else
                        {
                            $updatePayment = EmployeeLedger::find($new['id'])
                            ->update(['selectedToPaymentInv' => -1]);
                        }
                       
                    }
                }
            }
            DB::commit();
            return $this->sendResponse('', 'Payment details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError("Error Occurred");
        }

    }

    public function updatePaymentVoucherMatchingDetail(Request $request)
    {
        $input = $request->all();
        
        
        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($input['payDetailAutoID']);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }
        
        $matchDocumentMasterData = MatchDocumentMaster::find($input['matchingDocID']);
        if (empty($matchDocumentMasterData)) {
            return $this->sendError('Matching document not found');
        }

        $user_type = $matchDocumentMasterData->user_type;
        if ($matchDocumentMasterData->matchingConfirmedYN) {
            return $this->sendError('You cannot update the detail, this document already confirmed', 500);
        }

        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($matchDocumentMasterData->supplierTransCurrencyID);

        

        if ($input['supplierPaymentAmount'] > $input['paymentBalancedAmount']) {
            return $this->sendError('Matching amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch']);
        }
        
        //calculate the total
        $existTotal = 0;
        $detailAmountTot = PaySupplierInvoiceDetail::where('matchingDocID', $input['matchingDocID'])
            ->where('payDetailAutoID', '<>', $input['payDetailAutoID'])
            ->sum('supplierPaymentAmount');
        $input['supplierPaymentAmount'] = isset($input['supplierPaymentAmount']) ?  \Helper::stringToFloat($input['supplierPaymentAmount']) : 0;
        $existTotal = $detailAmountTot + $input['supplierPaymentAmount'];
        $currencyDecimal = CurrencyMaster::where('currencyID',$matchDocumentMasterData->supplierTransCurrencyID)->select('DecimalPlaces')->first();
        $matchAmount = round($matchDocumentMasterData->matchBalanceAmount,$currencyDecimal->DecimalPlaces);
        if (($existTotal - $matchAmount) > 0.00001) {
            return $this->sendError('Matching amount total cannot be greater than balance amount to match', 500, ['type' => 'amountmismatch']);
        }

        if ($input['supplierPaymentAmount'] == "") {
            $input['supplierPaymentAmount'] = 0;
        }
       


        if($user_type == 2)
        {
            $supplierPaidAmountSumPayment = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                                                        ->whereHas('payment_master', function($query) {
                                                                                $query->whereIn('invoiceType',[6,7]);
                                                                         })->where('apAutoID', $input["apAutoID"])
                                                                         ->where(function($query){
                                                                            $query->where('documentSystemID', '=', NULL)
                                                                                    ->orWhere('documentSystemID', '=', 4);
                                                                         })
                                                                        ->where('payDetailAutoID', '<>', $input["payDetailAutoID"])
                                                                        ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                                                        ->first();


            $supplierPaidAmountSumDebit = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                                                    ->whereHas('debite_note', function($query) {
                                                                              $query->where('type',2);
                                                                      })->where('apAutoID', $input["apAutoID"])
                                                                      ->where('documentSystemID', '=', 15)
                                                                      ->where('payDetailAutoID', '<>', $input['payDetailAutoID'])
                                                                      ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                                                      ->first();  


              $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = $supplierPaidAmountSumPayment["SumOfsupplierPaymentAmount"] + $supplierPaidAmountSumDebit["SumOfsupplierPaymentAmount"];


        }
        else if($user_type == 1)
        {
            $supplierPaidAmountSumPayment = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
            ->whereHas('payment_master', function($query) {
                    $query->where(function ($query) {
                        $query->where('invoiceType','!=',6)
                              ->where('invoiceType','!=',7);
                    });
             })->where('apAutoID', $input["apAutoID"])
             ->where(function($query){
                $query->where('documentSystemID', '=', NULL)
                        ->orWhere('documentSystemID', '=', 4);
             })
            ->where('payDetailAutoID', '<>', $input["payDetailAutoID"])
            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
            ->first();


            $supplierPaidAmountSumDebit = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
            ->whereHas('debite_note', function($query) {
                      $query->where('type',1);
              })->where('apAutoID', $input["apAutoID"])
              ->where('documentSystemID', '=', 15)
              ->where('payDetailAutoID', '<>', $input['payDetailAutoID'])
              ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
              ->first();  



            $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = $supplierPaidAmountSumPayment["SumOfsupplierPaymentAmount"] + $supplierPaidAmountSumDebit["SumOfsupplierPaymentAmount"];
           
        }
        else
        {

            $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
            ->where('apAutoID', $input["apAutoID"])
            ->where('payDetailAutoID', '<>', $input['payDetailAutoID'])
            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();
        }



        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input["bookingInvSystemCode"])->where('documentSystemID', $input["addedDocumentSystemID"])->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

        $currentPayAmount = $paySupplierInvoiceDetail->supplierPaymentAmount + $input['supplierPaymentAmount'];

        $machAmount = 0;
        if ($matchedAmount) {
            $machAmount = $matchedAmount["SumOfmatchedAmount"];
        }

        if ($input['temptype'] == 1) {
            $input['supplierPaymentAmount'] = $input['paymentBalancedAmount'];
        }

        if (!$supplierPaidAmountSum) {
            $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = 0;
        }
       
        $paymentBalancedAmount = $paySupplierInvoiceDetail->supplierInvoiceAmount - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));

    
       
      

        if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {
            //supplier invoice
            if (($input["supplierPaymentAmount"] - $paymentBalancedAmount) > 0.00001) {
                return $this->sendError('Payment amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch', 'amount' => $paymentBalancedAmount]);
            }
        } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {
            //debit note
            if (($paymentBalancedAmount - $input["supplierPaymentAmount"]) > 0.00001) {
                return $this->sendError('Payment amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch', 'amount' => $paymentBalancedAmount]);
            }
        }
       
        $paymentBalancedAmount = $paymentBalancedAmount - $input["supplierPaymentAmount"];


        $input["paymentBalancedAmount"] = \Helper::roundValue($paymentBalancedAmount);

        $conversionAmount = \Helper::convertAmountToLocalRpt(4, $input["payDetailAutoID"], ABS($input["supplierPaymentAmount"]));
        $input["paymentSupplierDefaultAmount"] = \Helper::roundValue($conversionAmount["defaultAmount"]);
        $input["paymentLocalAmount"] = \Helper::roundValue($conversionAmount["localAmount"]);
        $input["paymentComRptAmount"] = \Helper::roundValue($conversionAmount["reportingAmount"]);

        unset($input['pomaster']);

        if ($matchDocumentMasterData->documentSystemID == 4 && $matchDocumentMasterData->PayMasterAutoId > 0) {
            $pvMasterData = PaySupplierInvoiceMaster::find($matchDocumentMasterData->PayMasterAutoId);

            if ($pvMasterData->invoiceType == 5 && $pvMasterData->applyVAT == 1) {
                $advancePaymentVATAmount = AdvancePaymentDetails::where('PayMasterAutoId', $matchDocumentMasterData->PayMasterAutoId)
                                                                ->sum('VATAmount');


                if ($advancePaymentVATAmount > 0) {
                    $supplierInvoiceVAT = 0;
                    $supplierInvoiceVATLocal = 0;
                    $supplierInvoiceVATRpt = 0;
                    $supplierInvoice = BookInvSuppMaster::find($input['bookingInvSystemCode']);

                    if ($supplierInvoice && $supplierInvoice->documentType == 0) {
                        $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($input['bookingInvSystemCode']);
                        $totalVATAmount = isset($vatDetails['totalVAT']) ? $vatDetails['totalVAT'] : 0;
                        $totalVATAmountLocal = isset($vatDetails['totalVATLocal']) ? $vatDetails['totalVATLocal'] : 0;
                        $totalVATAmountRpt = isset($vatDetails['totalVATRpt']) ? $vatDetails['totalVATRpt'] : 0;
                    
                        $supplierInvoiceVAT = (($totalVATAmount / $input['supplierInvoiceAmount']) * $input['supplierPaymentAmount']);
                        $supplierInvoiceVATLocal = (($totalVATAmountLocal / $input['localAmount']) * $input['paymentLocalAmount']);
                        $supplierInvoiceVATRpt = (($totalVATAmountRpt / $input['comRptAmount']) * $input['paymentComRptAmount']);
                        
                    } else if ($supplierInvoice && $supplierInvoice->documentType == 1) {
                        $supplierInvoiceVAT = (($supplierInvoice->VATAmount / $input['supplierInvoiceAmount']) * $input['supplierPaymentAmount']);
                        $supplierInvoiceVATLocal = (($supplierInvoice->VATAmountLocal / $input['localAmount']) * $input['paymentLocalAmount']);
                        $supplierInvoiceVATRpt = (($supplierInvoice->VATAmountRpt / $input['comRptAmount']) * $input['paymentComRptAmount']);
                    }

                    $input['VATAmount'] = $supplierInvoiceVAT;
                    $input['VATAmountLocal'] = $supplierInvoiceVATLocal;
                    $input['VATAmountRpt'] = $supplierInvoiceVATRpt;
                    $input['VATPercentage'] = ($input['supplierPaymentAmount'] > 0) ? (($supplierInvoiceVAT / $input['supplierPaymentAmount']) * 100) : 0;

                }
            }
        }

        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->update($input, $input['payDetailAutoID']);

        if($user_type == 2)
        {
            $supplierPaidAmountSumPayment = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
            ->whereHas('payment_master', function($query) {
                    $query->whereIn('invoiceType',[6,7]);
             })->where('apAutoID', $input["apAutoID"])
             ->where(function($query){
                $query->where('documentSystemID', '=', NULL)
                        ->orWhere('documentSystemID', '=', 4);
             })
            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
            ->first();


            $supplierPaidAmountSumDebit = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
             ->whereHas('debite_note', function($query) {
                      $query->where('type',2);
              })->where('apAutoID', $input["apAutoID"])
              ->where('documentSystemID', '=', 15)
              ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
              ->first();  

              


            $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = $supplierPaidAmountSumPayment["SumOfsupplierPaymentAmount"] + $supplierPaidAmountSumDebit["SumOfsupplierPaymentAmount"];


        }
        else if($user_type == 1)
        {
            $supplierPaidAmountSumPayment = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
            ->whereHas('payment_master', function($query) {
                    $query->where(function ($query) {
                        $query->where('invoiceType','!=',6)
                              ->where('invoiceType','!=',7);
                    });
             })->where('apAutoID', $input["apAutoID"])
             ->where(function($query){
                $query->where('documentSystemID', '=', NULL)
                        ->orWhere('documentSystemID', '=', 4);
             })
            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
            ->first();


            $supplierPaidAmountSumDebit = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
             ->whereHas('debite_note', function($query) {
                      $query->where('type',1);
              })->where('apAutoID', $input["apAutoID"])
              ->where('documentSystemID', '=', 15)
              ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
              ->first();  

              


            $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = $supplierPaidAmountSumPayment["SumOfsupplierPaymentAmount"] + $supplierPaidAmountSumDebit["SumOfsupplierPaymentAmount"];
        }
        else
        {
            $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
            ->where('apAutoID', $input["apAutoID"])
            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

        }
       

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input["bookingInvSystemCode"])->where('documentSystemID', $input["addedDocumentSystemID"])->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

        $machAmount = 0;
        if ($matchedAmount) {
            $machAmount = $matchedAmount["SumOfmatchedAmount"];
        }

        $paymentBalancedAmount = \Helper::roundValue($paySupplierInvoiceDetail->supplierInvoiceAmount - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1)));

        if (!$supplierPaidAmountSum) {
            $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = 0;
        }

        $totalPaidAmount = ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));
    
        if ($paySupplierInvoiceDetail->addedDocumentSystemID == 11) {

            if($user_type == 2)
            {
                if ($totalPaidAmount == 0) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $paySupplierInvoiceDetail->supplierInvoiceAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => -1]);
                } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                }
            }
            else
            {
                if ($totalPaidAmount == 0) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $paySupplierInvoiceDetail->supplierInvoiceAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => -1]);
                } else if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                }
            }

         
        } else if ($paySupplierInvoiceDetail->addedDocumentSystemID == 15 || $paySupplierInvoiceDetail->addedDocumentSystemID == 24) {

            if($user_type == 2)
            {
                if ($totalPaidAmount == 0) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                    $updatePayment = EmployeeLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                }
            }
            else
            {
                if ($totalPaidAmount == 0) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 0]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $totalPaidAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount < $totalPaidAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 1]);
                } else if ($paySupplierInvoiceDetail->supplierInvoiceAmount > $totalPaidAmount) {
                    $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                        ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                }
            }


  
        }
        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'PaySupplierInvoiceDetail updated successfully');
    }

    public function storePaymentVoucherBankChargeDetails(Request $request)
    {
        $input = $request->all();

        $messages = [
            'companySystemID.required' => 'Company is required.',
            'payMasterAutoID.required' => 'ID is required.',
            'glCode.required' => 'GL Account is required.',
        ];

        $validator = \Validator::make($request->all(), [
            'companySystemID' => 'required|numeric|min:1',
            'payMasterAutoID' => 'required|numeric|min:1',
            'glCode' => 'required|numeric|min:1'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companySystemID = $input['companySystemID'];
        $glCode = $input['glCode'] ?? 0;
        $payMasterAutoID = $input['payMasterAutoID'];

        $master = PaySupplierInvoiceMaster::where('PayMasterAutoId', $payMasterAutoID)->first();

        if(empty($master)){
            return $this->sendError('Payment Voucher not found.');
        }

        if($master->confirmedYN){
            return $this->sendError('You cannot add detail, this document already confirmed', 500);
        }

        $company = Company::where('companySystemID', $companySystemID)->first();

        if($glCode){
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID', 'controlAccounts')
                ->where('chartOfAccountSystemID', $glCode)
                ->first();

            $inputData['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
            $inputData['glCode'] = $chartOfAccount->AccountCode;
            $inputData['glCodeDescription'] = $chartOfAccount->AccountDescription;
        }

        $inputData['payMasterAutoID'] = $payMasterAutoID;
        $inputData['companyID'] = $company->CompanyID;
        $inputData['companySystemID'] = $companySystemID;

        $inputData['dpAmountCurrency'] = $master->supplierTransCurrencyID;
        $inputData['dpAmountCurrencyER'] = $master->supplierTransCurrencyER;
        $inputData['dpAmount'] = 0;
        $inputData['localCurrency'] = $master->localCurrencyID;
        $inputData['localCurrencyER'] = $master->localCurrencyER;
        $inputData['localAmount'] = 0;
        $inputData['comRptCurrency'] = $master->companyRptCurrencyID;
        $inputData['comRptCurrencyER'] = $master->companyRptCurrencyER;
        $inputData['comRptAmount'] = 0;

        DB::beginTransaction();

        try {
            PaymentVoucherBankChargeDetails::create($inputData);
            DB::commit();
            return $this->sendResponse(null, 'successfully created');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getLine());
        }

    }

    public function updatePaymentVoucherBankChargeDetails(request $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);
        $id = $input['id'];

        $input = array_except($input, ['id']);

        $detail = PaymentVoucherBankChargeDetails::where('id', $id)->first();

        if (empty($detail)) {
            return $this->sendError('Payment voucher bank detail not found', 500);
        }

        $master = PaySupplierInvoiceMaster::where('PayMasterAutoId', $detail->payMasterAutoID)->first();

        if(empty($master)){
            return $this->sendError('Payment Voucher not found.');
        }

        if($master->confirmedYN){
            return $this->sendError('You cannot update detail, this document already confirmed', 500);
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }
        else if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID){
            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
        }

        $myCurr = $master->supplierTransCurrencyID;
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

        $input['dpAmountCurrency'] = $master->supplierTransCurrencyID;
        $input['dpAmountCurrencyER'] = $master->supplierTransCurrencyER;
        $totalAmount = $input['dpAmount'];
        $input['dpAmount'] = round($input['dpAmount'], $decimal);

        try {
            $currency = \Helper::convertAmountToLocalRpt(203, $detail->payMasterAutoID, $totalAmount);
            $input["comRptAmount"] = \Helper::roundValue($currency['reportingAmount']);
            $input["localAmount"] = \Helper::roundValue($currency['localAmount']);

            DB::beginTransaction();

            PaymentVoucherBankChargeDetails::where('id', $id)->update($input);

            DB::commit();
            return $this->sendResponse('s', 'successfully updated');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getMessage());
        }
    }

}
