<?php
/**
 * =============================================
 * -- File Name : PoPaymentTermsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Terms
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - April 2018
 * -- Description : This file contains the all CRUD for Po Payment Terms
 * -- REVISION HISTORY
 * -- Date: 20-April 2018 By: Nazir Description: Added new functions named as getProcumentOrderPaymentTerms(),
 * -- Date: 14-August 2018 By: Nazir Description: Added new functions named as updateAllPaymentTerms(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoPaymentTermsAPIRequest;
use App\Http\Requests\API\UpdatePoPaymentTermsAPIRequest;
use App\Models\PoAddons;
use App\Models\PoPaymentTerms;
use App\Models\PaymentTermTemplateAssigned;
use App\Models\PaymentTermConfig;
use App\Models\PaymentTermTemplate;
use App\Models\SupplierMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Repositories\PoPaymentTermsRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PoAdvancePayment;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Class PoPaymentTermsController
 * @package App\Http\Controllers\API
 */
class PoPaymentTermsAPIController extends AppBaseController
{
    /** @var  PoPaymentTermsRepository */
    private $poPaymentTermsRepository;

    public function __construct(PoPaymentTermsRepository $poPaymentTermsRepo)
    {
        $this->poPaymentTermsRepository = $poPaymentTermsRepo;
    }

    /**
     * Display a listing of the PoPaymentTerms.
     * GET|HEAD /poPaymentTerms
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poPaymentTermsRepository->pushCriteria(new RequestCriteria($request));
        $this->poPaymentTermsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poPaymentTerms = $this->poPaymentTermsRepository->all();

        return $this->sendResponse($poPaymentTerms->toArray(), 'Po Payment Terms retrieved successfully');
    }

    /**
     * Store a newly created PoPaymentTerms in storage.
     * POST /poPaymentTerms
     *
     * @param CreatePoPaymentTermsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePoPaymentTermsAPIRequest $request)
    {
        $input = $request->all();
        $purchaseOrderID = $input['poID'];

        if (isset($input['comDate'])) {
            if ($input['comDate']) {
                $input['comDate'] = new Carbon($input['comDate']);
            }
        }

        if (isset($input['LCPaymentYNR'])) {
            $input['LCPaymentYN'] = $input['LCPaymentYNR'];
        }

        $prDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();

        if (empty($prDetailExist)) {
            return $this->sendError('At least one item should added to create payment term');
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        /*        $supplier = SupplierMaster::where('supplierCodeSystem', $purchaseOrder['supplierID'])->first();
                if ($supplier) {
                    $input['inDays'] = $supplier->creditPeriod;
                }*/
        $input['inDays'] = $purchaseOrder->creditPeriod;

        if (!empty($purchaseOrder->createdDateTime) && !empty($purchaseOrder->creditPeriod)) {
            $addedDate = strtotime("+$purchaseOrder->creditPeriod day", strtotime($purchaseOrder->createdDateTime));
            $input['comDate'] = date("Y-m-d", $addedDate);
        } else {
            $input['comDate'] = $purchaseOrder->createdDateTime;
        }

        if ($input['LCPaymentYN'] == 1) {
            $input['paymentTemDes'] = 'Payment In';
        } else if ($input['LCPaymentYN'] == 2) {
            $input['paymentTemDes'] = 'Advance Payment';
        }

        $poPaymentTerms = $this->poPaymentTermsRepository->create($input);

