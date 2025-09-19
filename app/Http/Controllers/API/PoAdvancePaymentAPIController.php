<?php
/**
 * =============================================
 * -- File Name : PoAdvancePaymentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Advance Payment
 * -- Author : Mohamed Nazir
 * -- Create date : 02 - April 2018
 * -- Description : This file contains the all CRUD for Po Advance Payment
 * -- REVISION HISTORY
 * -- Date: 02-April 2018 By: Nazir Description: Added new functions named as poPaymentTermsAdvanceDetailView()
 * -- Date: 05-April 2018 By: Nazir Description: Added new functions named as loadPoPaymentTermsLogistic()
 * -- Date: 29-May 2018 By: Nazir Description: Added new functions named as storePoPaymentTermsLogistic()
 * -- Date: 31-April 2018 By: Nazir Description: Added new functions named as getLogisticPrintDetail()
 * -- Date: 14-June 2018 By: Nazir Description: Added new functions named as loadPoPaymentTermsLogisticForGRV()
 * -- Date: 27-August 2018 By: Nazir Description: Added new functions named as getPoLogisticPrintPDF()
 * -- Date: 13-November 2018 By: Nazir Description: Added new functions named as generateAdvancePaymentRequestReport(),exportAdvancePaymentRequestReport()
 **/
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoAdvancePaymentAPIRequest;
use App\Http\Requests\API\UpdatePoAdvancePaymentAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentAttachments;
use App\Models\AddonCostCategories;
use App\Models\ItemAssigned;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\PoAdvancePayment;
use App\Models\User;
use App\Repositories\PoAdvancePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\ProcumentOrder;
use App\Models\CurrencyMaster;
use App\Models\PoPaymentTermTypes;
use App\Repositories\UserRepository;
use App\Models\SupplierMaster;
use Illuminate\Support\Facades\DB;
use App\Models\PoPaymentTerms;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Facades\Auth;
use App\helper\Helper;
use App\helper\CreateExcel;
use App\Models\Company;
/**
 * Class PoAdvancePaymentController
 * @package App\Http\Controllers\API
 */
class PoAdvancePaymentAPIController extends AppBaseController
{
    /** @var  PoAdvancePaymentRepository */
    private $poAdvancePaymentRepository;
    private $userRepository;

    public function __construct(PoAdvancePaymentRepository $poAdvancePaymentRepo, UserRepository $userRepo)
    {
        $this->poAdvancePaymentRepository = $poAdvancePaymentRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the PoAdvancePayment.
     * GET|HEAD /poAdvancePayments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poAdvancePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->poAdvancePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poAdvancePayments = $this->poAdvancePaymentRepository->all();

        return $this->sendResponse($poAdvancePayments->toArray(), trans('custom.po_advance_payments_retrieved_successfully'));
    }

    /**
     * Store a newly created PoAdvancePayment in storage.
     * POST /poAdvancePayments
     *
     * @param CreatePoAdvancePaymentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePoAdvancePaymentAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['timestamp']);
        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['poID'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }
        $poTermAmount = PoPaymentTerms::where('paymentTermID', $input['paymentTermID'])
            ->where('poID', $input['poID'])
            ->first();
        if (empty($input['comAmount']) || $input['comAmount'] == 0) {
            return $this->sendError('Amount should be greater than 0');
        }

        //check record all ready exist
        $poTermExist = PoAdvancePayment::where('poTermID', $input['paymentTermID'])
            ->where('poID', $input['poID'])
            ->first();

        if (!empty($poTermExist)) {
            return $this->sendError('Advance Payment all ready requested');
        }

        $input['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $input['serviceLineID'] = $purchaseOrder->serviceLine;
        $input['companySystemID'] = $purchaseOrder->companySystemID;
        $input['companyID'] = $purchaseOrder->companyID;
        $input['supplierID'] = $purchaseOrder->supplierID;
        $input['SupplierPrimaryCode'] = $purchaseOrder->supplierPrimaryCode;
        $input['currencyID'] = $purchaseOrder->supplierTransactionCurrencyID;

        $input['poCode'] = $purchaseOrder->purchaseOrderCode;
        $input['poTermID'] = $input['paymentTermID'];
        $input['narration'] = $input['paymentTemDes'];

        /*     if (isset($input['comDate'])) {
                 $masterDate = str_replace('/', '-', $input['comDate']);
                 $input['reqDate'] = date('Y-m-d', strtotime($masterDate));
             }*/
        $input['reqDate'] = date('Y-m-d H:i:s');

