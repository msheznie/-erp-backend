<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteReceiptAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteReceiptAPIRequest;
use App\Models\CreditNoteReceipt;
use App\Models\CreditNote;
use App\Models\BankLedger;
use App\Repositories\CreditNoteReceiptRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerReceivePayment;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CreditNoteReceiptController
 * @package App\Http\Controllers\API
 */

class CreditNoteReceiptAPIController extends AppBaseController
{
    /** @var  CreditNoteReceiptRepository */
    private $creditNoteReceiptRepository;

    public function __construct(CreditNoteReceiptRepository $creditNoteReceiptRepo)
    {
        $this->creditNoteReceiptRepository = $creditNoteReceiptRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/creditNoteReceipts",
     *      summary="getCreditNoteReceiptList",
     *      tags={"CreditNoteReceipt"},
     *      description="Get all CreditNoteReceipts",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/CreditNoteReceipt")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $input = $request->all();

        $this->creditNoteReceiptRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteReceiptRepository->pushCriteria(new LimitOffsetCriteria($request));

        $creditNoteReceipts = [];
        
        if (isset($input['creditNoteAutoID']) && $input['creditNoteAutoID'] != '') {
            // with credit note and receipt vouchers add
            $creditNoteReceipts = $this->creditNoteReceiptRepository
                ->with(['creditNote', 'customerReceivePayment.currency'])
                ->findWhere(['creditNoteAutoID' => $input['creditNoteAutoID']]);
        }

        return $this->sendResponse($creditNoteReceipts, 'Credit Note Receipts retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/creditNoteReceipts",
     *      summary="createCreditNoteReceipt",
     *      tags={"CreditNoteReceipt"},
     *      description="Create CreditNoteReceipt",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CreditNoteReceipt"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteReceiptAPIRequest $request)
    {
        $input = $request->all();

        $createdReceipts = [];
        foreach ($input['selectedVouchers'] as $receiptData) {
            $receiptData['companySystemID'] = $input['companySystemID'];
            
            $creditNoteReceipt = $this->creditNoteReceiptRepository->create($receiptData);
            $createdReceipts[] = $creditNoteReceipt->toArray();
        }

        return $this->sendResponse($createdReceipts, 'Credit Note Receipts saved successfully');

    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/creditNoteReceipts/{id}",
     *      summary="getCreditNoteReceiptItem",
     *      tags={"CreditNoteReceipt"},
     *      description="Get CreditNoteReceipt",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CreditNoteReceipt",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CreditNoteReceipt"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CreditNoteReceipt $creditNoteReceipt */
        $creditNoteReceipt = $this->creditNoteReceiptRepository->findWithoutFail($id);

        if (empty($creditNoteReceipt)) {
            return $this->sendError('Credit Note Receipt not found');
        }

        return $this->sendResponse($creditNoteReceipt->toArray(), 'Credit Note Receipt retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/creditNoteReceipts/{id}",
     *      summary="updateCreditNoteReceipt",
     *      tags={"CreditNoteReceipt"},
     *      description="Update CreditNoteReceipt",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CreditNoteReceipt",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CreditNoteReceipt"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteReceiptAPIRequest $request)
    {
        $input = $request->all();

        /** @var CreditNoteReceipt $creditNoteReceipt */
        $creditNoteReceipt = $this->creditNoteReceiptRepository->findWithoutFail($id);

        if (empty($creditNoteReceipt)) {
            return $this->sendError('Credit Note Receipt not found');
        }

        $input['companySystemID'] = $input['companySystemID'];

        $creditNoteReceipt = $this->creditNoteReceiptRepository->update($input, $id);

        return $this->sendResponse($creditNoteReceipt->toArray(), 'CreditNoteReceipt updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/creditNoteReceipts/{id}",
     *      summary="deleteCreditNoteReceipt",
     *      tags={"CreditNoteReceipt"},
     *      description="Delete CreditNoteReceipt",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CreditNoteReceipt",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CreditNoteReceipt $creditNoteReceipt */
        $creditNoteReceipt = $this->creditNoteReceiptRepository->findWithoutFail($id);

        if (empty($creditNoteReceipt)) {
            return $this->sendError('Credit Note Receipt not found');
        }

        $creditNoteReceipt->delete();

        return $this->sendResponse(null,'Credit Note Receipt deleted successfully');
    }

    /**
     * Get Receipt Vouchers for Refund Credit Note
     * 
     * @param Request $request
     * @return Response
     */
    public function getReceiptVouchersForRefundCreditNote(Request $request)
    {
        $input = $request->all();

        $creditNoteAutoID = $input['creditNoteAutoID'];
        $creditNote = CreditNote::find($creditNoteAutoID);
        
        if (empty($creditNote)) {
            return $this->sendError(trans('custom.credit_note_not_found'));
        }

        // Get credit note details
        $customerID = $creditNote->customerID;
        $customerCurrencyID = $creditNote->customerCurrencyID;
        $companySystemID = $creditNote->companySystemID;

        $search = $request->input('search.value');
        
        $receiptVouchers = CustomerReceivePayment::with('currency')->select('erp_customerreceivepayment.*')
            ->selectRaw('IFNULL(SUM(erp_bankledger.payAmountBank), 0) as totalPayAmountBank')
            ->selectRaw('IFNULL(SUM(erp_creditnote_receipts.refundAmount), 0) as usedRefundAmount')
            ->selectRaw('(IFNULL(SUM(erp_bankledger.payAmountBank), 0) - IFNULL(SUM(erp_creditnote_receipts.refundAmount), 0)) as balanceAmount')
            ->leftJoin('erp_bankledger', function($join) use ($companySystemID) {
                $join->on('erp_bankledger.documentSystemCode', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID')
                     ->where('erp_bankledger.documentSystemID', 21)
                     ->where('erp_bankledger.companySystemID', $companySystemID);
            })
            ->leftJoin('erp_creditnote_receipts', function($join) use ($companySystemID) {
                $join->on('erp_creditnote_receipts.custReceivePaymentAutoID', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID')
                     ->where('erp_creditnote_receipts.companySystemID', $companySystemID);
            })
            ->where('erp_customerreceivepayment.companySystemID', $companySystemID)
            ->where('erp_customerreceivepayment.customerID', $customerID)
            ->where('erp_customerreceivepayment.custTransactionCurrencyID', $customerCurrencyID)
            ->where('erp_customerreceivepayment.approved', -1)
            ->where('erp_customerreceivepayment.documentType', 13)
            ->where('erp_customerreceivepayment.pdcChequeYN', 0);
        
        // Add search filter if search term is provided
        if ($search) {
            $search = str_replace("\\", "\\\\\\\\", $search);
            $receiptVouchers->where('erp_customerreceivepayment.custPaymentReceiveCode', 'LIKE', '%' . $search . '%');
        }
        
        $receiptVouchers = $receiptVouchers
            ->groupBy('erp_customerreceivepayment.custReceivePaymentAutoID')
            ->havingRaw('(IFNULL(SUM(erp_bankledger.payAmountBank), 0) - IFNULL(SUM(erp_creditnote_receipts.refundAmount), 0)) > 0')
            ->orderBy('erp_customerreceivepayment.custReceivePaymentAutoID', 'desc')
            ->get();

        $request->request->remove('order');
        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);
        $request->request->remove('search.value');

        return \DataTables::of($receiptVouchers)
            ->addIndexColumn()
            ->with('orderCondition', 'desc')
            ->make(true);
    }
}
