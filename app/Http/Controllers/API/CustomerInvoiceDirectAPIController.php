<?php
/**
 * =============================================
 * -- File Name : GRVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Master
 * -- Author : Mohamed Nazir
 * -- Create date : 11-June 2018
 * -- Description : This file contains the all CRUD for GRV Master
 * -- REVISION HISTORY
 * -- Date: 13 Aug 2018 By: Shahmy Description: Added new functions named as getINVFormData() For load form View
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectAPIRequest;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\CompanyFinancePeriod;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerMaster;
use App\Models\PerformaDetails;
use App\Models\PerformaMaster;
use App\Models\Unit;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Months;
use App\Models\Taxdetail;
use App\Models\Company;
use App\Models\customercurrency;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\Contract;
use App\Models\chartOfAccount;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\EmployeesDepartment;
use App\Models\SegmentMaster;
use App\Models\FreeBillingMasterPerforma;
use App\Models\GRVMaster;
use App\Repositories\CustomerInvoiceDirectRepository;
use Carbon\Carbon;
use App\Models\BankMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CustomerInvoiceDirectController
 * @package App\Http\Controllers\API
 */
class CustomerInvoiceDirectAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirects",
     *      summary="Get a listing of the CustomerInvoiceDirects.",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Get all CustomerInvoiceDirects",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirect")
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
        $this->customerInvoiceDirectRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->all();

        return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice Directs retrieved successfully');
    }

    /**
     * @param CreateCustomerInvoiceDirectAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirects",
     *      summary="Store a newly created CustomerInvoiceDirect in storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Store CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirect that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirect")
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'companyFinanceYearID','custTransactionCurrencyID'));
        $companyFinanceYearID = $input['companyFinanceYearID'];
        $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
      /*  $currency = customercurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
        custTransactionCurrencyID*/
       /* if ($currency) {*/
            //$input['custTransactionCurrencyID'] = $currency->currencyID;
            $myCurr = $input['custTransactionCurrencyID'];

            $companyCurrency = \Helper::companyCurrency($myCurr);
            $companyCurrencyConversion = \Helper::currencyConversion($company['companySystemID'], $myCurr, $myCurr, 0);
            /*exchange added*/
            $input['custTransactionCurrencyER'] = 1;
            $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

            $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $company['CompanyID'])->where('isDefault', -1)->first();
            if ($bank) {
                $input['bankID'] = $bank->bankmasterAutoID;
                $bankAccount = BankAccount::where('companyID', $company['CompanyID'])->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $myCurr)->first();
                if($bankAccount){
                    $input['bankAccountID'] = $bankAccount->bankAccountAutoID;
                }

            }
      /*  }*/

        /* if ($customer->creditDays == 0 || $customer->creditDays == '') {
             return $this->sendResponse('e', $customer->CustomerName . ' - Credit days not mentioned for this customer');
         }*/


        /**/

        $serialNo = CustomerInvoiceDirect::select(DB::raw('IFNULL(MAX(serialNo),0)+1 as serialNo'))->where('documentID', 'INV')->where('companySystemID', $input['companyID'])->orderBy('serialNo', 'desc')->first();
        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $bookingInvCode = ($company['CompanyID'] . '\\' . $y . '\\INV' . str_pad($serialNo['serialNo'], 6, '0', STR_PAD_LEFT));

        $input['documentID'] = "INV";
        $input['documentSystemiD'] = 20;
        $input['bookingInvCode'] = $bookingInvCode;
        $input['serialNo'] = $serialNo['serialNo'];
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;
        $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
        $input['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $input['customerInvoiceDate'] = $input['bookingDate'];
        $input['companySystemID'] = $input['companyID'];
        $input['companyID'] = $company['CompanyID'];
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
        $input['documentType'] = 11;
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();


        if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
            $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
            return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice  saved successfully');
        } else {
            return $this->sendResponse('e', 'Document Date should be between financial period start date and end date');
        }


    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Display the specified CustomerInvoiceDirect",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Get CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
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
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->with(['company' => function ($query) {
            $query->select('CompanyName', 'companySystemID', 'isTaxYN');
        }, 'bankaccount', 'currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'grv'])->findWithoutFail($id);


        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }


        return $this->sendResponse($customerInvoiceDirect->toArray(), 'Customer Invoice Direct retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Update the specified CustomerInvoiceDirect in storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Update CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirect that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirect")
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);
        $detail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->get();
        $isPerforma = $customerInvoiceDirect->isPerforma;
        if ($isPerforma == 1) {
            $input = $this->convertArrayToSelectedValue($input, array('customerID', 'secondaryLogoCompanySystemID'));
        } else {
            $input = $this->convertArrayToSelectedValue($input, array('customerID', 'secondaryLogoCompanySystemID', 'custTransactionCurrencyID', 'bankID', 'bankAccountID'));
            $_post['custTransactionCurrencyID'] = $input['custTransactionCurrencyID'];
            $_post['bankID'] = $input['bankID'];
            $_post['bankAccountID'] = $input['bankAccountID'];

            if ($_post['custTransactionCurrencyID'] != $customerInvoiceDirect->custTransactionCurrencyID) {
                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the currency.', 500);
                } else {
                    $myCurr = $_post['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $_post['custTransactionCurrencyER'] = 1;
                    $_post['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $_post['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $_post['bankAccountID'] = NULL;

                }
            }

            if ($_post['bankID'] != $customerInvoiceDirect->bankID) {
                $_post['bankAccountID'] = NULL;
            }

        }


        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        }
        $input['departmentSystemID'] = 4;
        /*financial Year check*/
        $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYearCheck["success"]) {
            return $this->sendError($companyFinanceYearCheck["message"], 500);
        }
        /*financial Period check*/
        $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
        if (!$companyFinancePeriodCheck["success"]) {
            return $this->sendError($companyFinancePeriodCheck["message"], 500);
        }

        $_post['wanNO'] = $input['wanNO'];
        $_post['secondaryLogoCompanySystemID'] = $input['secondaryLogoCompanySystemID'];
        $_post['servicePeriod'] = $input['servicePeriod'];
        $_post['comments'] = $input['comments'];
        $_post['customerID'] = $input['customerID'];
        $_post['rigNo'] = $input['rigNo'];
        $_post['PONumber'] = $input['PONumber'];
        $_post['customerGRVAutoID'] = $input['customerGRVAutoID'];


        if ($input['secondaryLogoCompanySystemID'] != $customerInvoiceDirect->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompID'] != '') {
                $company = Company::select('companyLogo', 'CompanyID')->where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $_post['secondaryLogoCompID'] = $company->CompanyID;
                $_post['secondaryLogo'] = $company->companyLogo;
            } else {
                $_post['secondaryLogoCompID'] = NULL;
                $_post['secondaryLogo'] = NULL;
            }

        }

        if ($input['customerInvoiceNo'] != $customerInvoiceDirect->customerInvoiceNo) {
            $_post['customerInvoiceNo'] = $input['customerInvoiceNo'];
        }


        if ($input['customerID'] != $customerInvoiceDirect->customerID) {


            if (count($detail) > 0) {
                return $this->sendError('Invoice details exist. You can not change the customer.', 500);
            }
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            if ($customer->creditDays == 0 || $customer->creditDays == '') {
                return $this->sendError($customer->CustomerName . ' - Credit days not mentioned for this customer', 500);
            }

            /*if customer change*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $_post['customerGLCode'] = $customer->custGLaccount;
            $_post['customerGLSystemID'] = $customer->custGLAccountSystemID;
            $currency = customercurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
            if ($currency) {
                $_post['custTransactionCurrencyID'] = $currency->currencyID;
                $myCurr = $currency->currencyID;

                $companyCurrency = \Helper::companyCurrency($currency->currencyID);
                $companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $myCurr, $myCurr, 0);
                /*exchange added*/
                $_post['custTransactionCurrencyER'] = 1;
                $_post['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $_post['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $_post['bankID'] = null;
                $_post['bankAccountID'] = null;
                $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $customerInvoiceDirect->companyID)->where('isDefault', -1)->first();
                if ($bank) {
                    $_post['bankID'] = $bank->bankmasterAutoID;
                    $bankAccount = BankAccount::where('companyID', $customerInvoiceDirect->companyID)->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $currency->currencyID)->first();
                    $_post['bankAccountID'] = $bankAccount->bankAccountAutoID;
                }
            }
            /**/

        }


        if ($input['serviceStartDate'] != '' && $input['serviceEndDate'] != '') {
            $_post['serviceStartDate'] = Carbon::parse($input['serviceStartDate'])->format('Y-m-d') . ' 00:00:00';
            $_post['serviceEndDate'] = Carbon::parse($input['serviceEndDate'])->format('Y-m-d') . ' 00:00:00';
            if (($_post['serviceStartDate'] >= $_post['serviceEndDate'])) {
                return $this->sendError('Service start date can not be greater than service end date.', 500);
            }
        }

        $_post['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $_post['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';

        if (($_post['bookingDate'] >= $input['FYPeriodDateFrom']) && ($_post['bookingDate'] <= $input['FYPeriodDateTo'])) {

        } else {
            return $this->sendError('Document Date should be between financial period start date and end date.', 500);

        }

        if ($input['confirmedYN'] == 1) {
            if ($customerInvoiceDirect->confirmedYN == 0) {
                if ($isPerforma != 1) {

                    $messages = [

                        'custTransactionCurrencyID.required' => 'Currency is required.',
                        'bankID.required' => 'Bank is required.',
                        'bankAccountID.required' => 'Bank account is required.'

                    ];
                    $validator = \Validator::make($_post, [
                        'custTransactionCurrencyID' => 'required|numeric|min:1',
                        'bankID' => 'required|numeric|min:1',
                        'bankAccountID' => 'required|numeric|min:1'
                    ], $messages);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }


                }

                if (count($detail) == 0) {
                    return $this->sendError('You can not confirm. Invoice Details not found.', 500);
                } else {
                    $detailValidation = CustomerInvoiceDirectDetail::selectRaw("IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( unitOfMeasure IS NULL OR unitOfMeasure = '' OR unitOfMeasure = 0, null, 1 ) AS unitOfMeasure, IF ( invoiceQty IS NULL OR invoiceQty = '' OR invoiceQty = 0, null, 1 ) AS invoiceQty, IF ( contractID IS NULL OR contractID = '' OR contractID = 0, null, 1 ) AS contractID,
                    IF ( invoiceAmount IS NULL OR invoiceAmount = '' OR invoiceAmount = 0, null, 1 ) AS invoiceAmount,
                    IF ( unitCost IS NULL OR unitCost = '' OR unitCost = 0, null, 1 ) AS unitCost")->
                    where('custInvoiceDirectID', $id)
                        ->where(function ($query) {

                            $query->whereIn('serviceLineSystemID', [null, 0])
                                ->orwhereIn('unitOfMeasure', [null, 0])
                                ->orwhereIn('invoiceQty', [null, 0])
                                ->orwhereIn('contractID', [null, 0])
                                ->orwhereIn('invoiceAmount', [null, 0])
                                ->orwhereIn('unitCost', [null, 0]);
                        });


                    if (!empty($detailValidation->get()->toArray())) {


                        foreach ($detailValidation->get()->toArray() as $item) {

                            $validators = \Validator::make($item, [
                                'serviceLineSystemID' => 'required|numeric|min:1',
                                'unitOfMeasure' => 'required|numeric|min:1',
                                'invoiceQty' => 'required|numeric|min:1',
                                'contractID' => 'required|numeric|min:1',
                                'invoiceAmount' => 'required|numeric|min:1',
                                'unitCost' => 'required|numeric|min:1',
                            ], [

                                'serviceLineSystemID.required' => 'Department is required.',
                                'unitOfMeasure.required' => 'UOM is required.',
                                'invoiceQty.required' => 'Qty is required.',
                                'contractID.required' => 'Contract no. is required.',
                                'invoiceAmount.required' => 'Amount is required.',
                                'unitCost.required' => 'Unit cost is required.'

                            ]);

                            if ($validators->fails()) {
                                return $this->sendError($validators->messages(), 422);
                            }


                        }

                    }

                    /*  $employee=\Helper::getEmployeeInfo();
                      $input['createdPcID'] = getenv('COMPUTERNAME');
                      $input['confirmedByEmpID'] =  \Helper::getEmployeeID();
                      $input['confirmedByName'] = $employee->empName;
                      $input['confirmedDate'] = Carbon::now();
                      $input['confirmedByEmpSystemID'] = \Helper::getEmployeeSystemID();*/


                    $groupby = CustomerInvoiceDirectDetail::select('serviceLineCode')->where('custInvoiceDirectID', $id)->groupBy('serviceLineCode')->get();
                    $groupbycontract = CustomerInvoiceDirectDetail::select('contractID')->where('custInvoiceDirectID', $id)->groupBy('contractID')->get();

                    if (count($groupby) != 0 || count($groupby) != 0) {

                        if (count($groupby) > 1 || count($groupbycontract) > 1) {
                            return $this->sendError('You can not continue . multiple service line or contract exist in details.', 500);
                        } else {
                            $params = array('autoID' => $id,
                                'company' => $customerInvoiceDirect->companySystemID,
                                'document' => $customerInvoiceDirect->documentSystemiD,
                                'segment' => '',
                                'category' => '',
                                'amount' => ''
                            );
                            $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($_post, $id);
                            $confirm = \Helper::confirmDocument($params);
                            if (!$confirm["success"]) {

                                return $this->sendError($confirm["message"], 500);
                            } else {
                                return $this->sendResponse($customerInvoiceDirect->toArray(), 'Customer invoice confirmed successfully');
                            }
                        }
                    } else {
                        return $this->sendError('No invoice details found.', 500);
                    }

                }
            }
        } else {
            $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($_post, $id);

            return $this->sendResponse($_post, 'Invoice Updated Successfully ');
        }


    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Remove the specified CustomerInvoiceDirect from storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Delete CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
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
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $customerInvoiceDirect->delete();

        return $this->sendResponse($id, 'Customer Invoice Direct deleted successfully');
    }

    public function customerInvoiceDetails(request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        }, 'invoicedetails'
        => function ($query) {
                $query->with(['unit', 'department', 'performadetails' => function ($query) {
                    $query->with(['freebillingmaster' => function ($query) {
                        $query->with(['ticketmaster' => function ($query) {
                            $query->with(['field']);
                        }]);
                    }]);
                }]);
            }
        ])->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        } else {
            /*   $CustomerInvoiceDirectDetail = CustomerInvoiceDirectDetail::select('*')->where('custInvoiceDirectID', $id)->get();
               $data['data']['master'] = $customerInvoiceDirect;
               $data['data']['detail'] = $CustomerInvoiceDirectDetail;*/

            return $this->sendResponse($customerInvoiceDirect, 'Customer Invoice Direct deleted successfully');
        }
    }


    public function getINVFormData(Request $request)
    {
        $companyId = $request['companyId'];

        //$grvAutoID = $request['grvAutoID'];


        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = CustomerInvoiceDirect::select(DB::raw("YEAR(bookingDate) as year"))
            ->whereNotNull('bookingDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();


        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,

        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getCustomerInvoiceMasterView(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('invConfirmedYN', 'month', 'approved', 'year', 'isProforma'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $invMaster = DB::table('erp_custinvoicedirect')
            ->leftjoin('currencymaster', 'custTransactionCurrencyID', '=', 'currencyID')
            ->leftjoin('employees', 'erp_custinvoicedirect.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_custinvoicedirect.customerID')
            ->where('companySystemID', $input['companyId'])
            ->where('erp_custinvoicedirect.documentSystemID', $input['documentId']);


        /* $invMaster = CustomerInvoiceDirect::where('companySystemID', $input['companyId']);
         $invMaster->where('documentSystemID', $input['documentId']);
         $invMaster->with(['currency', 'createduser', 'customer']);*/


        if (array_key_exists('invConfirmedYN', $input)) {
            if (($input['invConfirmedYN'] == 0 || $input['invConfirmedYN'] == 1) && !is_null($input['invConfirmedYN'])) {
                $invMaster->where('erp_custinvoicedirect.confirmedYN', $input['invConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('erp_custinvoicedirect.approved', $input['approved']);
            }
        }
        if (array_key_exists('isProforma', $input)) {
            if (!is_null($input['isProforma'])) {
                $invMaster->where('isPerforma', $input['isProforma']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('bookingDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('bookingDate', '=', $input['year']);
            }
        }
        /*  if (array_key_exists('year', $input)) {
              if ($input['year'] && !is_null($input['year'])) {
                  $invoiceDate = $input['year'] . '-12-31';
                  if (array_key_exists('month', $input)) {
                      if ($input['month'] && !is_null($input['month'])) {
                          $invoiceDate = $input['year'] . '-' . $input['month'] . '-31';
                      }
                  }

                  $invMaster->where('bookingDate', '<=', $invoiceDate);

              }
          }*/


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->Where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                    ->orWhere('customerInvoiceNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        $request->request->remove('search.value');
        $invMaster->select('bookingInvCode', 'CurrencyCode', 'erp_custinvoicedirect.approvedDate', 'customerInvoiceNo', 'erp_custinvoicedirect.comments', 'empName', 'DecimalPlaces', 'erp_custinvoicedirect.confirmedYN', 'erp_custinvoicedirect.approved', 'custInvoiceDirectAutoID', 'customermaster.CustomerName', 'bookingAmountTrans', 'VATAmount');

        return \DataTables::of($invMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('custInvoiceDirectAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getcreateINVFormData(Request $request)
    {
        $companyId = $request['companyId'];
        $id = $request['id'];
        $bankID = isset($request['bankID']) ? $request['bankID'] : false;
        $type = isset($request['type']) ? $request['type'] : false;

        if($type=='getCurrency'){
            $customerID = $request['customerID'];
            $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
            return $this->sendResponse($output, 'Record retrieved successfully');
        }
        if ($id) {
            $master = customerInvoiceDirect::select('bankID', 'custTransactionCurrencyID','customerID')->where('custInvoiceDirectAutoID', $id)->first();
        }

        if (!$bankID && $id) {
            $bankID = $master->bankID;
        }

        $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companyId)->where('isAssigned', '-1')->where('isActive', '1')->get();
        $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
        $output['invoiceType'] = array(array('value' => 1, 'label' => 'Proforma Invoice'), array('value' => 0, 'label' => 'Direct Invoice'));
        $output['companyFinanceYear'] = \Helper::companyFinanceYear($companyId);
        $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companyId)->first();
        $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
        $output['yesNoSelectionForMinus'] = YesNoSelectionForMinus::all();
        $output['yesNoSelection'] = YesNoSelection::all();
        $output['tax'] = \DB::select("SELECT * FROM erp_taxmaster WHERE taxType=2 AND companyID='{$output['company']['CompanyID']}'");

        if ($id) {
            if ($master->customerID != '') {
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault', 'DecimalPlaces')->get();
            } else {
                $output['currencies'] = [];
            }

            /* $output['currencies'] = CurrencyMaster::all();*/
            $output['bankDropdown'] = BankAssign::where('isActive', 1)->where('isAssigned', -1)->where('companyID', $output['company']['CompanyID'])->get();
            $output['bankAccount'] = [];
            if ($bankID != '' && $master->custTransactionCurrencyID != '') {

                $output['bankAccount'] = BankAccount::where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $bankID)->where('accountCurrencyID', $master->custTransactionCurrencyID)->get();
            }

            $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->get();
            $output['uom'] = Unit::select('UnitID', 'UnitShortCode')->get();

        }


        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    function getCustomerInvoicePerformaDetails(Request $request)
    {

        if (request()->has('order') && $request['order'][0]['column'] == 0 && $request['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $id = $request['id'];
        $master = CustomerInvoiceDirect::select('customerID', 'companySystemID')->where('custInvoiceDirectAutoID', $id)->first();
        $PerformaMaster = PerformaMaster::with(['ticket' => function ($query) {
            $query->with(['rig']);
        }])->where('companySystemID', $master->companySystemID)->where('customerSystemID', $master->customerID)->where('performaStatus', 0)->where('PerformaOpConfirmed', 1);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $PerformaMaster = $PerformaMaster->where(function ($query) use ($search) {
                $query->where('PerformaCode', 'LIKE', "%{$search}%");

            });
        }

        return \DataTables::eloquent($PerformaMaster)
            /*  ->addColumn('Actions', $policy)*/
            ->order(function ($query) use ($request) {
                if (request()->has('order')) {
                    if ($request['order'][0]['column'] == 0) {
                        $query->orderBy('PerformaMasterID', $request['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);


        // $performaDetails=PerformaDetails::
    }

    public function saveCustomerinvoicePerforma(Request $request)
    {
        $custInvoiceDirectAutoID = $request['id'];
        $performaMasterID = $request['value'];

        /*get master*/
        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $bookingInvCode = $master->bookingInvCode;
        /*selectedPerformaMaster*/
        $performa = PerformaMaster::with(['ticket' => function ($query) {
            $query->with(['rig']);
        }])->where('companySystemID', $master->companySystemID)->where('customerSystemID', $master->customerID)->where('performaStatus', 0)->where('PerformaOpConfirmed', 1)->where('PerformaInvoiceNo', $performaMasterID)->first();
        if (empty($performa)) {
            return $this->sendResponse('e', 'Already pulled');
        }

        /*if bookinvoice not available create header*/
        if ($master->bookingInvCode == '' || $master->bookingInvCode == 0) {

            $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $master->companyFinanceYearID)->first();
            $serialNo = CustomerInvoiceDirect::select(DB::raw('IFNULL(MAX(serialNo),0)+1 as serialNo'))->where('documentID', 'INV')->where('companySystemID', $master->companySystemID)->orderBy('serialNo', 'desc')->first();
            $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));

            /*header*/
            $bookingInvCode = ($master->companyID . '\\' . $y . '\\INV' . str_pad($serialNo->serialNo, 6, '0', STR_PAD_LEFT));
            $upMaster['serialNo'] = $serialNo->serialNo;
            $upMaster['bookingInvCode'] = $bookingInvCode;
            $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($upMaster, $custInvoiceDirectAutoID);
        }

        /*get bank check bank details from performaDetails*/
        $bankAccountDetails = PerformaDetails::select('currencyID', 'bankID', 'accountID')->where('companyID', $master->companyID)->where('performaMasterID', $performaMasterID)->first();

        if (empty($bankAccountDetails)) {
            return $this->sendResponse('e', 'No details records found');
        }

        $detailsAlreadyExist = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();

        if (!empty($detailsAlreadyExist)) {
            return $this->sendResponse('e', 'Already a proforma added to this customer invoice');
        }

        $contract = Contract::select('contractUID', 'isRequiredStamp', 'paymentInDaysForJob')->where('CompanyID', $master->companyID)->where('ContractNumber', $performa->contractID)->first();


        $getRentalDetailFromFreeBilling = FreeBillingMasterPerforma::select('companyID', 'PerformaInvoiceNo', 'rentalStartDate', 'rentalEndDate')->where('companyID', $master->companyID)->where('PerformaInvoiceNo', $performaMasterID)->first();

        $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)->first();
        if (!empty($tax)) {
            return $this->sendResponse('e', 'Please delete tax details to continue');
        }
        if (!empty($contract)) {
            if ($contract->paymentInDaysForJob <= 0) {
                return $this->sendResponse('e', 'Payment Period is not updated in the contract. Please update and try again');
            }
            /*isRequiredStamp*/
            if ($contract->isRequiredStamp == -1) {
                if ($performa->clientAppPerformaType == 2 || $performa->clientAppPerformaType == 3) {

                } else {
                    return $this->sendResponse('e', 'Stamp / OT release not done in proforma');
                }
            }
        } else {
            return $this->sendResponse('e', 'Contract not exist.');

        }

        $invoiceExist = PerformaDetails::select('invoiceSsytemCode')->where('invoiceSsytemCode', $custInvoiceDirectAutoID)->where('performaMasterID', $performaMasterID)->first();
        if (!empty($invoiceExist)) {
            return $this->sendResponse('e', 'You cannot add this proforma to this invoice as this was previously added in invoice - ' . $bookingInvCode);
        }

        $myCurr = $bankAccountDetails->currencyID; /*currencyID*/
        $updatedInvoiceNo = PerformaDetails::select('*')->where('companyID', $master->companyID)->where('performaMasterID', $performaMasterID)->get();
        $companyCurrency = \Helper::companyCurrency($myCurr);

        $x = 0;
        if (!empty($updatedInvoiceNo)) {
            foreach ($updatedInvoiceNo as $updateInvoice) {
                $serviceLine = SegmentMaster::select('serviceLineSystemID')->where('ServiceLineCode', $updateInvoice->serviceLine)->first();
                $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('AccountCode', $updateInvoice->financeGLcode)->first();

                $companyCurrencyConversion = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $updateInvoice->totAmount);
                /*    trasToLocER,trasToRptER,transToBankER,reportingAmount,localAmount,documentAmount,bankAmount*/
                /*define input*/

                $addToCusInvDetails[$x]['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                $addToCusInvDetails[$x]['companyID'] = $master->companyID;
                $addToCusInvDetails[$x]['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                $addToCusInvDetails[$x]['serviceLineCode'] = $updateInvoice->serviceLine;
                $addToCusInvDetails[$x]['customerID'] = $updateInvoice->customerID;
                $addToCusInvDetails[$x]['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                $addToCusInvDetails[$x]['glCode'] = $updateInvoice->financeGLcode;
                $addToCusInvDetails[$x]['glCodeDes'] = $chartOfAccount->AccountDescription;
                $addToCusInvDetails[$x]['accountType'] = $chartOfAccount->catogaryBLorPL;
                $addToCusInvDetails[$x]['comments'] = ($chartOfAccount->comments == '' ? $chartOfAccount->AccountDescription : $master->comments);
                $addToCusInvDetails[$x]['invoiceAmountCurrency'] = $updateInvoice->currencyID;
                $addToCusInvDetails[$x]['invoiceAmountCurrencyER'] = 1;
                $addToCusInvDetails[$x]['unitOfMeasure'] = 7;
                $addToCusInvDetails[$x]['invoiceQty'] = 1;
                $addToCusInvDetails[$x]['unitCost'] = 1;
                $addToCusInvDetails[$x]['invoiceAmount'] = $updateInvoice->totAmount;

                $addToCusInvDetails[$x]['localCurrency'] = $companyCurrency->localcurrency->currencyID;
                $addToCusInvDetails[$x]['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $addToCusInvDetails[$x]['localAmount'] = $companyCurrencyConversion['localAmount'];
                $addToCusInvDetails[$x]['comRptCurrency'] = $companyCurrency->reportingcurrency->currencyID;
                $addToCusInvDetails[$x]['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                $addToCusInvDetails[$x]['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];
                $addToCusInvDetails[$x]['clientContractID'] = $updateInvoice->contractID;
                $addToCusInvDetails[$x]['contractID'] = $contract->contractUID;
                $addToCusInvDetails[$x]['performaMasterID'] = $performaMasterID;
                $x++;
            }


            $invNo['invoiceSsytemCode'] = $custInvoiceDirectAutoID; /*update in custinvoice*/
            $performaStatus['performaStatus'] = 1; /*performa master update*/

            /*bankDetails*/

            $bankdetails['bankID'] = $bankAccountDetails->bankID;
            $bankdetails['custTransactionCurrencyID'] = $bankAccountDetails['currencyID'];
            $bankdetails['bankAccountID'] = $bankAccountDetails->accountID;
            $bankdetails['customerInvoiceNo'] = $performa->PerformaCode;

            $companyCurrencyConversion = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, 0);
            /*exchange added*/
            $bankdetails['custTransactionCurrencyER'] = 1;
            $bankdetails['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $bankdetails['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $bankdetails['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
            $bankdetails['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

            $now = Carbon::now();
            $new_date = $now->addDays($contract->paymentInDaysForJob);


            $bankdetails['invoiceDueDate'] = $new_date;
            $bankdetails['paymentInDaysForJob'] = $contract->paymentInDaysForJob;
            $bankdetails['performaDate'] = $performa->performaDate;
            $bankdetails['rigNo'] = ($performa->ticket ? $performa->ticket->regNo . ' - ' . $performa->ticket->rig->RigDescription :'' ) ;
            $bankdetails['servicePeriod'] = "";
            $bankdetails['serviceStartDate'] = $getRentalDetailFromFreeBilling->rentalStartDate;
            $bankdetails['serviceEndDate'] = $getRentalDetailFromFreeBilling->rentalEndDate;
            /**/

            DB::beginTransaction();

            try {


                if (!empty($addToCusInvDetails)) {
                    foreach ($addToCusInvDetails as $item) {
                        CustomerInvoiceDirectDetail::create($item);
                    }
                }
                CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($bankdetails);
                PerformaMaster::where('companyID', $master->companyID)->where('PerformaInvoiceNo', $performaMasterID)->update($performaStatus);

                if (!empty($updatedInvoiceNo)) {
                    foreach ($updatedInvoiceNo as $peformaDet) {
                        PerformaDetails::where('companyID', $master->companyID)->where('performaMasterID', $performaMasterID)->where('idperformaDetails', $peformaDet->idperformaDetails)->update($invNo);
                    }
                }
                $details = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"), DB::raw("SUM(localAmount) as bookingAmountLocal"), DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first()->toArray();

                CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);


                DB::commit();
                return $this->sendResponse('s', 'successfully created');
            } catch (\Exception $exception) {
                DB::rollback();
                return $this->sendResponse('e', 'Error Occured !');
            }

        }


    }

    public function savecustomerInvoiceTaxDetails(Request $request)
    {
        $input = $request->all();
        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];
        $percentage = $input['percentage'];
        $taxMasterAutoID = $input['taxMasterAutoID'];

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $invoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (empty($invoiceDetail)) {
            return $this->sendResponse('e', 'Invoice Details not found.');
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);
        $totalDetail = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as amount"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }


        $totalAmount = ($percentage / 100) * $totalAmount;


        $taxMaster = \DB::select("SELECT * FROM erp_taxmaster WHERE taxType=2 AND companyID='{$master->companyID}'");

        if (empty($taxMaster)) {
            return $this->sendResponse('e', 'Tax Master not found');
        } else {
            $taxMaster = $taxMaster[0];
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)->first();
        if (!empty($Taxdetail)) {
            return $this->sendResponse('e', 'Tax Detail Already exist');
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = $taxMaster->taxShortCode;
        $_post['taxDescription'] = $taxMaster->taxDescription;
        $_post['taxPercent'] = $taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalAmount / $_post['localCurrencyER']);
                }
            }
        }
        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);


        DB::beginTransaction();
        try {
            Taxdetail::create($_post);
            $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

            $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
            $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
            $vatAmount['VATPercentage'] = $percentage;
            $vatAmount['VATAmount'] = $_post['amount'];
            $vatAmount['VATAmountLocal'] = $_post["localAmount"];
            $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
            DB::commit();
            return $this->sendResponse('s', 'Successfully Added');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('e', 'Error Occurred');
        }
    }

    public function AllDeleteCustomerInvoiceDetails(Request $request)
    {
        $id = $request['id'];
        $getPerformaMasterID = CustomerInvoiceDirectDetail::select('performaMasterID', 'companyID', 'custInvoiceDirectID')->where('custInvoiceDirectID', $id)->first();
        if (empty($getPerformaMasterID)) {
            return $this->sendResponse('e', 'No details found');
        }

        $peformaMasterID = $getPerformaMasterID->performaMasterID;


        $Taxdetail = Taxdetail::where('documentSystemCode', $id)->first();
        if (!empty($Taxdetail)) {
            return $this->sendResponse('e', 'Please delete tax details to continue');
        }

        DB::beginTransaction();
        try {
            PerformaMaster::where('companyID', $getPerformaMasterID->companyID)->where('PerformaInvoiceNo', $peformaMasterID)->update(array('performaStatus' => 0));

            PerformaDetails::where('companyID', $getPerformaMasterID->companyID)->where('PerformaMasterID', $peformaMasterID)->update(array('invoiceSsytemCode' => 0));
            CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->delete();

            $details = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $id)->first()->toArray();
            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $id)->update($details);
            DB::commit();
            return $this->sendResponse('s', 'Successfully Deleted');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('e', 'Error Occurred');
        }


    }

    public function printCustomerInvoice(Request $request)
    {

        $id = $request->get('id');
        $master=CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$id)->first();

        if($master->isPerforma==1){

            $customerInvoice = $this->customerInvoiceDirectRepository->getAudit($id);
        }else{

            $customerInvoice = $this->customerInvoiceDirectRepository->getAudit2($id);

        }




        if (empty($customerInvoice)) {
            return $this->sendError('Customer Invoice not found.');
        }


        $customerInvoice->docRefNo = \Helper::getCompanyDocRefNo($customerInvoice->companySystemID, $customerInvoice->documentSystemiD);

        $template=false;
        if($master->isPerforma==1){
           $detail= CustomerInvoiceDirectDetail::with(['contract'])->where('custInvoiceDirectID',$id)->first();
           $template= $detail->contract->performaTempID+1;
        }
         $customerInvoice->template =$template;


;

        $array = array('request' => $customerInvoice);
        $time = strtotime("now");
        $fileName = 'customer_invoice_' . $id . '_' . $time . '.pdf';
        $html = view('print.customer_invoice', $array);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
    }


    public function customerInvoiceReopen(Request $request)
    {
        $input = $request->all();

        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);
        $emails = array();
        if (empty($invoice)) {
            return $this->sendError('Invoice not found');
        }

        if ($invoice->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this invoice it is already partially approved');
        }

        if ($invoice->approved == -1) {
            return $this->sendError('You cannot reopen this invoice it is already fully approved');
        }

        if ($invoice->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this invoice, it is not confirmed');
        }

        // updating fields
        $invoice->confirmedYN = 0;
        $invoice->confirmedByEmpSystemID = null;
        $invoice->confirmedByEmpID = null;
        $invoice->confirmedByName = null;
        $invoice->confirmedDate = null;
        $invoice->RollLevForApp_curr = 1;
        $invoice->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $invoice->documentSystemiD)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $invoice->bookingInvCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $invoice->bookingInvCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $invoice->companySystemID)
            ->where('documentSystemCode', $invoice->custInvoiceDirectAutoID)
            ->where('documentSystemID', $invoice->documentSystemiD)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $invoice->companySystemID)
                    ->where('documentSystemID', $invoice->documentSystemiD)
                    ->first();



                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

               /* if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }*/

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        $deleteApproval = DocumentApproved::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $invoice->companySystemID)
            ->where('documentSystemID', $invoice->documentSystemiD)
            ->delete();

        return $this->sendResponse($invoice->toArray(), 'Invoice reopened successfully');
    }

    function customerInvoiceAudit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $gRVMaster = $this->customerInvoiceDirectRepository->with(['createduser', 'confirmed_by', 'cancelled_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 20);
        }, 'invoicedetails', 'company', 'currency', 'companydocumentattachment_by' => function ($query) {
            $query->where('documentSystemID', 20);
        }])->findWithoutFail($id);


        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'GRV retrieved successfully');
    }

    public function getAllcontractbyclient(request $request)
    {
        /*   $input = $request->all();
           $companyID = $input['companyID'];
           $serviceLineSystemID = $input['serviceLineSystemID'];
           $custInvDirDetAutoID = $input['custInvDirDetAutoID'];
           $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $custInvDirDetAutoID)->first();
           $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();
           $contract = Contract::select('contractUID', 'ContractNumber')->whereHas('segment', function ($query) use ($serviceLineSystemID) {
               $query->where('serviceLineSystemID', $serviceLineSystemID);
           })->where('companySystemID', $companyID)->where('clientID', $master->customerID);

           if($detail->contract !=''){
               $contractb = Contract::select('contractUID', 'ContractNumber')->where('contractUID',$detail->contractID);
               $contract->union($contractb)->get();
           }*/

        $input = $request->all();


        $custInvDirDetAutoID = $input['custInvDirDetAutoID'];
        $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $custInvDirDetAutoID)->first();
        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();
        /*    $contract = Contract::select('contractUID', 'ContractNumber')->whereHas('segment', function ($query) use ($serviceLineSystemID) {
                $query->where('serviceLineSystemID', $serviceLineSystemID);
            })->where('companySystemID', $companyID)->where('clientID', $master->customerID)->get();*/


        $contractID = 0;
        if ($detail->contractID != '' && $detail->contractID != 0) {
            $contractID = $detail->contractID;

        }

        $qry = "SELECT * FROM ( SELECT contractUID, ContractNumber FROM contractmaster WHERE ServiceLineCode = '{$detail->serviceLineCode}' AND companySystemID = $master->companySystemID AND clientID = $master->customerID UNION ALL SELECT contractUID, ContractNumber FROM contractmaster WHERE contractUID = $contractID ) t GROUP BY contractUID, ContractNumber";
        $contract = DB::select($qry);


        return $this->sendResponse($contract, 'Contract deleted successfully');
    }


}
