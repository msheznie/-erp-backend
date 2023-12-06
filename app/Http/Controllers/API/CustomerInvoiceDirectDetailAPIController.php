<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceDirectDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Invoice Direct Detail
 * -- Author : Mohamed Nazir
 * -- Create date : 09 - September 2018
 * -- Description : This file contains the all CRUD for Customer Invoice Direct Detail
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectDetailAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceDirect;
use App\helper\TaxService;
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\SegmentMaster;
use App\Models\ChartOfAccount;
use App\Models\Company;
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

        $master =  CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $masterID)->first();
        if($master->isPerforma != 2) {
            $resVat = $this->updateTotalVAT($customerInvoiceDirectDetail->custInvoiceDirectID);
            if (!$resVat['status']) {
                return $this->sendError($resVat['message']); 
             } 
        }


        return $this->sendResponse($id, 'Customer Invoice Direct Detail deleted successfully');
    }

    public function addDirectInvoiceDetails(Request $request)
    {


        $messages = [
            'companySystemID.required' => 'Company is required.',
            /*    'contractID.required' => 'The contract number is required.',*/
            /* 'unitID.required' => 'The unit is required.',*/
            /* 'qty.required' => 'The qty is required.',*/
            /* 'unitCost.required' => 'The unit cost is required.',*/
            'custInvoiceDirectAutoID.required' => 'ID is required.',
            'glCode.required' => 'GL Account is required.',
            /* 'serviceLineSystemID.required' => 'The department is required.',*/
        ];

        $validator = \Validator::make($request->all(), [
            'companySystemID' => 'required|numeric|min:1',
            /* 'contractID' => 'required|numeric|min:1',*/
            /*    'unitID' => 'required|numeric|min:1',*/
            /*  'qty' => 'required|numeric|min:1',*/
            /* 'unitCost' => 'required|numeric|min:1',*/
            'custInvoiceDirectAutoID' => 'required|numeric|min:1',
            'glCode' => 'required|numeric|min:1',
            /*     'serviceLineSystemID' => 'required|numeric|min:1',*/
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /* $amount = $request['amount'];
         $comments = $request['comments'];*/
        $companySystemID = $request['companySystemID'];
        /* $contractID = $request['contractID'];*/
        $custInvoiceDirectAutoID = $request['custInvoiceDirectAutoID'];
        $glCode = $request['glCode'];
        /* $qty = $request['qty'];*/
        /* $serviceLineSystemID = $request['serviceLineSystemID'];
         $unitCost = $request['unitCost'];
         $unitID = $request['unitID'];*/


        /*this*/


        /*get master*/
        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $bookingInvCode = $master->bookingInvCode;
        /*selectedPerformaMaster*/


        $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();
        if (!empty($tax)) {
            // return $this->sendError('Please delete tax details to continue !');
        }

        $myCurr = $master->custTransactionCurrencyID;
        /*currencyID*/

        //$companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        /*$serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $serviceLineSystemID)->first();*/
        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();
        $totalAmount = 0; //$unitCost * $qty;

        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
        $addToCusInvDetails['companyID'] = $master->companyID;
        /*  $addToCusInvDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;*/
        /*        $addToCusInvDetails['serviceLineCode'] = $serviceLine->ServiceLineCode;*/
        $addToCusInvDetails['customerID'] = $master->customerID;
        $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
        $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
        $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
        $addToCusInvDetails['comments'] = $master->comments;
        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
        $addToCusInvDetails['invoiceAmountCurrencyER'] = 1;
        /* $addToCusInvDetails['unitOfMeasure'] = $unitID;
         $addToCusInvDetails['invoiceQty'] = $qty;
         $addToCusInvDetails['unitCost'] = $unitCost;*/
        $addToCusInvDetails['invoiceAmount'] = round($totalAmount, $decimal);

        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
        $addToCusInvDetails["comRptAmount"] = 0; // \Helper::roundValue($MyRptAmount);
        $addToCusInvDetails["localAmount"] = 0; // \Helper::roundValue($MyLocalAmount);
        if($master->isPerforma==0){
            $addToCusInvDetails['unitOfMeasure'] = 7;
            $addToCusInvDetails['invoiceQty'] = 1;
        }

        if ($master->isVatEligible && $master->isPerforma != 2) {
            $vatDetails = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
            $addToCusInvDetails['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $addToCusInvDetails['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $addToCusInvDetails['VATPercentage'] = $vatDetails['percentage'];
        }

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

    }

    public function updateDirectInvoice(Request $request)
    {

        $input = $request->all();
        $input = array_except($input, array('unit', 'department','performadetails','contract', 'project'));
        $input = $this->convertArrayToValue($input);
        $id = $input['custInvDirDetAutoID'];

        $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $id)->first();


        if (empty($detail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();

        if (empty($master)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $tax = Taxdetail::where('documentSystemCode', $detail->custInvoiceDirectID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();

        if (!empty($tax)) {
            // return $this->sendError('Please delete tax details to continue');
        }


        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($master->documentSystemiD, $master->companySystemID, $id, $input, $master->customerID, $master->isPerforma);

        if (!$validateVATCategories['status']) {
            return $this->sendError($validateVATCategories['message'], 500, array('type' => 'vat'));
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
        }

        if ($input['contractID'] != $detail->contractID) {

            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob', 'contractStatus')
                ->where('CompanyID', $detail->companyID)
                ->where('contractUID', $input['contractID'])
                ->first();

            $input['clientContractID'] = $contract->ContractNumber;

            if (!empty($contract)) {
                if($contract->contractStatus != 6){
                    if ($contract->paymentInDaysForJob <= 0) {
                        return $this->sendError('Payment Period is not updated in the contract. Please update and try again');
                    }
                }
            } else {
                return $this->sendError('Contract not exist.');

            }
        }

        if (isset($input["discountPercentage"]) && $input["discountPercentage"] > 100) {
            return $this->sendError('Discount Percentage cannot be greater than 100 percentage');
        }

        if (isset($input["discountAmountLine"]) && isset($input['salesPrice']) && $input['discountAmountLine'] > $input['salesPrice']) {
            return $this->sendError('Discount amount cannot be greater than sales price');
        }

        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            $input['contractID'] = NULL;
            $input['clientContractID'] = NULL;
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        $input['invoiceQty']= ($input['invoiceQty'] != ''?$input['invoiceQty']:0);
        $input['salesPrice']= ($input['salesPrice'] != '' ? $input['salesPrice'] : 0);
        
        $input['salesPrice'] = floatval($input['salesPrice'] );

        if(isset($input['by']) && ($input['by'] == 'discountPercentage' || $input['by'] == 'discountAmountLine')){
            if ($input['by'] === 'discountPercentage') {
              $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            } else if ($input['by'] === 'discountAmountLine') {
                if($input['salesPrice'] > 0){
                    $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
                } else {
                    $input["discountPercentage"] = 0;
                }
            }
        } else {
            if ($input['discountPercentage'] != 0) {
              $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            } else if ($input['discountAmountLine'] != 0){
                if($input['salesPrice'] > 0){
                    $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
                } else {
                    $input["discountPercentage"] = 0;
                }
            }
        }

        $input['unitCost'] = $input['salesPrice'] - $input["discountAmountLine"];
        if ($input['invoiceQty'] != $detail->invoiceQty || $input['unitCost'] != $detail->unitCost) {
            $myCurr = $master->custTransactionCurrencyID;               /*currencyID*/
            //$companyCurrency = \Helper::companyCurrency($myCurr);
            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

            $input['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
            $input['invoiceAmountCurrencyER'] = 1;
            $totalAmount = ($input['unitCost'] != ''?$input['unitCost']:0) * ($input['invoiceQty'] != ''?$input['invoiceQty']:0);
            $input['invoiceAmount'] = round($totalAmount, $decimal);
            
            if($master->isPerforma == 2) {
                $totalAmount = $input['salesPrice'];
                $input['invoiceAmount'] = round($input['salesPrice'], $decimal);
            }

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
            $input["comRptAmount"] =   \Helper::roundValue($MyRptAmount);
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
            $input["localAmount"] =  \Helper::roundValue($MyLocalAmount);


        }

        if(isset($input['by']) && ($input['by'] == 'VATPercentage' || $input['by'] == 'VATAmount')){
            if ($input['by'] === 'VATPercentage') {
              $input["VATAmount"] = $input['unitCost'] * $input["VATPercentage"] / 100;
            } else if ($input['by'] === 'VATAmount') {
                if($input['unitCost'] > 0){
                    $input["VATPercentage"] = ($input["VATAmount"] / $input['unitCost']) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        } else {
            if ($input['VATPercentage'] != 0) {
              $input["VATAmount"] = $input['unitCost'] * $input["VATPercentage"] / 100;
            } else if ($input['VATAmount'] != 0){
                if($input['unitCost'] > 0){
                    $input["VATPercentage"] = ($input["VATAmount"] / $input['unitCost']) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        }

        $currencyConversionVAT = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $input['VATAmount']);
        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;
        if($policy == true) {
            $input['VATAmountLocal'] = \Helper::roundValue($input["VATAmount"] / $master->localCurrencyER);
            $input['VATAmountRpt'] = \Helper::roundValue($input["VATAmount"] / $master->companyReportingER);
        }
        if($policy == false) {
            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }
        if (isset($input['by'])) {
            unset($input['by']);
        }

        if (isset($input['vatMasterCategoryAutoID'])) {
            unset($input['vatMasterCategoryAutoID']);
        }

        if (isset($input['itemPrimaryCode'])) {
            unset($input['itemPrimaryCode']);
        }
        
        if (isset($input['itemDescription'])) {
            unset($input['itemDescription']);
        }

        if (isset($input['subCategoryArray'])) {
            unset($input['subCategoryArray']);
        }

        if (isset($input['subCatgeoryType'])) {
            unset($input['subCatgeoryType']);
        }

        if (isset($input['exempt_vat_portion'])) {
            unset($input['exempt_vat_portion']);
        }

        DB::beginTransaction();

        try {
            $x=CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $detail->custInvDirDetAutoID)->update($input);
            $allDetail = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $detail->custInvoiceDirectID)->first()->toArray();

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->update($allDetail);

            if($master->isPerforma != 2) {
                $resVat = $this->updateTotalVAT($master->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 
            }

            DB::commit();
            return $this->sendResponse('s', 'successfully created');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception);
        }

    }

    public function updateTotalVAT($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)
                                                    ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->invoiceQty * $value->VATAmount;
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                              ->where('documentSystemID', 20)
                              ->delete();

        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']]; 
            } 
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }


        return ['status' => true];
    }

     public function savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Customer Invoice not found.'];
        }

        $invoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Invoice Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $totalDetail = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as amount"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemID'] = $master->documentSystemiD;
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);
       
        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }
}
