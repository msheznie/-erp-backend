<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectDetailAPIRequest;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\SegmentMaster;
use App\Models\ChartOfAccount;
use App\Models\Taxdetail;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CustomerInvoiceDirectDetailController
 * @package App\Http\Controllers\API
 */
class CustomerInvoiceDirectDetailAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectDetailRepository */
    private $customerInvoiceDirectDetailRepository;

    public function __construct(CustomerInvoiceDirectDetailRepository $customerInvoiceDirectDetailRepo)
    {
        $this->customerInvoiceDirectDetailRepository = $customerInvoiceDirectDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectDetails",
     *      summary="Get a listing of the CustomerInvoiceDirectDetails.",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Get all CustomerInvoiceDirectDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirectDetail")
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
        $this->customerInvoiceDirectDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirectDetails = $this->customerInvoiceDirectDetailRepository->all();

        return $this->sendResponse($customerInvoiceDirectDetails->toArray(), 'Customer Invoice Direct Details retrieved successfully');
    }

    /**
     * @param CreateCustomerInvoiceDirectDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirectDetails",
     *      summary="Store a newly created CustomerInvoiceDirectDetail in storage",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Store CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectDetail")
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectDetailAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectDetails = $this->customerInvoiceDirectDetailRepository->create($input);

        return $this->sendResponse($customerInvoiceDirectDetails->toArray(), 'Customer Invoice Direct Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectDetails/{id}",
     *      summary="Display the specified CustomerInvoiceDirectDetail",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Get CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetail",
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetail"
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
        /** @var CustomerInvoiceDirectDetail $customerInvoiceDirectDetail */
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        return $this->sendResponse($customerInvoiceDirectDetail->toArray(), 'Customer Invoice Direct Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirectDetails/{id}",
     *      summary="Update the specified CustomerInvoiceDirectDetail in storage",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Update CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectDetail")
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirectDetail $customerInvoiceDirectDetail */
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirectDetail->toArray(), 'CustomerInvoiceDirectDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirectDetails/{id}",
     *      summary="Remove the specified CustomerInvoiceDirectDetail from storage",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Delete CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetail",
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


        /** @var CustomerInvoiceDirectDetail $customerInvoiceDirectDetail */
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }
        $masterID = $customerInvoiceDirectDetail->custInvoiceDirectID;
        $customerInvoiceDirectDetail->delete();

        $details = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $masterID)->first()->toArray();



        /* selectRaw*/


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $masterID)->update($details);

        return $this->sendResponse($id, 'Customer Invoice Direct Detail deleted successfully');
    }

    public function addDirectInvoiceDetails(Request $request)
    {


        $validator = \Validator::make($request->all(), [
            'companySystemID' => 'required|numeric|min:1',
            'contractID' => 'required|numeric|min:1',
            'unitID' => 'required|numeric|min:1',
            'qty' => 'required|numeric|min:1',
            'unitCost' => 'required|numeric|min:1',
            'custInvoiceDirectAutoID' => 'required|numeric|min:1',
            'glCode' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $amount = $request['amount'];
        $comments = $request['comments'];
        $companySystemID = $request['companySystemID'];
        $contractID = $request['contractID'];
        $custInvoiceDirectAutoID = $request['custInvoiceDirectAutoID'];
        $glCode = $request['glCode'];
        $qty = $request['qty'];
        $serviceLineSystemID = $request['serviceLineSystemID'];
        $unitCost = $request['unitCost'];
        $unitID = $request['unitID'];


        /*this*/


        /*get master*/
        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $bookingInvCode = $master->bookingInvCode;
        /*selectedPerformaMaster*/


        /*if bookinvoice not available create header*/
        if ($master->bookingInvCode == '' || $master->bookingInvCode == 0) {

            $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $master->companyFinanceYearID)->first();
            $serialNo = CustomerInvoiceDirect::select(DB::raw('IFNULL(MAX(serialNo),0)+1 as serialNo'))->where('documentID', 'INV')->where('companySystemID', $master->companySystemID)->orderBy('serialNo', 'desc')->first();
            $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));

            /*header*/
            $bookingInvCode = ($master->companyID . '\\' . $y . '\\INV' . str_pad($serialNo->serialNo, 6, '0', STR_PAD_LEFT));
            $upMaster['serialNo'] = $serialNo->serialNo;
            $upMaster['bookingInvCode'] = $bookingInvCode;
            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($upMaster);
        }


        $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')->where('CompanyID', $master->companyID)->where('contractUID', $contractID)->first();

        $detail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (count($detail) > 0) {
            if ($detail->serviceLineSystemID != $serviceLineSystemID || $contract->ContractNumber != $detail->clientContractID) {
                return $this->sendError('Different Service Line or Contract ID selected');
            }
        }

        $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)->first();
        if (!empty($tax)) {
            return $this->sendResponse('e', 'Please delete tax details to continue');
        }
        if (!empty($contract)) {
            if ($contract->paymentInDaysForJob <= 0) {
                return $this->sendError('Payment Period is not updated in the contract. Please update and try again');
            }
        } else {
            return $this->sendError('Contract not exist.');

        }


        $myCurr = $master->custTransactionCurrencyID; /*currencyID*/

        $companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $serviceLineSystemID)->first();
        $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();
        $totalAmount = $unitCost * $qty;

        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
        $addToCusInvDetails['companyID'] = $master->companyID;
        $addToCusInvDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
        $addToCusInvDetails['serviceLineCode'] = $serviceLine->ServiceLineCode;
        $addToCusInvDetails['customerID'] = $master->customerID;
        $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
        $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
        $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
        $addToCusInvDetails['comments'] = ($comments == '' ? $chartOfAccount->AccountDescription : $master->comments);
        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
        $addToCusInvDetails['invoiceAmountCurrencyER'] = 1;
        $addToCusInvDetails['unitOfMeasure'] = $unitID;
        $addToCusInvDetails['invoiceQty'] = $qty;
        $addToCusInvDetails['unitCost'] = $unitCost;
        $addToCusInvDetails['invoiceAmount'] = round($totalAmount, $decimal);

        $addToCusInvDetails['localCurrency'] = $companyCurrency->localcurrency->currencyID;
        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;
        /* $addToCusInvDetails['localAmount'] = $companyCurrencyConversion['localAmount'];*/
        $addToCusInvDetails['comRptCurrency'] = $companyCurrency->reportingcurrency->currencyID;
        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
        /* $addToCusInvDetails['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];*/
        $addToCusInvDetails['clientContractID'] = $contract->ContractNumber;

        /**/
        $MyRptAmount = 0;
        if ($master->custTransactionCurrencyID == $master->companyReportingCurrencyID) {
            $MyRptAmount = $totalAmount;
        } else {
            if ($master->companyReportingER > $master->custTransactionCurrencyER) {
                if ($master->companyReportingER > 1) {
                    $MyRptAmount = ($totalAmount / $master->companyReportingER);
                } else {
                    $MyRptAmount = ($totalAmount * $master->companyReportingER);
                }
            } else {
                if ($master->companyReportingER > 1) {
                    $MyRptAmount = ($totalAmount * $master->companyReportingER);
                } else {
                    $MyRptAmount = ($totalAmount / $master->companyReportingER);
                }
            }
        }
        $addToCusInvDetails["comRptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($master->custTransactionCurrencyID == $master->localCurrencyID) {
            $MyLocalAmount = $totalAmount;
        } else {
            if ($master->localCurrencyER > $master->custTransactionCurrencyER) {
                if ($master->localCurrencyER > 1) {
                    $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                } else {
                    $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                }
            } else {
                if ($master->localCurrencyER > 1) {
                    $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                } else {
                    $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                }
            }
        }
        $addToCusInvDetails["localAmount"] = \Helper::roundValue($MyLocalAmount);


        /**/


        DB::beginTransaction();

        try {
            CustomerInvoiceDirectDetail::create($addToCusInvDetails);
            $details = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"), DB::raw("SUM(localAmount) as bookingAmountLocal"), DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first()->toArray();

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);


            DB::commit();
            return $this->sendResponse('s', 'successfully created');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Error Occured !');
        }


        /*done*/


    }


}
