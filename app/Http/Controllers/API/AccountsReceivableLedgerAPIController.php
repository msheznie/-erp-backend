<?php
/**
 * =============================================
 * -- File Name : AccountsReceivableLedgerAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Accounts Receivable
 * -- Author : Mubashir
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Accounts receivable ledger
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountsReceivableLedgerAPIRequest;
use App\Http\Requests\API\UpdateAccountsReceivableLedgerAPIRequest;
use App\Models\AccountsReceivableLedger;
use App\Models\CustomerReceivePayment;
use App\Repositories\AccountsReceivableLedgerRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class AccountsReceivableLedgerController
 * @package App\Http\Controllers\API
 */

class AccountsReceivableLedgerAPIController extends AppBaseController
{
    /** @var  AccountsReceivableLedgerRepository */
    private $accountsReceivableLedgerRepository;

    public function __construct(AccountsReceivableLedgerRepository $accountsReceivableLedgerRepo)
    {
        $this->accountsReceivableLedgerRepository = $accountsReceivableLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/accountsReceivableLedgers",
     *      summary="Get a listing of the AccountsReceivableLedgers.",
     *      tags={"AccountsReceivableLedger"},
     *      description="Get all AccountsReceivableLedgers",
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
     *                  @SWG\Items(ref="#/definitions/AccountsReceivableLedger")
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
        $this->accountsReceivableLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->accountsReceivableLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accountsReceivableLedgers = $this->accountsReceivableLedgerRepository->all();

        return $this->sendResponse($accountsReceivableLedgers->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.acc_receivable_ledger')]));
    }

    /**
     * @param CreateAccountsReceivableLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/accountsReceivableLedgers",
     *      summary="Store a newly created AccountsReceivableLedger in storage",
     *      tags={"AccountsReceivableLedger"},
     *      description="Store AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccountsReceivableLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccountsReceivableLedger")
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
     *                  ref="#/definitions/AccountsReceivableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAccountsReceivableLedgerAPIRequest $request)
    {
        $input = $request->all();

        $accountsReceivableLedgers = $this->accountsReceivableLedgerRepository->create($input);

        return $this->sendResponse($accountsReceivableLedgers->toArray(), trans('custom.save', ['attribute' => trans('custom.acc_receivable_ledger')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/accountsReceivableLedgers/{id}",
     *      summary="Display the specified AccountsReceivableLedger",
     *      tags={"AccountsReceivableLedger"},
     *      description="Get AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsReceivableLedger",
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
     *                  ref="#/definitions/AccountsReceivableLedger"
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
        /** @var AccountsReceivableLedger $accountsReceivableLedger */
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.acc_receivable_ledger')]));
        }

        return $this->sendResponse($accountsReceivableLedger->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.acc_receivable_ledger')]));
    }

