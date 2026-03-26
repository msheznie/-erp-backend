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
use App\Models\CustomerInvoice;
use App\Repositories\CustomerInvoiceRepository;
use App\Services\API\CustomerInvoiceAPIService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use App\Traits\DocumentSystemMappingTrait;

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
        $companyId = (int) $request->get('company_id', 0);
        if ($companyId <= 0) {
            return $this->sendAPIError('company_id is required', 422, []);
        }

        $invoiceCode = trim((string) $request->get('invoice_code', ''));
        $invoiceType = trim((string) $request->get('invoice_type', ''));
        $generatedFrom = trim((string) $request->get('generated_from', ''));
        $customerCode = trim((string) $request->get('customer_code', ''));

        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(500, $perPage));

        // Map invoice_type (human) -> documentType (int) used by erp_custinvoicedirect
        $typeMap = [
            'direct invoice' => 1,
            'item sales invoice' => 2,
            'from delivery order' => 3,
            'from sales order' => 4,
            'from quotation' => 5,
        ];

        // Payments (receipt voucher + matching) aggregated per invoice
        $paidSub = DB::query()->fromSub(function ($q) use ($companyId) {
            $receipt = DB::table('erp_custreceivepaymentdet as det')
                ->join('erp_customerreceivepayment as rv', 'det.custReceivePaymentAutoID', '=', 'rv.custReceivePaymentAutoID')
                ->where('det.companySystemID', $companyId)
                ->where('det.matchingDocID', 0)
                ->where('rv.approved', -1)
                ->selectRaw('det.bookingInvCodeSystem as invoice_id, SUM(IFNULL(det.receiveAmountTrans,0)) as paid_trans');

            $matching = DB::table('erp_custreceivepaymentdet as det')
                ->join('erp_matchdocumentmaster as m', function ($join) {
                    $join->on('m.matchDocumentMasterAutoID', '=', 'det.matchingDocID')
                        ->on('m.companySystemID', '=', 'det.companySystemID');
                })
                ->where('det.companySystemID', $companyId)
                ->where('m.matchingConfirmedYN', 1)
                ->selectRaw('det.bookingInvCodeSystem as invoice_id, SUM(IFNULL(det.receiveAmountTrans,0)) as paid_trans');

            $q->from($receipt->unionAll($matching), 'u')
                ->selectRaw('invoice_id, SUM(paid_trans) as paid_trans')
                ->groupBy('invoice_id');
        }, 'paid');

        // Sales return amounts aggregated per invoice (approved returns only)
        $returnSub = DB::table('salesreturndetails as srd')
            ->join('salesreturn as sr', 'srd.salesReturnID', '=', 'sr.id')
            ->where('srd.companySystemID', $companyId)
            ->where('sr.approvedYN', -1)
            ->selectRaw('srd.custInvoiceDirectAutoID as invoice_id, SUM(IFNULL(srd.transactionAmount,0) + (IFNULL(srd.transactionAmount,0) * IFNULL(srd.VATPercentage,0) / 100)) as return_trans')
            ->groupBy('srd.custInvoiceDirectAutoID');

        $query = DB::table('erp_custinvoicedirect as inv')
            ->leftJoin('customermaster as c', 'c.customerCodeSystem', '=', 'inv.customerID')
            ->leftJoinSub($paidSub, 'paid', 'paid.invoice_id', '=', 'inv.custInvoiceDirectAutoID')
            ->leftJoinSub($returnSub, 'ret', 'ret.invoice_id', '=', 'inv.custInvoiceDirectAutoID')
            ->where('inv.companySystemID', $companyId)
            ->where('inv.confirmedYN', 1)
            ->where('inv.approved', -1)
            ->where(function ($q) {
                $q->whereNull('inv.canceledYN')->orWhere('inv.canceledYN', 0);
            })
            ->selectRaw('
                inv.custInvoiceDirectAutoID as invoice_id,
                inv.bookingInvCode as invoice_code,
                inv.customerInvoiceNo as customer_invoice_no,
                inv.documentType as invoice_type_id,
                inv.bookingDate as invoice_date,
                inv.companySystemID as company_id,
                inv.bookingAmountTrans as invoice_amount,
                IFNULL(paid.paid_trans,0) as paid_amount,
                IFNULL(ret.return_trans,0) as return_amount,
                (IFNULL(inv.bookingAmountTrans,0) - IFNULL(paid.paid_trans,0) - IFNULL(ret.return_trans,0)) as balance_amount,
                c.CutomerCode as customer_code,
                c.CustomerName as customer_name
            ');

        if ($invoiceCode !== '') {
            $query->where('inv.bookingInvCode', $invoiceCode);
        }

        if ($customerCode !== '') {
            $query->where('c.CutomerCode', $customerCode);
        }

        if ($invoiceType !== '') {
            $key = strtolower($invoiceType);
            if (! array_key_exists($key, $typeMap)) {
                return $this->sendAPIError('Customer invoice Type not match with system', 422, []);
            }
            $query->where('inv.documentType', $typeMap[$key]);
        }

        // Best-effort: if provided, validate value but do not hard-filter unless mapping exists.
        if ($generatedFrom !== '') {
            $gf = strtolower($generatedFrom);
            if (! in_array($gf, ['pos generated', 'club generated'], true)) {
                return $this->sendAPIError('Generated From - Invoice Flag not match with system', 422, []);
            }
        }

        // Exact-match validation errors required by spec
        if ($invoiceCode !== '') {
            $exists = (clone $query)->exists();
            if (! $exists) {
                // Determine which error message to show
                $anyCode = DB::table('erp_custinvoicedirect')
                    ->where('companySystemID', $companyId)
                    ->where('bookingInvCode', $invoiceCode)
                    ->exists();
                if (! $anyCode) {
                    return $this->sendAPIError('Customer invoice not match with system', 422, []);
                }
                $approved = DB::table('erp_custinvoicedirect')
                    ->where('companySystemID', $companyId)
                    ->where('bookingInvCode', $invoiceCode)
                    ->where('confirmedYN', 1)
                    ->where('approved', -1)
                    ->exists();
                if (! $approved) {
                    return $this->sendAPIError('Customer invoice not fully approved .', 422, []);
                }
            }
        }

        if ($customerCode !== '') {
            $customerExists = DB::table('customermaster')
                ->where('CutomerCode', $customerCode)
                ->exists();
            if (! $customerExists) {
                return $this->sendAPIError('Customer code not match with system', 422, []);
            }
        }

        $paginator = $query->orderBy('inv.custInvoiceDirectAutoID', 'desc')->paginate($perPage);

        // Attach document-level status details (receipt voucher / matching) for the returned invoices
        $invoiceIds = collect($paginator->items())->pluck('invoice_id')->filter()->values()->all();
        $statusRows = [];
        if ($invoiceIds) {
            $statusRows = DB::select("
                SELECT
                    det.bookingInvCodeSystem AS invoice_id,
                    IF(det.matchingDocID = 0 OR det.matchingDocID IS NULL, rv.custPaymentReceiveCode, m.matchingDocCode) AS docCode,
                    IF(det.matchingDocID = 0 OR det.matchingDocID IS NULL, rv.custPaymentReceiveDate, m.matchingDocdate) AS docDate,
                    det.receiveAmountTrans AS amount,
                    rv.confirmedYN,
                    rv.approved,
                    m.matchingConfirmedYN
                FROM erp_custreceivepaymentdet det
                LEFT JOIN erp_customerreceivepayment rv ON det.custReceivePaymentAutoID = rv.custReceivePaymentAutoID
                LEFT JOIN erp_matchdocumentmaster m ON det.matchingDocID = m.matchDocumentMasterAutoID
                WHERE det.companySystemID = ?
                  AND det.bookingInvCodeSystem IN (" . implode(',', array_map('intval', $invoiceIds)) . ")
            ", [$companyId]);
        }
        $statusByInvoice = collect($statusRows)->groupBy('invoice_id')->map(fn($rows) => array_values(array_map(function ($r) {
            return [
                'doc_code' => $r->docCode ?? null,
                'doc_date' => $r->docDate ?? null,
                'amount' => (float) ($r->amount ?? 0),
            ];
        }, $rows->all())))->toArray();

        $data = array_map(function ($row) use ($statusByInvoice) {
            $row = (array) $row;
            $row['status_details'] = $statusByInvoice[$row['invoice_id']] ?? [];
            return $row;
        }, $paginator->items());

        return $this->sendResponse([
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'data' => $data,
        ], 'Customer invoice balances retrieved successfully');
    }
}