        if(!empty($poTermAmount)) {
            $input['reqAmount'] = $poTermAmount->comAmount;
            $input['reqAmountTransCur_amount'] = $poTermAmount->comAmount;

            $companyCurrencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $poTermAmount->comAmount);

            $input['reqAmountInPOTransCur'] = $poTermAmount->comAmount;
        } else {
            $input['reqAmount'] = $input['comAmount'];
            $input['reqAmountTransCur_amount'] = $input['comAmount'];

            $companyCurrencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['comAmount']);

            $input['reqAmountInPOTransCur'] = $input['comAmount'];
        }

        $input['reqAmountInPOLocalCur'] = $companyCurrencyConversion['localAmount'];
        $input['reqAmountInPORptCur'] = $companyCurrencyConversion['reportingAmount'];

        $input['requestedByEmpID'] = $user->employee['empID'];
        $input['requestedByEmpName'] = $user->employee['empName'];

        $poAdvancePayments = $this->poAdvancePaymentRepository->create($input);

        if ($poAdvancePayments) {
            $update = PoPaymentTerms::where('paymentTermID', $input['paymentTermID'])
                ->update(['isRequested' => 1]);
        }

        return $this->sendResponse($poAdvancePayments->toArray(), trans('custom.po_advance_payment_saved_successfully'));
    }

    /**
     * Display the specified PoAdvancePayment.
     * GET|HEAD /poAdvancePayments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */

    public function show($id)
    {
        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            return $this->sendError(trans('custom.po_advance_payment_not_found'));
        }

        return $this->sendResponse($poAdvancePayment->toArray(), trans('custom.po_advance_payment_retrieved_successfully'));
    }

    /**
     * Update the specified PoAdvancePayment in storage.
     * PUT/PATCH /poAdvancePayments/{id}
     *
     * @param  int $id
     * @param UpdatePoAdvancePaymentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoAdvancePaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            return $this->sendError(trans('custom.po_advance_payment_not_found'));
        }

        $poAdvancePayment = $this->poAdvancePaymentRepository->update($input, $id);

        return $this->sendResponse($poAdvancePayment->toArray(), trans('custom.poadvancepayment_updated_successfully'));
    }

    /**
     * Remove the specified PoAdvancePayment from storage.
     * DELETE /poAdvancePayments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PoAdvancePayment $poAdvancePayment */
        DB::beginTransaction();
        try {
            $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);
            if (empty($poAdvancePayment)) {
                return $this->sendError(trans('custom.po_advance_payment_not_found'));
            }
            if ($poAdvancePayment["grvAutoID"]) {
                $grv = GRVMaster::find($poAdvancePayment["grvAutoID"]);
                if ($grv) {
                    if ($grv["grvConfirmedYN"]) {
                        return $this->sendError('Selected logistic charge is linked to GRV ' . $grv["grvPrimaryCode"]);
                    }
                }
            }
            $poAdvancePayment->delete();
            if ($poAdvancePayment) {
                $grvDetail = GRVDetails::where('grvAutoID', $poAdvancePayment["grvAutoID"])->update(['logisticsCharges_TransCur' => 0, 'logisticsCharges_LocalCur' => 0, 'logisticsChargest_RptCur' => 0, 'landingCost_TransCur' => DB::raw('GRVcostPerUnitSupTransCur'), 'landingCost_LocalCur' => DB::raw('GRVcostPerUnitLocalCur'), 'landingCost_RptCur' => DB::raw('GRVcostPerUnitComRptCur')]);
            }

            $attachment = DocumentAttachments::where('documentSystemID', 60)
                ->where('companySystemID', $poAdvancePayment->companySystemID)
                ->where('documentSystemCode', $poAdvancePayment->poAdvPaymentID)
                ->get();

            if (!empty($attachment)) {
                foreach ($attachment as $das) {
                    $path = $das->path;
                    $attachmentDel = DocumentAttachments::find($das->attachmentID);
                    $disk = Helper::policyWiseDisk($attachmentDel->companySystemID, 'public');
                    if ($exists = Storage::disk($disk)->exists($path)) {
                        $attachmentDel->delete();
                        Storage::disk($disk)->delete($path);
                    } else {
                        $attachmentDel->delete();
                    }
                }

            }
            DB::commit();
            return $this->sendResponse($id, trans('custom.po_advance_payment_deleted_successfully'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($id, 'Error Occurred');
        }
    }

    public function poPaymentTermsAdvanceDetailView(Request $request)
    {
        $input = $request->all();

        $AdvancePayment = PoAdvancePayment::where('poTermID', $input['paymentTermID'])->with(['cancelled_by'])->first();

        if (empty($AdvancePayment)) {
            return $this->sendError(trans('custom.po_payment_terms_not_found'));
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $AdvancePayment->poID)->first();

        $currency = CurrencyMaster::where('currencyID', $purchaseOrder->supplierTransactionCurrencyID)->first();

        $detailPaymentType = PoPaymentTermTypes::where('paymentTermsCategoryID', $AdvancePayment->LCPaymentYN)->first();


        $output = array('pomaster' => $purchaseOrder,
            'advancedetail' => $AdvancePayment,
            'currency' => $currency,
            'ptype' => $detailPaymentType
        );

        return $this->sendResponse($output, trans('custom.data_retrieved_successfully'));
    }

    public function loadPoPaymentTermsLogistic(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];

        $items = PoAdvancePayment::where('poID', $poID)
            ->where('poTermID', 0)
            ->where('confirmedYN', 1)
            ->where('isAdvancePaymentYN', 1)
            ->where('approvedYN', -1)
            ->with(['category_by', 'vat_sub_category','grv_by', 'currency', 'supplier_by' => function ($query) {
            }])->get();

        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }

    public function storePoPaymentTermsLogistic(Request $request)
    {
        $input = $request->all();

        //$input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['detail']['supplierID'])->first();

        if (empty($supplier)) {
            return $this->sendError(trans('custom.supplier_not_found'));
        }

        // checking grv detail exist

        $detail = DB::select('SELECT
	erp_grvmaster.grvAutoID,
	erp_grvmaster.grvPrimaryCode,
	erp_grvdetails.purchaseOrderMastertID
FROM
	erp_grvmaster
INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
WHERE
	erp_grvmaster.grvConfirmedYN = 0
AND erp_grvmaster.approved = 0
GROUP BY
	erp_grvmaster.grvAutoID,
	erp_grvmaster.grvPrimaryCode,
	erp_grvdetails.purchaseOrderMastertID
HAVING
	erp_grvdetails.purchaseOrderMastertID = ' . $input['purchaseOrderID'] . '
ORDER BY
	erp_grvmaster.grvAutoID DESC');

        if ($purchaseOrder->grvRecieved == 1) {
            if (!empty($detail) && empty($input['detail']['grvAutoID'])) {
                return $this->sendError(trans('custom.please_select_grv_as_there_is_grv_done_for_this_po'));
            }
        }

        if ($purchaseOrder->grvRecieved == 2) {
            if (!empty($detail) && empty($input['detail']['grvAutoID'])) {
                return $this->sendError(trans('custom.please_select_grv_as_there_is_grv_done_for_this_po'));
            } else if (empty($detail)) {
                return $this->sendError(trans('custom.po_is_fully_received_you_cannot_add_logistic'));
            }
        }


        $checkCategory = AddonCostCategories::find($input['detail']['logisticCategoryID']);

        if (!$checkCategory) {
            return $this->sendError(trans('custom.logistic_category_not_found'));    
        }

        if (is_null($checkCategory->itemSystemCode)) {
            return $this->sendError('Please assign a service item to selected logistic category');    
        }

        $checkAssignedStatus = ItemAssigned::where('companySystemID', $purchaseOrder->companySystemID)
                                            ->where('itemCodeSystem', $checkCategory->itemSystemCode)
                                            ->where('isAssigned', -1)
                                            ->first();

        if (!$checkAssignedStatus) {
            return $this->sendError('Item linked with this logistic category is not assigned to the company');    
        }


        $input['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $input['serviceLineID'] = $purchaseOrder->serviceLine;
        $input['companySystemID'] = $purchaseOrder->companySystemID;
        $input['companyID'] = $purchaseOrder->companyID;
        $input['SupplierPrimaryCode'] = $purchaseOrder->supplierPrimaryCode;

        $input['poID'] = $input['purchaseOrderID'];
        $input['poCode'] = $purchaseOrder->purchaseOrderCode;
        $input['narration'] = $input['detail']['narration'];

        //grv code sorting
        if (isset($input['detail']['grvAutoID']) && !empty($input['detail']['grvAutoID'])) {
            $input['grvAutoID'] = $input['detail']['grvAutoID'];
        } else {
            $input['grvAutoID'] = 0;
        }

        if (isset($input['detail']['reqDate'])) {
            $masterDate = str_replace('/', '-', $input['detail']['reqDate']);
            $input['reqDate'] = date('Y-m-d', strtotime($masterDate));
        }

        $currencyID = null;

        if (is_array($input['detail']['currencyID'])) {
            $currencyID = $input['detail']['currencyID'][0];
        } else {
            $currencyID = $input['detail']['currencyID'];
        }
        $input['currencyID'] = $currencyID;
        $input['reqAmount'] = $input['detail']['reqAmount'];
        $input['reqAmountTransCur_amount'] = \Helper::roundValue($input['detail']['reqAmount']);
        $input['logisticCategoryID'] = $input['detail']['logisticCategoryID'];

        $companyCurrencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $currencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['detail']['reqAmount']);

        //$input['detail']['reqAmount'];
        $input['reqAmountInPOTransCur'] = \Helper::roundValue($companyCurrencyConversion['documentAmount']);
        $input['reqAmountInPOLocalCur'] = \Helper::roundValue($companyCurrencyConversion['localAmount']);
        $input['reqAmountInPORptCur'] = \Helper::roundValue($companyCurrencyConversion['reportingAmount']);

        $input['requestedByEmpID'] = $user->employee['empID'];
        $input['requestedByEmpName'] = $user->employee['empName'];

        //updating supplier details coloums
        if ($supplier) {
            $input['supplierID'] = $input['detail']['supplierID'];
            $input['SupplierPrimaryCode'] = $supplier->primarySupplierCode;
            $input['liabilityAccountSysemID'] = $supplier->liabilityAccountSysemID;
            $input['liabilityAccount'] = $supplier->liabilityAccount;
            $input['UnbilledGRVAccountSystemID'] = $supplier->UnbilledGRVAccountSystemID;
            $input['UnbilledGRVAccount'] = $supplier->UnbilledGRVAccount;
        }

        //updating default coloums
        $input['poTermID'] = 0;
        $input['confirmedYN'] = 1;
        $input['approvedYN'] = -1;
        $input['isAdvancePaymentYN'] = 1;
        $input['selectedToPayment'] = 0;
        $input['fullyPaid'] = 0;
        

        $input['vatSubCategoryID'] = isset($input['detail']['vatSubCategoryID']) ? $input['detail']['vatSubCategoryID'] : null;
        $input['VATPercentage'] = isset($input['detail']['VATPercentage']) ? $input['detail']['VATPercentage'] : 0;
        $input['addVatOnPO'] = isset($input['detail']['addVatOnPO']) ? $input['detail']['addVatOnPO'] : 0;

        if (isset($input['detail']['VATAmount']) && $input['detail']['VATAmount'] > 0) {
            $companyCurrencyConversionVAT = \Helper::currencyConversion($purchaseOrder->companySystemID, $currencyID, $currencyID, $input['detail']['VATAmount']);

            $input['VATAmount'] = $input['detail']['VATAmount'];
            $input['VATAmountLocal'] = \Helper::roundValue($companyCurrencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($companyCurrencyConversionVAT['reportingAmount']);
        }

        $poAdvancePayments = $this->poAdvancePaymentRepository->create($input);

        return $this->sendResponse($poAdvancePayments->toArray(), trans('custom.po_advance_payment_saved_successfully'));
    }

    public function getLogisticPrintDetail(Request $request)
    {
        $input = $request->all();
        $poAdvPaymentID = $input['poAdvPaymentID'];
        $typeID = $input['typeID'];

        if ($typeID == 1) {

            $poPaymentTerms = PoAdvancePayment::where('poTermID', $poAdvPaymentID)
                ->first();

            $poAdvPaymentID = $poPaymentTerms->poAdvPaymentID;
        }

        $items = PoAdvancePayment::where('poAdvPaymentID', $poAdvPaymentID)
            ->with(['company', 'currency', 'supplier_by' => function ($query) {
            }])->first();

        $purchaseOrder = ProcumentOrder::find($items->poID);

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $refernaceDoc = CompanyDocumentAttachment::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->first();

        $newRefDoc = explode('D', $refernaceDoc["docRefNumber"]);

        $newRefDocNew = $newRefDoc[0];

        $printData = array(
            'podata' => $items,
            'docRef' => $newRefDocNew
        );

        return $this->sendResponse($printData, trans('custom.data_retrieved_successfully'));
    }

    public function loadPoPaymentTermsLogisticForGRV(Request $request)
    {
        $input = $request->all();
        $grvAutoID = $input['grvAutoID'];

        $items = PoAdvancePayment::where('grvAutoID', $grvAutoID)
            ->where('confirmedYN', 1)
            ->where('approvedYN', -1)
            ->with(['category_by', 'grv_by', 'currency', 'supplier_by' => function ($query) {
            }])->get();

        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }

    public function unlinkLogistic(Request $request)
    {
        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($request->poAdvPaymentID);

        if (empty($poAdvancePayment)) {
            return $this->sendError(trans('custom.po_advance_payment_not_found'));
        }

        if ($poAdvancePayment["grvAutoID"]) {
            $grv = GRVMaster::find($poAdvancePayment["grvAutoID"]);
            if ($grv) {
                if ($grv["grvConfirmedYN"]) {
                    return $this->sendError('Selected logistic charge is linked to GRV ' . $grv["grvPrimaryCode"]);
                }
            }
        }

        $poAdvancePayment = $this->poAdvancePaymentRepository->update(['grvAutoID' => 0], $request->poAdvPaymentID);

        return $this->sendResponse([], trans('custom.successfully_unlinked'));
    }

    public function getPoLogisticPrintPDF(Request $request)
    {
        $id = $request->get('id');

        $typeID = $request->get('typeID');

        if ($typeID == 1) {

            $poPaymentTerms = PoAdvancePayment::where('poTermID', $id)
                ->first();

            $id = $poPaymentTerms->poAdvPaymentID;
        }

        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            return $this->sendError(trans('custom.po_advance_payment_not_found'));
        }

        $purchaseOrder = ProcumentOrder::find($poAdvancePayment->poID);

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $PoAdvancePaymentData = PoAdvancePayment::where('poAdvPaymentID', $id)
            ->with(['company', 'currency', 'supplier_by' => function ($query) {
            }])->first();

        $refernaceDoc = CompanyDocumentAttachment::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->first();

        $newRefDoc = explode('D', $refernaceDoc["docRefNumber"]);

        $newRefDocNew = $newRefDoc[0];

        $currencyDecimal = CurrencyMaster::select('DecimalPlaces')
            ->where('currencyID', $purchaseOrder->supplierTransactionCurrencyID)
            ->first();

        $decimal = 2;
        if ($currencyDecimal) {
            $decimal = $currencyDecimal['DecimalPlaces'];
        }


        $order = array(
            'podata' => $PoAdvancePaymentData,
            'docRef' => $newRefDocNew,
            'numberFormatting' => $decimal
        );

        $html = view('print.po_logistic_print', $order);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream();
    }


    public function generateAdvancePaymentRequestReport(Request $request)
    {

        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'reportTypeID' => 'required',
            'asOfDate' => 'required',
            'currencyID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        
        $input = $this->convertArrayToSelectedValue($input, array('currencyID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $advancePaymentRequest = $this->advancePaymentRequestReportQry($input,$search);

        return \DataTables::of($advancePaymentRequest)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        // $query->orderBy('poAdvPaymentID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function advancePaymentRequestReportQry($request,$search){

        $input = $request;
        $selectedCompanyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $asOfDate = (new Carbon($input['asOfDate']))->format('Y-m-d');

        $detailsSumColumn = 'paymentAmount';
        $caseColumn = 'reqAmount';
        if ($input['currencyID'] == 2) {
            $caseColumn = 'reqAmountInPOLocalCur';
            $detailsSumColumn = 'localAmount';
        } else if ($input['currencyID'] == 3) {
            $caseColumn = 'reqAmountInPORptCur';
            $detailsSumColumn = 'comRptAmount';
        }

        $agingField = '';
        if($input['reportTypeID'] == 'APRA') {
            $aging = ['0-30', '31-60', '61-90', '91-120', '121-150', '151-180', '181-210', '211-240', '241-365', '> 365'];
            $condition = 'DATEDIFF("' . $asOfDate . '",DATE(erp_purchaseorderadvpayment.reqDate))';
            if (!empty($aging)) { /*calculate aging range in query*/
                $count = count($aging);
                $c = 1;
                foreach ($aging as $val) {
                    if ($count == $c) {
                        $agingField .= "if(" . $condition . "   > " . 365 . "," . $caseColumn . ",0) as `case" . $c . "`,";
                    } else {
                        $list = explode("-", $val);
                        $agingField .= "if(" . $condition . " >= " . $list[0] . " AND " . $condition . " <= " . $list[1] . "," . $caseColumn . ",0) as `case" . $c . "`,";
                    }
                    $c++;
                }
            }
        }

        $advancePaymentRequest = DB::table('erp_purchaseorderadvpayment')
            ->selectRaw('erp_purchaseorderadvpayment.*,'.$agingField.'
                                        erp_purchaseordermaster.localCurrencyID,erp_purchaseordermaster.companyReportingCurrencyID,erp_purchaseordermaster.supplierTransactionCurrencyID,
                                        erp_purchaseordermaster.poTotalSupplierTransactionCurrency,erp_purchaseordermaster.poTotalLocalCurrency,
                                        erp_purchaseordermaster.poTotalComRptCurrency,
                                        suppliermaster.primarySupplierCode,suppliermaster.supplierName,
                                        trns.CurrencyCode as trnsCurrencyCode,trns.DecimalPlaces as trnsDecimalPlaces,
                                        potrns.CurrencyCode as potrnsCurrencyCode,potrns.DecimalPlaces as potrnsDecimalPlaces,
                                        local.CurrencyCode as localCurrencyCode,local.DecimalPlaces as localDecimalPlaces,
                                        rpt.CurrencyCode as rptCurrencyCode,rpt.DecimalPlaces as rptDecimalPlaces,
                                        companymaster.CompanyName,
                                        details.PayMasterAutoId,details.SumOfpaymentAmount,erp_paysupplierinvoicemaster.approved as pay_approved,
                                        (If(round(reqAmount - details.SumOfpaymentAmount)=0 And erp_paysupplierinvoicemaster.approved=-1,2,
                                        If((selectedToPayment=-1 Or selectedToPayment=0) And round(reqAmount - details.SumOfpaymentAmount)<>0 And erp_paysupplierinvoicemaster.approved=-1,1,
                                        If(selectedToPayment=-1 And erp_paysupplierinvoicemaster.approved=0,3,0)))) as status,
                                        DATEDIFF("' . $asOfDate . '",DATE(erp_purchaseorderadvpayment.reqDate)) as ageDays')
            ->whereIn('erp_purchaseorderadvpayment.companySystemID', $subCompanies)
            ->where('erp_purchaseordermaster.poConfirmedYN', 1)
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseordermaster.poCancelledYN', 0)
            ->where('erp_purchaseorderadvpayment.cancelledYN', 0)
            ->whereDate('erp_purchaseorderadvpayment.reqDate','<=', $asOfDate)
            ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('suppliermaster', 'erp_purchaseorderadvpayment.supplierID', 'suppliermaster.supplierCodeSystem')
            ->leftJoin('currencymaster as trns', 'erp_purchaseorderadvpayment.currencyID', 'trns.currencyID')
            ->leftJoin('currencymaster as potrns', 'erp_purchaseordermaster.supplierTransactionCurrencyID', 'potrns.currencyID')
            ->leftJoin('currencymaster as local', 'erp_purchaseordermaster.localCurrencyID', 'local.currencyID')
            ->leftJoin('currencymaster as rpt', 'erp_purchaseordermaster.companyReportingCurrencyID', 'rpt.currencyID')
            ->leftJoin('companymaster', 'erp_purchaseorderadvpayment.companySystemID', 'companymaster.companySystemID')
            ->leftJoin(DB::raw('(SELECT poAdvPaymentID, SumOfpaymentAmount,PayMasterAutoId FROM (SELECT * FROM
                ( SELECT MAX( PayMasterAutoId ) AS PayMasterAutoId,poAdvPaymentID as poAdvPaymentIDs FROM erp_advancepaymentdetails GROUP BY poAdvPaymentID ) a
                INNER JOIN ( SELECT erp_advancepaymentdetails.poAdvPaymentID, Sum( erp_advancepaymentdetails.'.$detailsSumColumn.' ) AS SumOfpaymentAmount FROM erp_advancepaymentdetails GROUP BY poAdvPaymentID) AS maximum ON maximum.poAdvPaymentID = a.poAdvPaymentIDs 
                ) b) as details'), function ($query)
            {
                $query->on('erp_purchaseorderadvpayment.poAdvPaymentID', '=', 'details.poAdvPaymentID');
            })
            ->leftJoin('erp_paysupplierinvoicemaster', 'details.PayMasterAutoId', 'erp_paysupplierinvoicemaster.PayMasterAutoId');

        
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $advancePaymentRequest = $advancePaymentRequest->where(function ($query) use ($search) {
                $query->where('poCode', 'LIKE', "%{$search}%")
                    ->orWhere('primarySupplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('erp_purchaseorderadvpayment.narration', 'LIKE', "%{$search}%");
            });
        }

        $advancePaymentRequest = $advancePaymentRequest->get();

        if (array_key_exists('invoiceType', $input) && !is_null($input['invoiceType'])) {
            $invoiceID = collect($input['invoiceType']);
           $getInvoiceID = $invoiceID->pluck('id')->toArray();
           $advancePaymentRequest = collect($advancePaymentRequest)->whereIn('status', $getInvoiceID)->all();
       }

       return $advancePaymentRequest;
    }


    public function exportAdvancePaymentRequestReport(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'reportTypeID' => 'required',
            'asOfDate' => 'required',
            'currencyID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $from_date = $request->asOfDate;
        $to_date = $request->asOfDate;
        $company = Company::find($request->companyId);
        $company_name = $company->CompanyName;
        $from_date =  ((new Carbon($from_date))->format('d/m/Y'));

        $fileName = trans('custom.advance_payment_request');

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('currencyID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $data = array();
            $search = $request->input('search.value');
            $advancePaymentRequest = $this->advancePaymentRequestReportQry($input,$search);
            $type = $request->type;

            if ($advancePaymentRequest) {

                $groupArray = array();

                $i = 0;
                $poCode = '';
                foreach ($advancePaymentRequest as $dt){
                    $groupArray[$dt->poCode][$i] = $dt;
                    if( $poCode != $dt->poCode){
                        $i++;
                    }
                    $poCode = $dt->poCode;
                }

                $x = 0;
                foreach ($advancePaymentRequest as $val) {

                    $decimal = 2;
                    $poTransCurDecimal = 2;
                    if ($input['currencyID'] == 1) {
                        $decimal = $val->trnsDecimalPlaces;
                        $poTransCurDecimal = $val->potrnsDecimalPlaces;
                    } else if ($input['currencyID'] == 2) {
                        $decimal = $val->localDecimalPlaces;
                    } else if ($input['currencyID'] == 3) {
                        $decimal = $val->rptDecimalPlaces;
                    }


                    if (\Helper::checkIsCompanyGroup($input['companyId'])) {
                        $data[$x][trans('custom.company_id')] = $val->companyID;
                        $data[$x][trans('custom.company_name')] = $val->CompanyName;
                    }

                    $data[$x][trans('custom.supplier_code')] = $val->primarySupplierCode;
                    $data[$x][trans('custom.supplier_name')] = $val->supplierName;
                    $data[$x][trans('custom.purchase_order_code')] = $val->poCode;
                    $data[$x][trans('custom.req_date')] =  \Helper::dateFormat($val->reqDate);
                    $data[$x][trans('custom.narration')] = $val->narration;

                    if ($input['currencyID'] == 1) {
                        $data[$x][trans('custom.po_currency')] = $val->potrnsCurrencyCode;
                        $data[$x][trans('custom.po_amount')] = round($val->poTotalSupplierTransactionCurrency,$poTransCurDecimal);
                        $data[$x][trans('custom.req_currency')] = $val->trnsCurrencyCode;
                        $data[$x][trans('custom.req_amount')] = round($val->reqAmount,$decimal);
                    } else if ($input['currencyID'] == 2) {
                        $data[$x][trans('custom.po_currency')] = $val->localCurrencyCode;
                        $data[$x][trans('custom.po_amount')] = round($val->poTotalLocalCurrency,$decimal);
                        $data[$x][trans('custom.req_currency')] = $val->localCurrencyCode;
                        $data[$x][trans('custom.req_amount')] = round($val->reqAmountInPOLocalCur,$decimal);
                    } else if ($input['currencyID'] == 3) {
                        $data[$x][trans('custom.po_currency')] = $val->rptCurrencyCode;
                        $data[$x][trans('custom.po_amount')] = round($val->poTotalComRptCurrency,$decimal);
                        $data[$x][trans('custom.req_currency')] = $val->rptCurrencyCode;
                        $data[$x][trans('custom.req_amount')] = round($val->reqAmountInPORptCur,$decimal);
                    }else{
                        $data[$x][trans('custom.po_currency')] = '';
                        $data[$x][trans('custom.po_amount')] = round(0,$decimal);
                        $data[$x][trans('custom.req_currency')] = '';
                        $data[$x][trans('custom.req_amount')] = round(0,$decimal);
                    }

                    $data[$x][trans('custom.paid_amount')] = round($val->SumOfpaymentAmount,$decimal);

                    $status = "";
                    if($val->status == 0){
                        $status= trans('custom.payment_not_created');
                    } else if($val->status == 1){
                        $status = trans('custom.payment_partially_released');
                    }
                    else if($val->status == 2){
                        $status= trans('custom.payment_released');
                    }
                    else if($val->status == 3){
                        $status= trans('custom.payment_created_but_not_released');
                    }

                    $data[$x][trans('custom.status')] = $status;

                    if($input['reportTypeID'] == 'APRA') {
                        $data[$x]['<=30'] = number_format($val->case1, $decimal);
                        $data[$x]['31 to 60'] = number_format($val->case2, $decimal);
                        $data[$x]['61 to 90'] = number_format($val->case3, $decimal);
                        $data[$x]['91 to 120'] = number_format($val->case4, $decimal);
                        $data[$x]['121 to 150'] = number_format($val->case5, $decimal);
                        $data[$x]['151 to 180'] = number_format($val->case6, $decimal);
                        $data[$x]['181 to 210'] = number_format($val->case7, $decimal);
                        $data[$x]['211 to 240'] = number_format($val->case8, $decimal);
                        $data[$x]['241 to 365'] = number_format($val->case9, $decimal);
                        $data[$x]['Over 365'] = number_format($val->case10, $decimal);
                    }
                    $x++;
                }
            } else {
                $data = array();
            }
            $requestCurrency = NULL;
        $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
        $path = 'accounts-payable/report/advance_payment_request/excel/';
            if($input['reportTypeID'] == 'APRA') {
                $title = trans('custom.advance_payment_request_aging');
            }
            else
            {
                $title = trans('custom.advance_payment_request_detail');
            }
            
            $detail_array = array('type' => 2,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'company_code'=>$companyCode,'cur'=>$requestCurrency,'title'=>$title);
         
            $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

            if($basePath == '')
            {
                 return $this->sendError('Unable to export excel');
            }
            else
            {
                 return $this->sendResponse($basePath, trans('custom.success_export'));
            }
    }

    public function advancePaymentTermCancel(Request $request)
    {
        $input = $request->all();

        $advancePayment = PoAdvancePayment::where('poTermID', $input['paymentTermID'])->first();

        if (empty($advancePayment)) {
            return $this->sendError(trans('custom.advance_payment_terms_not_found'));
        }

        if ($advancePayment->selectedToPayment == -1) {
            return $this->sendError(trans('custom.advance_payment_request_is_slected_for_payment_vou'), 500);
        }

        $advancePayment->cancelledYN = 1; 
        $advancePayment->cancelledComment = $input['comment']; 
        $advancePayment->cancelledByEmployeeSystemID = \Helper::getEmployeeSystemID(); 
        $advancePayment->cancelledDate = Carbon::now(); 
        $advancePayment->reqAmount = 0;
        $advancePayment->reqAmountTransCur_amount = 0;
        $advancePayment->reqAmountInPOTransCur = 0;
        $advancePayment->reqAmountInPOLocalCur = 0;
        $advancePayment->reqAmountInPORptCur = 0;

        $advancePayment->save();

        PoPaymentTerms::where('paymentTermID',$advancePayment['poTermID'])->update([
            'comAmount' => 0,
            'comPercentage' => 0
        ]);

        return $this->sendResponse([], trans('custom.successfully_cancelled'));
    }
}