        return $this->sendResponse($poPaymentTerms->toArray(), 'Po Payment Terms saved successfully');
    }

    /**
     * Display the specified PoPaymentTerms.
     * GET|HEAD /poPaymentTerms/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PoPaymentTerms $poPaymentTerms */
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }

        return $this->sendResponse($poPaymentTerms->toArray(), 'Po Payment Terms retrieved successfully');
    }

    /**
     * Update the specified PoPaymentTerms in storage.
     * PUT/PATCH /poPaymentTerms/{id}
     *
     * @param  int $id
     * @param UpdatePoPaymentTermsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoPaymentTermsAPIRequest $request)
    {
        $input = $request->all();

        if (array_key_exists('advance_payment_request', $input)) {
            unset($input['advance_payment_request']);
        }

        $input = $this->convertArrayToValue($input);

        $purchaseOrderID = $input['poID'];

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }

        /*        $supplier = SupplierMaster::where('supplierCodeSystem', $purchaseOrder['supplierID'])->first();
                if ($supplier) {
                    $input['inDays'] = $supplier->creditPeriod;
                }*/
        $daysin = $input['inDays'];
        if ($purchaseOrder->documentSystemID == 5 && $purchaseOrder->poType_N == 5) {
            if (isset($input['comDate'])) {
                if ($input['comDate']) {
                    $input['comDate'] = new Carbon($input['comDate']);
                }
            }

            if ($poPaymentTerms->comDate != $input['comDate']) {
                $createdDate_format = strtotime($purchaseOrder->createdDateTime);
                $comDate_format = strtotime($input['comDate']);

                $datediff = $comDate_format - $createdDate_format;
                $calculatedIndays = round($datediff / (60 * 60 * 24));

                $input['inDays'] = $calculatedIndays;
                $addedDate = strtotime("+$calculatedIndays day", strtotime($purchaseOrder->createdDateTime));
                $input['comDate'] = date("Y-m-d", $addedDate);

            } else {

                $calculatedIndays = $input['inDays'];

                $addedDate = strtotime("+$calculatedIndays day", strtotime($purchaseOrder->createdDateTime));
                $input['comDate'] = date("Y-m-d", $addedDate);
            }

        } else {
            if (!empty($purchaseOrder->createdDateTime) && $daysin != 0) {
                $addedDate = strtotime("+$daysin day", strtotime($purchaseOrder->createdDateTime));
                $input['comDate'] = date("Y-m-d", $addedDate);
            }

            if (!empty($purchaseOrder->createdDateTime) && $daysin == 0) {
                $input['comDate'] = $purchaseOrder->createdDateTime;
            }
        }


        /** @var PoPaymentTerms $poPaymentTerms */
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }


        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();

        //$poMasterSumDeducted = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) + $purchaseOrder->VATAmount;

        //$calculatePer = ($input['comPercentage'] / 100) * $poMasterSumDeducted;
        //$input['comAmount'] = round($calculatePer, 8);

        $input['comAmount'] = ($input['poAmount'] / 100) * $input['comPercentage'];
        $poPaymentTerms = $this->poPaymentTermsRepository->update($input, $id);

        return $this->sendResponse($poPaymentTerms->toArray(), 'PoPaymentTerms updated successfully');
    }

    /**
     * Remove the specified PoPaymentTerms from storage.
     * DELETE /poPaymentTerms/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PoPaymentTerms $poPaymentTerms */
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);
        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }

        $poPaymentTerms->delete();

        $deleteAdvancePayment = PoAdvancePayment::where('poTermID', $id)->delete();

        return $this->sendResponse($id, 'Po Payment Terms deleted successfully');
    }

    public function getProcumentOrderPaymentTerms(Request $request)
    {
        $input = $request->all();

        $poAdvancePaymentType = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
            ->with(['advance_payment_request'])
            ->orderBy('paymentTermID', 'ASC')
            ->get();

        return $this->sendResponse($poAdvancePaymentType->toArray(), 'Data retrieved successfully');
    }

    public function getProcumentOrderPaymentTermConfigs(Request $request)
    {
        $input = $request->all();
        $purchaseOrderID = $input['purchaseOrderID'];
        $supplierID = $input['supplierID'];

        $assignedTemplateId = PaymentTermTemplateAssigned::where('supplierID', $supplierID)->value('templateID');
        $isActiveTemplate = PaymentTermTemplate::where('id', $assignedTemplateId)->value('isActive');

        $approvedPoConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)
            ->where(function ($query) {
                $query->where('isApproved', true)
                    ->orWhere('isRejected', true);
            })
            ->orderBy('sortOrder')->get();

        if ($approvedPoConfigs->isNotEmpty())
        {
            $purchaseOrderPaymentTermConfigs = $approvedPoConfigs;
        }
        else if ($assignedTemplateId != null && $isActiveTemplate)
        {
            $poAssignedTemplateConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $assignedTemplateId)->first();
            if (!$poAssignedTemplateConfigs) {
                $paymentTermConfigs = PaymentTermConfig::where('templateId', $assignedTemplateId)->get();
                $isDefaultAssign = false;
                $this->createProcumentOrderPaymentTermConfigs($assignedTemplateId, $purchaseOrderID, $supplierID, $paymentTermConfigs, $isDefaultAssign);
            }
            $purchaseOrderPaymentTermConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $assignedTemplateId)->orderBy('sortOrder')->get();
        } else
        {
            $poDefaultConfigUpdate = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('isDefaultAssign', true)->where('isConfigUpdate', true)->first();
            if ($poDefaultConfigUpdate) {
                $purchaseOrderPaymentTermConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $poDefaultConfigUpdate->templateID)
                    ->where('isDefaultAssign', true)->orderBy('sortOrder')->get();
            } else {
                $defaultTemplateID = PaymentTermTemplate::where('isDefault', true)->value('id');
                $poDefaultTemplateConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $defaultTemplateID)->first();
                if (!$poDefaultTemplateConfigs) {
                    $paymentTermConfigs = PaymentTermConfig::where('templateId', $defaultTemplateID)->get();
                    $isDefaultAssign = true;
                    $this->createProcumentOrderPaymentTermConfigs($defaultTemplateID, $purchaseOrderID, $supplierID, $paymentTermConfigs, $isDefaultAssign);
                }
                $purchaseOrderPaymentTermConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $defaultTemplateID)->orderBy('sortOrder')->get();
            }
        }

        return $this->sendResponse($purchaseOrderPaymentTermConfigs->toArray(), 'Payment terms and conditions retrieved successfully');
    }

    public function createProcumentOrderPaymentTermConfigs($templateID, $purchaseOrderID, $supplierID, $paymentTermConfigs, $isDefaultAssign) {
        foreach ($paymentTermConfigs as $paymentTermConfig) {
            DB::table('po_wise_payment_term_config')->insert([
                'templateID' => $templateID,
                'purchaseOrderID' => $purchaseOrderID,
                'supplierID' => $supplierID,
                'term' => $paymentTermConfig->term,
                'description' => $paymentTermConfig->description,
                'sortOrder' => $paymentTermConfig->sortOrder,
                'isSelected' => $paymentTermConfig->isSelected,
                'isDefaultAssign' => $isDefaultAssign
            ]);
        }
    }

    public function updateAllPaymentTerms(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];
        $discountAmount = isset($input['discount']) ? $input['discount'] : 0;
        $poDiscountPercentage = isset($input['poDiscountPercentage']) ? $input['poDiscountPercentage'] : 0;
        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchaseOrder->update(
            [
                'poDiscountAmount' => $discountAmount,
                'poDiscountPercentage' => $poDiscountPercentage
            ]
        );

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();

        //getting addon Total for PO
        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
            ->where('poId', $purchaseOrderID)
            ->first();

        $poAdvancePaymentType = PoPaymentTerms::where('poID', $purchaseOrderID)
            ->get();

        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($purchaseOrder->supplierTransactionCurrencyID);

        $orderAmount = $poMasterSum['masterTotalSum'] + $poAddonMasterSum['addonTotalSum'];
        $orderAmountRounded = round($orderAmount, $supplierCurrencyDecimalPlace);
        $vatAmount = $input['vat'];

        if($purchaseOrder->rcmActivated){
            $vatAmount = 0;
        }


        if (!empty($poAdvancePaymentType)) {
            foreach ($poAdvancePaymentType as $advance) {

                //calculation advance amount
                if($advance['comPercentage'] == 0  || $advance['comPercentage'] == "") {
                    $calculatePer = ($advance['comPercentage'] / 100) * (($orderAmount - $discountAmount + $vatAmount));
                    $roundedCalculatePer = round($calculatePer, $supplierCurrencyDecimalPlace);
                }else if ($advance['comAmount'] == 0  || $advance['comAmount'] == "") {
                    $calculatePer = ($advance['comPercentage'] / 100) * (($orderAmount - $discountAmount + $vatAmount));
                    $roundedCalculatePer = round($calculatePer, $supplierCurrencyDecimalPlace);
                }else {
                    $calculatePer = $advance['comAmount'];
                    $roundedCalculatePer = round($calculatePer, $supplierCurrencyDecimalPlace);
                }


                //update payment terms table
                $paymentTermUpdate = PoPaymentTerms::find($advance['paymentTermID']);
                $paymentTermUpdate->comAmount = $calculatePer;
                $paymentTermUpdate->save();

                $PoAdvancePaymentFetch = PoAdvancePayment::where('poTermID', $advance['paymentTermID'])
                    ->where('poID', $purchaseOrderID)
                    ->first();

                if (!empty($PoAdvancePaymentFetch)) {

                    //update advance payment terms table
                    $advancePaymentTermUpdate = PoAdvancePayment::find($PoAdvancePaymentFetch->poAdvPaymentID);

                    $advancePaymentTermUpdate->reqAmount = $roundedCalculatePer;
                    $advancePaymentTermUpdate->reqAmountTransCur_amount = $roundedCalculatePer;

                    $companyCurrencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $roundedCalculatePer);

                    $advancePaymentTermUpdate->reqAmountInPOTransCur = $roundedCalculatePer;
                    $advancePaymentTermUpdate->reqAmountInPOLocalCur = $companyCurrencyConversion['localAmount'];
                    $advancePaymentTermUpdate->reqAmountInPORptCur = $companyCurrencyConversion['reportingAmount'];
                    $advancePaymentTermUpdate->save();
                }
            }

        }

        return $this->sendResponse($purchaseOrder, 'Data retrieved successfully');

    }

}
