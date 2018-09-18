<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentDetailAPIRequest;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\CustomerReceivePayment;
use App\Repositories\CustomerReceivePaymentDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CustomerReceivePaymentDetailController
 * @package App\Http\Controllers\API
 */
class CustomerReceivePaymentDetailAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentDetailRepository */
    private $customerReceivePaymentDetailRepository;

    public function __construct(CustomerReceivePaymentDetailRepository $customerReceivePaymentDetailRepo)
    {
        $this->customerReceivePaymentDetailRepository = $customerReceivePaymentDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePaymentDetails",
     *      summary="Get a listing of the CustomerReceivePaymentDetails.",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Get all CustomerReceivePaymentDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerReceivePaymentDetail")
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
        $this->customerReceivePaymentDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerReceivePaymentDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->all();

        return $this->sendResponse($customerReceivePaymentDetails->toArray(), 'Customer Receive Payment Details retrieved successfully');
    }

    /**
     * @param CreateCustomerReceivePaymentDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerReceivePaymentDetails",
     *      summary="Store a newly created CustomerReceivePaymentDetail in storage",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Store CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePaymentDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePaymentDetail")
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
     *                  ref="#/definitions/CustomerReceivePaymentDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerReceivePaymentDetailAPIRequest $request)
    {
        $input = $request->all();

        $id= $input['id'];
        $value = $input['value'];
        $master = CustomerReceivePayment::where('custReceivePaymentAutoID',$input['id'])->first();
        $qry="SELECT erp_accountsreceivableledger.arAutoID, erp_accountsreceivableledger.documentCodeSystem AS bookingInvSystemCode, custTransCurrencyID, erp_accountsreceivableledger.custTransER,	erp_accountsreceivableledger.InvoiceNo, erp_accountsreceivableledger.localCurrencyID, erp_accountsreceivableledger.localER, erp_accountsreceivableledger.localAmount, erp_accountsreceivableledger.comRptCurrencyID, erp_accountsreceivableledger.comRptER, erp_accountsreceivableledger.comRptAmount, erp_accountsreceivableledger.companySystemID, erp_accountsreceivableledger.companyID, erp_accountsreceivableledger.documentSystemID AS addedDocumentSystemID, erp_accountsreceivableledger.documentID AS addedDocumentID, erp_accountsreceivableledger.documentCode AS bookingInvDocCode, erp_accountsreceivableledger.documentDate AS bookingInvoiceDate, erp_accountsreceivableledger.customerID, IFNULL( SumOfreceiveAmountTrans, 0 ) AS SumOfreceiveAmountTrans, CurrencyCode, DecimalPlaces, IFNULL( SumOfcustbalanceAmount, 0 ) AS SumOfcustbalanceAmount, IFNULL( matchedAmount, 0 ) AS matchedAmount, FALSE AS isChecked FROM erp_accountsreceivableledger LEFT JOIN ( SELECT erp_custreceivepaymentdet.arAutoID, Sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS SumOfreceiveAmountTrans, Sum( erp_custreceivepaymentdet.custbalanceAmount ) AS SumOfcustbalanceAmount FROM erp_custreceivepaymentdet WHERE companySystemID = $master->companySystemID GROUP BY erp_custreceivepaymentdet.arAutoID HAVING erp_custreceivepaymentdet.arAutoID IS NOT NULL ) sid ON sid.arAutoID = erp_accountsreceivableledger.arAutoID LEFT JOIN ( SELECT erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.companyID, erp_matchdocumentmaster.documentSystemID, erp_matchdocumentmaster.BPVcode, erp_matchdocumentmaster.BPVsupplierID, erp_matchdocumentmaster.supplierTransCurrencyID, erp_matchdocumentmaster.matchedAmount, erp_matchdocumentmaster.matchLocalAmount, erp_matchdocumentmaster.matchRptAmount, erp_matchdocumentmaster.matchingConfirmedYN FROM erp_matchdocumentmaster WHERE erp_matchdocumentmaster.companySystemID = $master->companySystemID AND erp_matchdocumentmaster.documentSystemID IN ( 20, 19 ) ) md ON md.documentSystemID = erp_accountsreceivableledger.documentSystemID AND md.PayMasterAutoId = erp_accountsreceivableledger.documentCodeSystem AND md.BPVsupplierID = erp_accountsreceivableledger.customerID AND md.supplierTransCurrencyID = custTransCurrencyID LEFT JOIN currencymaster ON custTransCurrencyID = currencymaster.currencyID WHERE erp_accountsreceivableledger.arAutoID = {$value}  AND  erp_accountsreceivableledger.documentDate < '{$master->custPaymentReceiveDate}' AND erp_accountsreceivableledger.selectedToPaymentInv = 0 AND erp_accountsreceivableledger.fullyInvoiced <> 2 AND erp_accountsreceivableledger.companySystemID = $master->companySystemID AND erp_accountsreceivableledger.customerID = $master->customerID AND erp_accountsreceivableledger.custTransCurrencyID = $master->custTransactionCurrencyID HAVING ROUND( SumOfcustbalanceAmount, DecimalPlaces ) > 0 ";

        $invMaster = DB::select($qry);
        if ( 0 < count( $invMaster ) ) {
            dd($invMaster);
        }else{
            $this->sendError('', 'Invoice Detail not found');
        }


        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->create($input);


        return $this->sendResponse($customerReceivePaymentDetails->toArray(), 'Customer Receive Payment Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePaymentDetails/{id}",
     *      summary="Display the specified CustomerReceivePaymentDetail",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Get CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentDetail",
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
     *                  ref="#/definitions/CustomerReceivePaymentDetail"
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
        /** @var CustomerReceivePaymentDetail $customerReceivePaymentDetail */
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            return $this->sendError('Customer Receive Payment Detail not found');
        }

        return $this->sendResponse($customerReceivePaymentDetail->toArray(), 'Customer Receive Payment Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerReceivePaymentDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerReceivePaymentDetails/{id}",
     *      summary="Update the specified CustomerReceivePaymentDetail in storage",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Update CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePaymentDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePaymentDetail")
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
     *                  ref="#/definitions/CustomerReceivePaymentDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerReceivePaymentDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerReceivePaymentDetail $customerReceivePaymentDetail */
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            return $this->sendError('Customer Receive Payment Detail not found');
        }

        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->update($input, $id);

        return $this->sendResponse($customerReceivePaymentDetail->toArray(), 'CustomerReceivePaymentDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerReceivePaymentDetails/{id}",
     *      summary="Remove the specified CustomerReceivePaymentDetail from storage",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Delete CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentDetail",
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
        /** @var CustomerReceivePaymentDetail $customerReceivePaymentDetail */
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            return $this->sendError('Customer Receive Payment Detail not found');
        }

        $customerReceivePaymentDetail->delete();

        return $this->sendResponse($id, 'Customer Receive Payment Detail deleted successfully');
    }

    public function saveReceiptVoucherUnAllocationsDetails(Request $request)
    {
        $input = $request->all();

        $custReceivePaymentAutoID = $input['custReceivePaymentAutoID'];


        $output = CustomerReceivePayment::where('custReceivePaymentAutoID',$custReceivePaymentAutoID)->first();
        $detail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID',$custReceivePaymentAutoID)->where('bookingInvCode',0)->first();
        if($detail){
            return $this->sendError('Unallocation detail is already exist');
        }
        $receiveAmountTrans = $input['receiveAmountTrans'];

        $data['custReceivePaymentAutoID']=$custReceivePaymentAutoID;
        $data['companySystemID']=$output->companySystemID;
        $data['companyID']=$output->companyID;
        $data['matchingDocID']=0;
        $data['bookingInvCode']=0;
        $data['comments']= $input['comments'];
        $data['custTransactionCurrencyID'] = $output->custTransactionCurrencyID;
        $data['custTransactionCurrencyER']  =  $output->custTransactionCurrencyER;
        $data['companyReportingCurrencyID']= $output->companyRptCurrencyID;
        $data['companyReportingER']=   $output->companyRptCurrencyER;
        $data['localCurrencyID']= $output->localCurrencyID;
        $data['localCurrencyER']= $output->localCurrencyER;
        $currency = \Helper::convertAmountToLocalRpt($output->documentSystemID,$output->custReceivePaymentAutoID,$receiveAmountTrans);
        $data['bookingAmountTrans']=$receiveAmountTrans;
        $data['bookingAmountLocal']=$currency['localAmount'];
        $data['bookingAmountRpt']=$currency['reportingAmount'];

        $data['custReceiveCurrencyER']=0;
        $data['custbalanceAmount']=0;
        $data['receiveAmountTrans']=$receiveAmountTrans;
        $data['receiveAmountLocal']=$currency['localAmount'];
        $data['receiveAmountRpt']=$currency['reportingAmount'];

        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->create($data);

        return $this->sendResponse('', 'Unallocation amount added successfully');
    }
}