    /**
     * @param int $id
     * @param UpdateAccountsReceivableLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/accountsReceivableLedgers/{id}",
     *      summary="Update the specified AccountsReceivableLedger in storage",
     *      tags={"AccountsReceivableLedger"},
     *      description="Update AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsReceivableLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccountsReceivableLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccountsReceivableLedger")
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
     *                  ref="#/definitions/AccountsReceivableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAccountsReceivableLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccountsReceivableLedger $accountsReceivableLedger */
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.acc_receivable_ledger')]));
        }

        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->update($input, $id);

        return $this->sendResponse($accountsReceivableLedger->toArray(), trans('custom.update', ['attribute' => trans('custom.acc_receivable_ledger')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/accountsReceivableLedgers/{id}",
     *      summary="Remove the specified AccountsReceivableLedger from storage",
     *      tags={"AccountsReceivableLedger"},
     *      description="Delete AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsReceivableLedger",
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
        /** @var AccountsReceivableLedger $accountsReceivableLedger */
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.acc_receivable_ledger')]));
        }

        $accountsReceivableLedger->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.acc_receivable_ledger')]));
    }

    public function getCustomerReceiptInvoices(Request $request){

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $master = CustomerReceivePayment::where('custReceivePaymentAutoID',$input['id'])->first();
        $filter='';
        $search = $request->input('search.value');
        if($search){
            $search = str_replace("\\", "\\\\\\\\", $search);
            $filter = " AND (( erp_accountsreceivableledger.documentCode LIKE '%{$search}%' OR erp_accountsreceivableledger.InvoiceNo LIKE '%{$search}%' )) ";
        }
        $custPaymentReceiveDate = Carbon::parse($master->custPaymentReceiveDate)->format('Y-m-d');
           $qry="SELECT
                    erp_accountsreceivableledger.arAutoID,
                    erp_accountsreceivableledger.documentCodeSystem AS bookingInvSystemCode,
                    custTransCurrencyID,
                    erp_accountsreceivableledger.custTransER,
                    erp_accountsreceivableledger.InvoiceNo,
                    erp_accountsreceivableledger.localCurrencyID,
                    erp_accountsreceivableledger.localER,
                    erp_accountsreceivableledger.localAmount,
                    erp_accountsreceivableledger.comRptCurrencyID,
                    erp_accountsreceivableledger.comRptER,
                    erp_accountsreceivableledger.comRptAmount,
                    erp_accountsreceivableledger.companySystemID,
                    erp_accountsreceivableledger.companyID,
                    erp_accountsreceivableledger.documentSystemID AS addedDocumentSystemID,
                    erp_accountsreceivableledger.documentID AS addedDocumentID,
                    erp_accountsreceivableledger.documentCode AS bookingInvDocCode,
                    erp_accountsreceivableledger.documentDate AS bookingInvoiceDate,
                    erp_accountsreceivableledger.customerID,
                    erp_accountsreceivableledger.serviceLineCode,
                erp_accountsreceivableledger.custInvoiceAmount as SumOfreceiveAmountTrans,
                    erp_accountsreceivableledger.localAmount as SumOfreceiveAmountLocal,
                        erp_accountsreceivableledger.comRptAmount as SumOfreceiveAmountRpt,
                    CurrencyCode,
                    DecimalPlaces,
                      erp_accountsreceivableledger.custInvoiceAmount-IFNULL( SumOfreceiveAmountTrans, 0 )-IFNULL( matchedAmount*-1, 0 ) - IFNULL( sumReturnTransactionAmount, 0) - IFNULL(sumReturnDEOTransactionAmount, 0) as balanceAmount,
                    IFNULL( matchedAmount, 0 ) AS matchedAmount,
                    FALSE AS isChecked 
                FROM
                    erp_accountsreceivableledger
                LEFT JOIN (
                    SELECT
                        erp_custreceivepaymentdet.arAutoID,
                        IFNULL(
                            Sum(
                                erp_custreceivepaymentdet.bookingAmountTrans
                            ),
                            0
                        ) AS SumOfsupplierPaymentAmount,
                        IFNULL(
                            Sum(
                                erp_custreceivepaymentdet.custbalanceAmount
                            ),
                            0
                        ) AS SumOfcustbalanceAmount,
                        IFNULL(Sum(erp_custreceivepaymentdet.receiveAmountTrans), 0) AS SumOfreceiveAmountTrans
                    FROM
                        erp_custreceivepaymentdet
                    GROUP BY
                        erp_custreceivepaymentdet.arAutoID
                ) sid ON sid.arAutoID = erp_accountsreceivableledger.arAutoID
                    LEFT JOIN (
                SELECT
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.companyID,
                    erp_matchdocumentmaster.documentSystemID,
                    erp_matchdocumentmaster.BPVcode,
                    erp_matchdocumentmaster.BPVsupplierID,
                    erp_matchdocumentmaster.supplierTransCurrencyID,
                    erp_matchdocumentmaster.serviceLineSystemID,
                    erp_matchdocumentmaster.matchingConfirmedYN ,
                       sum( erp_matchdocumentmaster.matchedAmount ) as matchedAmount,
                    sum( erp_matchdocumentmaster.matchLocalAmount ) as matchLocalAmount,
                    sum( erp_matchdocumentmaster.matchRptAmount ) as matchRptAmount
                FROM
                    erp_matchdocumentmaster 
                WHERE
                    erp_matchdocumentmaster.companySystemID = $master->companySystemID
                    AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                    AND erp_matchdocumentmaster.documentSystemID IN (  19 ) 
                    GROUP BY 	erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.companyID,
                    erp_matchdocumentmaster.documentSystemID,
                    erp_matchdocumentmaster.BPVcode,
                    erp_matchdocumentmaster.BPVsupplierID,
                    erp_matchdocumentmaster.supplierTransCurrencyID,
                    erp_matchdocumentmaster.serviceLineSystemID,
                    erp_matchdocumentmaster.matchingConfirmedYN 
                    ) md ON md.documentSystemID = erp_accountsreceivableledger.documentSystemID 
                    AND md.PayMasterAutoId = erp_accountsreceivableledger.documentCodeSystem 
                    AND md.BPVsupplierID = erp_accountsreceivableledger.customerID 
                    AND md.serviceLineSystemID = erp_accountsreceivableledger.serviceLineSystemID 
                    AND md.supplierTransCurrencyID = custTransCurrencyID
                    LEFT JOIN (
                SELECT 
                    salesreturndetails.custInvoiceDirectAutoID,
                    salesreturndetails.salesReturnID,
                    salesreturndetails.companySystemID,
                    sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
                    sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
                    sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
                    FROM salesreturndetails
                    LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
                WHERE
                    salesreturndetails.companySystemID = $master->companySystemID
                    AND salesreturn.approvedYN = -1
                    GROUP BY salesreturndetails.custInvoiceDirectAutoID
                    ) sr ON sr.custInvoiceDirectAutoID = erp_accountsreceivableledger.documentCodeSystem 
                    LEFT JOIN (
                SELECT 
                    salesreturndetails.deliveryOrderDetailID,
                    erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
                    salesreturndetails.salesReturnID,
                    salesreturndetails.companySystemID,
                    sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOTransactionAmount,
                    sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEOLocalAmount,
                    sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnDEORptAmount
                    FROM salesreturndetails
                    LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
                    INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
                    WHERE
                    salesreturndetails.companySystemID = $master->companySystemID
                    AND salesreturn.approvedYN = -1
                    AND salesreturndetails.deliveryOrderDetailID <> 0
                    GROUP BY salesreturndetails.deliveryOrderDetailID
            ) srDEO ON srDEO.custInvoiceDirectAutoID = erp_accountsreceivableledger.documentCodeSystem
                    LEFT JOIN currencymaster ON custTransCurrencyID = currencymaster.currencyID 
                WHERE
                    date(erp_accountsreceivableledger.documentDate) <= '{$custPaymentReceiveDate}'
                    {$filter}
                    AND erp_accountsreceivableledger.selectedToPaymentInv = 0
                    AND erp_accountsreceivableledger.documentType <> 13
                    AND erp_accountsreceivableledger.documentType <> 15
                    AND erp_accountsreceivableledger.fullyInvoiced <> 2
                    AND erp_accountsreceivableledger.companySystemID = $master->companySystemID 
                    AND erp_accountsreceivableledger.customerID = $master->customerID 
                    AND erp_accountsreceivableledger.custTransCurrencyID = $master->custTransactionCurrencyID 
                HAVING
                    ROUND(balanceAmount, DecimalPlaces) <> 0 ORDER BY documentDate $sort
                        ";

        //echo $qry;
        //exit();
        $invMaster = DB::select($qry);

        $col[0] = $input['order'][0]['column'];
        $col[1] = $input['order'][0]['dir'];
        $request->request->remove('order');
        $data['order'] = [];
        /*  $data['order'][0]['column'] = '';
          $data['order'][0]['dir'] = '';*/
        $data['search']['value'] = '';
        $request->merge($data);

        $request->request->remove('search.value');

        return \DataTables::of($invMaster)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
