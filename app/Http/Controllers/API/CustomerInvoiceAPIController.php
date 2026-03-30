<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Customer Invoice
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceAPIRequest;
use App\Models\Company;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DocumentSystemMapping;
use App\Models\MatchDocumentMaster;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\Taxdetail;
use App\Models\ThirdPartySystems;
use App\Repositories\CustomerInvoiceRepository;
use App\helper\Helper;
use App\Services\API\CustomerInvoiceAPIService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Response;
use App\Traits\DocumentSystemMappingTrait;
use App\Models\CustomerMaster;

/**
 * Class CustomerInvoiceController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceAPIController extends AppBaseController
{
  


    /** @var  CustomerInvoiceRepository */
    private $customerInvoiceRepository;
    use DocumentSystemMappingTrait;
    public function __construct(CustomerInvoiceRepository $customerInvoiceRepo)
    {
        $this->customerInvoiceRepository = $customerInvoiceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoices",
     *      summary="Get a listing of the CustomerInvoices.",
     *      tags={"CustomerInvoice"},
     *      description="Get all CustomerInvoices",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoice")
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
        $this->customerInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoices = $this->customerInvoiceRepository->all();

        return $this->sendResponse($customerInvoices->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param CreateCustomerInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoices",
     *      summary="Store a newly created CustomerInvoice in storage",
     *      tags={"CustomerInvoice"},
     *      description="Store CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoice")
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
     *                  ref="#/definitions/CustomerInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoices = $this->customerInvoiceRepository->create($input);

        return $this->sendResponse($customerInvoices->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoices/{id}",
     *      summary="Display the specified CustomerInvoice",
     *      tags={"CustomerInvoice"},
     *      description="Get CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoice",
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
     *                  ref="#/definitions/CustomerInvoice"
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
        /** @var CustomerInvoice $customerInvoice */
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        return $this->sendResponse($customerInvoice->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoices/{id}",
     *      summary="Update the specified CustomerInvoice in storage",
     *      tags={"CustomerInvoice"},
     *      description="Update CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoice")
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
     *                  ref="#/definitions/CustomerInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoice $customerInvoice */
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        $customerInvoice = $this->customerInvoiceRepository->update($input, $id);

        return $this->sendResponse($customerInvoice->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoices/{id}",
     *      summary="Remove the specified CustomerInvoice from storage",
     *      tags={"CustomerInvoice"},
     *      description="Delete CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoice",
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
        /** @var CustomerInvoice $customerInvoice */
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        $customerInvoice->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_invoice')]));
    }

    public function createCustomerInvoiceAPI(Request $request){

        $input = $request->all();

        $header = $request->header('Authorization');

        $createCustomerInvoice = CustomerInvoiceAPIService::storeCustomerInvoicesFromAPI($input);

        if($createCustomerInvoice['status']){
            if (count($createCustomerInvoice['data']) > 0) {
                $this->storeToDocumentSystemMapping(20,$createCustomerInvoice['data'],$header);
            }
            return $this->sendResponse($createCustomerInvoice['responseData'],"Customer Invoice Created Successfully!");
        }
        else{
            return $this->sendAPIError($createCustomerInvoice['message'],422, $createCustomerInvoice['responseData']);
        }

    }


    public function getApprovedCustomerInvoiceBalancesAPI(Request $request)
    {
        $input = $request->all();

        $inputForValidation = $input;
        $rawGeneratedFrom = $inputForValidation['generated_from'] ?? null;
        if ($rawGeneratedFrom === null || $rawGeneratedFrom === '') {
            $inputForValidation['generated_from'] = null;
        } elseif (is_array($rawGeneratedFrom)) {
            $normalized = [];
            foreach ($rawGeneratedFrom as $v) {
                if ($v === null || $v === '') {
                    continue;
                }
                $normalized[] = strtoupper(trim((string) $v));
            }
            $inputForValidation['generated_from'] = $normalized === [] ? null : array_values(array_unique($normalized));
        } else {
            $inputForValidation['generated_from'] = $rawGeneratedFrom;
        }

        $validationRules = [
            'invoice_code' => 'nullable|array',
            'invoice_code.*' => 'string',
            'invoice_type' => 'nullable|string',
            'customer_code' => 'nullable|array',
            'customer_code.*' => 'string',
            'generated_from' => 'nullable|array',
            'generated_from.*' => 'in:POS,CLUB',
            'company_id' => 'required|integer',
        ];

        $validationMessages = [
            'invoice_code.string' => 'Invoice code must be a string',
            'customer_code.string' => 'Customer code must be a string',
            'company_id.required' => 'Company ID is required',
            'company_id.integer' => 'Company ID must be an integer',
            'invoice_type.string' => 'Invoice type must be a string',
            'generated_from.array' => 'generated_from must be an array (e.g. ["POS"] or ["POS","CLUB"])',
            'generated_from.*.in' => 'Generated from not match with system',
        ];

        $validator = \Validator::make($inputForValidation, $validationRules, $validationMessages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 422);
        }

        if ($request->has('page')) {
            $pageValidator = \Validator::make($input, [
                'page' => 'required|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:500',
            ], [
                'page.required' => 'page parameter is required',
                'page.integer' => 'page must be an integer',
                'page.min' => 'page must be at least 1',
                'per_page.integer' => 'per_page must be an integer',
                'per_page.min' => 'per_page must be at least 1',
                'per_page.max' => 'per_page cannot exceed 500',
            ]);
            if ($pageValidator->fails()) {
                return $this->sendError($pageValidator->errors()->first(), 422);
            }
        }

        $companyId = (int) $request->get('company_id', 0);

        
        $invoiceCodeRaw = $input['invoice_code'] ?? null;
        $invoiceCodes = isset($invoiceCodeRaw) && is_array($invoiceCodeRaw)
            ? array_values(array_filter($invoiceCodeRaw, function ($v) {
                return $v !== null && $v !== '';
            }))
            : [];



        $customerCodeRaw = $input['customer_code'] ?? null;
        $customerCodes = isset($customerCodeRaw) && is_array($customerCodeRaw)
            ? array_values(array_filter($customerCodeRaw, function ($v) {
                return $v !== null && $v !== '';
            }))
            : [];

        $companyMaster = Company::where('companySystemID', $companyId)->first();
        if (!$companyMaster) {
            return $this->sendError(trans('custom.the_company_system_ID_not_matching_with_system', ['companySystemID' => $companyId]), 422);
        }

        $isGroup = Helper::checkIsCompanyGroup($input['company_id']);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($input['company_id']);
        }
        else {
            $subCompanies = [$input['company_id']];
        }

        if (! empty($invoiceCodes)) {
            foreach ($invoiceCodes as $code) {
                $invoice = CustomerInvoice::where('bookingInvCode', $code)->whereIn('companySystemID', $subCompanies)->first();
                if (! $invoice) {
                    return $this->sendError(trans('custom.the_invoice_code_not_matching_with_system', ['invoiceCode' => $code]), 422);
                }
            }

        }

        if (! empty($customerCodes)) {
            foreach ($customerCodes as $code) {
                $customer = CustomerMaster::where('CutomerCode', $code)->whereIn('primaryCompanySystemID', $subCompanies)->first();
                if (! $customer) {
                    return $this->sendError(trans('custom.the_customer_code_not_matching_with_system', ['customerCode' => $code]), 422);
                }
            }
        }


        $invoiceTypeLabelToDocumentType = [
            'direct invoice' => 0,
            'proforma invoice' => 1,
            'item sales invoice' => 2,
            'from delivery note' => 3,
            'from delivery order' => 3,
            'from sales order' => 4,
            'from quotation' => 5,
        ];

        $type = null;
        $rawInvoiceType = $input['invoice_type'] ?? null;
        if ($rawInvoiceType !== null && $rawInvoiceType !== '') {
            $key = strtolower(trim((string) $rawInvoiceType));
            if (! array_key_exists($key, $invoiceTypeLabelToDocumentType)) {
                return $this->sendAPIError('Customer invoice Type not match with system', 422, []);
            }
            $type = $invoiceTypeLabelToDocumentType[$key];
        }

        $generatedFromList = $inputForValidation['generated_from'] ?? null;

        $mappingInvSub = ! empty($generatedFromList)
            ? self::customerInvoiceMappingDocumentIdsSubQuery($generatedFromList)
            : null;

        $receiptPaidSub = self::customerInvoiceReceiptPaymentBaseQuery($subCompanies);
        if ($mappingInvSub !== null) {
            $receiptPaidSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'det.bookingInvCodeSystem');
        }
        $receiptPaidSub = $receiptPaidSub
            ->selectRaw('det.bookingInvCodeSystem as invoice_id, SUM(IFNULL(det.receiveAmountTrans,0)) as receipt_paid')
            ->groupBy('det.bookingInvCodeSystem');

        $matchingPaidSub = self::customerInvoiceMatchingPaymentBaseQuery($subCompanies);
        if ($mappingInvSub !== null) {
            $matchingPaidSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'det.bookingInvCodeSystem');
        }
        $matchingPaidSub = $matchingPaidSub
            ->selectRaw('det.bookingInvCodeSystem as invoice_id, SUM(IFNULL(det.receiveAmountTrans,0)) as matching_paid')
            ->groupBy('det.bookingInvCodeSystem');

        $returnSub = self::customerInvoiceSalesReturnBaseQuery($subCompanies);
        if ($mappingInvSub !== null) {
            $returnSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'srd.custInvoiceDirectAutoID');
        }
        $returnSub = $returnSub
            ->selectRaw('srd.custInvoiceDirectAutoID as invoice_id, SUM(IFNULL(srd.transactionAmount,0) + (IFNULL(srd.transactionAmount,0) * IFNULL(srd.VATPercentage,0) / 100)) as return_trans')
            ->groupBy('srd.custInvoiceDirectAutoID');

        $taxTable = (new Taxdetail)->getTable();
        $taxSub = DB::table($taxTable.' as td')
            ->where('td.documentSystemID', 20)
            ->whereIn('td.companySystemID', $subCompanies);
        if ($mappingInvSub !== null) {
            $taxSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'td.documentSystemCode');
        }
        $taxSub = $taxSub
            ->selectRaw('td.documentSystemCode as invoice_id, SUM(IFNULL(td.amount,0)) as tax_amount')
            ->groupBy('td.documentSystemCode');

        $invTable = (new CustomerInvoiceDirect)->getTable();

        $documentTypeLabels = [
            0 => 'Direct Invoice',
            1 => 'Proforma Invoice',
            2 => 'Item Sales Invoice',
            3 => 'From Delivery Note',
            4 => 'From Sales Order',
            5 => 'From Quotation',
        ];

        $query = CustomerInvoiceDirect::query()
            ->with(['customer', 'warehouse', 'currency']);
        if ($mappingInvSub !== null) {
            $query->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', $invTable.'.custInvoiceDirectAutoID');
        }
        $query->leftJoinSub($receiptPaidSub, 'receipt_paid', 'receipt_paid.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($matchingPaidSub, 'matching_paid', 'matching_paid.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($returnSub, 'ret', 'ret.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($taxSub, 'tax', 'tax.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub(self::customerInvoiceGeneratedFromLabelSubQuery(), 'gen_src', 'gen_src.documentId', '=', $invTable.'.custInvoiceDirectAutoID')
            ->whereIn($invTable.'.companySystemID', $subCompanies)
            ->where($invTable.'.confirmedYN', 1)
            ->where($invTable.'.approved', -1)
            ->where(function ($q) use ($invTable) {
                $q->whereNull($invTable.'.canceledYN')->orWhere($invTable.'.canceledYN', 0);
            })
            ->select($invTable.'.*')
            ->selectRaw('(IFNULL('.$invTable.'.bookingAmountTrans,0) + IFNULL(tax.tax_amount,0)) as invoice_amount')
            ->selectRaw('IFNULL(tax.tax_amount,0) as tax_amount')
            ->selectRaw('(IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0) + IFNULL(ret.return_trans,0)) as paid_amount')
            ->selectRaw('IFNULL(receipt_paid.receipt_paid,0) as receipt_paid_amount')
            ->selectRaw('IFNULL(matching_paid.matching_paid,0) as matching_paid_amount')
            ->selectRaw('IFNULL(ret.return_trans,0) as return_amount')
            ->selectRaw('((IFNULL('.$invTable.'.bookingAmountTrans,0) + IFNULL(tax.tax_amount,0)) - (IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0)) - IFNULL(ret.return_trans,0)) as balance_amount')
            ->selectRaw('CASE
                    WHEN (
                        (IFNULL(' . $invTable . '.bookingAmountTrans,0) + IFNULL(tax.tax_amount,0)) 
                        - 
                        (IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0) + IFNULL(ret.return_trans,0))
                    ) <= 0 
                    THEN "Fully paid"
                    WHEN (IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0) + IFNULL(ret.return_trans,0)) > 0 THEN "Partially paid"
                    ELSE "Unpaid"
                END as balance_payment_status')
            ->addSelect(DB::raw('gen_src.generated_from_label as generated_from_display'));

        if (! empty($invoiceCodes)) {
            $query->whereIn($invTable.'.bookingInvCode', $invoiceCodes);
        }

        if (! empty($customerCodes)) {
            $query->whereHas('customer', function ($q) use ($customerCodes) {
                $q->whereIn('CutomerCode', $customerCodes);
            });
        }

        if ($type !== null) {
            $query->where($invTable.'.isPerforma', $type);
        }

   
        $loadStatusDetailsByInvoiceIds = function (array $invoiceIds) use ($subCompanies) {
            if ($invoiceIds === []) {
                return [];
            }

            $invoiceIds = array_values(array_unique(array_map('intval', $invoiceIds)));
            $byInvoice = array_fill_keys($invoiceIds, []);

            $receiptRows = self::customerInvoiceReceiptPaymentBaseQuery($subCompanies)
                ->whereIn('det.bookingInvCodeSystem', $invoiceIds)
                ->groupBy('det.bookingInvCodeSystem', 'det.custReceivePaymentAutoID', 'rv.custPaymentReceiveCode', 'rv.confirmedYN', 'rv.approved')
                ->selectRaw('
                    det.bookingInvCodeSystem as invoice_id,
                    rv.custPaymentReceiveCode as doc_code,
                    SUM(IFNULL(det.receiveAmountTrans,0)) as amount,
                    rv.confirmedYN as rv_confirmed,
                    rv.approved as rv_approved
                ')
                ->get();

            foreach ($receiptRows as $r) {
                $iid = (int) $r->invoice_id;
                if (! array_key_exists($iid, $byInvoice)) {
                    continue;
                }
                $byInvoice[$iid][] = [
                    'Document type' => 'receipt',
                    'Document Code' => $r->doc_code,
                    'Amount' => (float) ($r->amount ?? 0),
                    'Document status' => self::receiptVoucherDocumentStatusLabel($r->rv_approved, $r->rv_confirmed),
                ];
            }

            $matchingRows = self::customerInvoiceMatchingPaymentBaseQuery($subCompanies)
                ->whereIn('det.bookingInvCodeSystem', $invoiceIds)
                ->groupBy('det.bookingInvCodeSystem', 'm.matchDocumentMasterAutoID', 'm.matchingDocCode', 'm.matchingConfirmedYN')
                ->selectRaw('
                    det.bookingInvCodeSystem as invoice_id,
                    m.matchingDocCode as doc_code,
                    SUM(IFNULL(det.receiveAmountTrans,0)) as amount,
                    m.matchingConfirmedYN as matching_confirmed
                ')
                ->get();

            foreach ($matchingRows as $r) {
                $iid = (int) $r->invoice_id;
                if (! array_key_exists($iid, $byInvoice)) {
                    continue;
                }
                $byInvoice[$iid][] = [
                    'Document type' => 'matching',
                    'Document Code' => $r->doc_code,
                    'Amount' => (float) ($r->amount ?? 0),
                    'Document status' => self::matchingDocumentStatusLabel($r->matching_confirmed),
                ];
            }

            $returnRows = self::customerInvoiceSalesReturnBaseQuery($subCompanies)
                ->whereIn('srd.custInvoiceDirectAutoID', $invoiceIds)
                ->groupBy('srd.custInvoiceDirectAutoID', 'sr.id', 'sr.salesReturnCode', 'sr.approvedYN')
                ->selectRaw('
                    srd.custInvoiceDirectAutoID as invoice_id,
                    sr.salesReturnCode as doc_code,
                    SUM(IFNULL(srd.transactionAmount,0) + (IFNULL(srd.transactionAmount,0) * IFNULL(srd.VATPercentage,0) / 100)) as amount,
                    sr.approvedYN as sr_approved
                ')
                ->get();

            foreach ($returnRows as $r) {
                $iid = (int) $r->invoice_id;
                if (! array_key_exists($iid, $byInvoice)) {
                    continue;
                }
                $byInvoice[$iid][] = [
                    'Document type' => 'sales_return',
                    'Document Code' => $r->doc_code,
                    'Amount' => (float) ($r->amount ?? 0),
                    'Document status' => self::salesReturnDocumentStatusLabel($r->sr_approved),
                ];
            }

            return $byInvoice;
        };

        $mapBalanceItem = function (CustomerInvoiceDirect $invoice, array $docsByInvoice) use ($documentTypeLabels) {
            $iid = (int) $invoice->custInvoiceDirectAutoID;
            $dt = isset($invoice->isPerforma) ? (int) $invoice->isPerforma : -1;

            return [
                'Invoice Code' => $invoice->bookingInvCode,
                'Customer' => optional($invoice->customer)->CustomerName,
                'Invoice Type' => $documentTypeLabels[$dt] ?? '',
                'Document No' => $invoice->customerInvoiceNo,
                'Invoice Date' => $invoice->bookingDate,
                'Warehouse' => optional($invoice->warehouse)->wareHouseDescription,
                'Transaction Currency' => optional($invoice->currency)->CurrencyCode,
                'Due Date' => $invoice->invoiceDueDate,
                'Invoice Amount' => (float) ($invoice->invoice_amount ?? 0),
                'Balance Amount' => (float) ($invoice->balance_amount ?? 0),
                'Status' => [
                    'status' => $invoice->balance_payment_status ?? '',
                    'docs' => $docsByInvoice[$iid] ?? [],
                ],
                'Created Date & Time' => $invoice->createdDateAndTime,
                'Created By' => optional($invoice->createduser)->empName,
                'Last Updated Date & Time' => $invoice->timestamp,
                'Last Updated By' => optional($invoice->modified_by)->empName,
                'Generated From' => $invoice->generated_from_display ?: null,
            ];
        };

        $query->orderBy($invTable.'.custInvoiceDirectAutoID', 'desc');

        if ($request->has('page')) {
            $page = (int) $request->get('page', 1);
            $perPage = (int) ($request->get('per_page', 10));


            $paginator = (clone $query)->paginate($perPage, ['*'], 'page', $page);
            $invoiceIds = $paginator->getCollection()->pluck('custInvoiceDirectAutoID')->filter()->values()->all();
            $docsByInvoice = $loadStatusDetailsByInvoiceIds($invoiceIds);

            $paginator->setCollection(
                $paginator->getCollection()->map(function ($invoice) use ($docsByInvoice, $mapBalanceItem) {
                    return $mapBalanceItem($invoice, $docsByInvoice);
                })
            );

            return $this->sendResponse($paginator, 'Customer invoice balances retrieved successfully');
        }

        $rows = $query->get();
        $invoiceIds = $rows->pluck('custInvoiceDirectAutoID')->filter()->values()->all();
        $docsByInvoice = $loadStatusDetailsByInvoiceIds($invoiceIds);

        $data = $rows->map(function ($invoice) use ($docsByInvoice, $mapBalanceItem) {
            return $mapBalanceItem($invoice, $docsByInvoice);
        })->values();

        return $this->sendResponse($data, 'Customer invoice balances retrieved successfully');
    }


    private static function customerInvoiceGeneratedFromLabelSubQuery(): Builder
    {
        $dsmTable = (new DocumentSystemMapping)->getTable();
        $tpsTable = (new ThirdPartySystems)->getTable();
        $displayDescriptions = array_merge(
            self::thirdPartyDescriptionsForGeneratedFrom('POS'),
            self::thirdPartyDescriptionsForGeneratedFrom('CLUB')
        );

        $latestMappingIdPerDocument = DB::table($dsmTable)
            ->where('documentSystemId', 20)
            ->groupBy('documentId')
            ->selectRaw('documentId, MAX(id) as max_mapping_id');

        return DB::table($dsmTable.' as dsm')
            ->joinSub($latestMappingIdPerDocument, 'latest_map', 'latest_map.max_mapping_id', '=', 'dsm.id')
            ->join($tpsTable.' as tps', 'tps.id', '=', 'dsm.thirdPartySystemId')
            ->whereIn('tps.description', $displayDescriptions)
            ->select('dsm.documentId as documentId', 'tps.description as generated_from_label');
    }

 
    private static function customerInvoiceMappingDocumentIdsSubQuery(array $generatedFromList): Builder
    {
        $descriptions = [];
        foreach (array_unique($generatedFromList) as $flag) {
            $descriptions = array_merge($descriptions, self::thirdPartyDescriptionsForGeneratedFrom($flag));
        }
        $descriptions = array_values(array_unique($descriptions));

        $dsmTable = (new DocumentSystemMapping)->getTable();
        $tpsTable = (new ThirdPartySystems)->getTable();

        return DB::table($dsmTable.' as dsm')
            ->join($tpsTable.' as tps', 'tps.id', '=', 'dsm.thirdPartySystemId')
            ->where('dsm.documentSystemId', 20)
            ->whereIn('tps.description', $descriptions)
            ->select('dsm.documentId')
            ->distinct();
    }

    private static function customerInvoiceReceiptPaymentBaseQuery(array $subCompanies): Builder
    {
        $detTable = (new CustomerReceivePaymentDetail)->getTable();
        $rvTable = (new CustomerReceivePayment)->getTable();

        return CustomerReceivePaymentDetail::query()
            ->from($detTable.' as det')
            ->join($rvTable.' as rv', 'det.custReceivePaymentAutoID', '=', 'rv.custReceivePaymentAutoID')
            ->whereIn('det.companySystemID', $subCompanies)
            ->where('det.addedDocumentSystemID', 20)
            ->where(function ($qq) {
                $qq->whereNull('det.matchingDocID')->orWhere('det.matchingDocID', 0);
            })
            ->where('rv.approved', -1)
            ->toBase();
    }

    private static function customerInvoiceMatchingPaymentBaseQuery(array $subCompanies): Builder
    {
        $detTable = (new CustomerReceivePaymentDetail)->getTable();
        $mTable = (new MatchDocumentMaster)->getTable();

        return CustomerReceivePaymentDetail::query()
            ->from($detTable.' as det')
            ->join($mTable.' as m', function ($join) {
                $join->on('m.matchDocumentMasterAutoID', '=', 'det.matchingDocID')
                    ->on('m.companySystemID', '=', 'det.companySystemID');
            })
            ->whereIn('det.companySystemID', $subCompanies)
            ->where('det.addedDocumentSystemID', 20)
            ->where('det.matchingDocID', '>', 0)
            ->where('m.matchingConfirmedYN', 1)
            ->toBase();
    }

    private static function customerInvoiceSalesReturnBaseQuery(array $subCompanies): Builder
    {
        $srdTable = (new SalesReturnDetail)->getTable();
        $srTable = (new SalesReturn)->getTable();

        return SalesReturnDetail::query()
            ->from($srdTable.' as srd')
            ->join($srTable.' as sr', 'srd.salesReturnID', '=', 'sr.id')
            ->whereIn('srd.companySystemID', $subCompanies)
            ->where('sr.approvedYN', -1)
            ->toBase();
    }


    private static function thirdPartyDescriptionsForGeneratedFrom(string $generatedFrom): array
    {
        if ($generatedFrom === 'POS') {
            return ['GPOS', 'RPOS'];
        }
        if ($generatedFrom === 'CLUB') {
            return ['CBM'];
        }

        return [];
    }

    private static function receiptVoucherDocumentStatusLabel($rvApproved, $rvConfirmed): string
    {
        $approved = (int) ($rvApproved ?? 0) === -1;
        $confirmed = (int) ($rvConfirmed ?? 0) === 1;
        if ($approved && $confirmed) {
            return 'Approved & Confirmed';
        }
        if ($confirmed) {
            return 'Confirmed';
        }
        if ($approved) {
            return 'Approved';
        }

        return 'Pending';
    }

    private static function matchingDocumentStatusLabel($matchingConfirmed): string
    {
        return (int) ($matchingConfirmed ?? 0) === 1 ? 'Confirmed' : 'Pending';
    }

    private static function salesReturnDocumentStatusLabel($approvedYn): string
    {
        return (int) ($approvedYn ?? 0) === -1 ? 'Approved' : 'Pending';
    }
}
