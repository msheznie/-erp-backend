<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceDirectAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Invoice
 * -- Author : Mohamed Shahmy
 * -- Create date : 11-June 2018
 * -- Description : This file contains the all CRUD for Customer Invoice
 * -- REVISION HISTORY
 * -- Date: 13 Aug 2018 By: Shahmy Description: Added new functions named as getINVFormData() For load form View
 * -- Date: 18 November 2018 By: Nazir Description: Added new functions named as getAllcontractbyclientbase()
 * -- Date: 27 November 2018 By: Nazir Description: Added new functions named as getCustomerInvoiceApproval()
 * -- Date: 27 November 2018 By: Nazir Description: Added new functions named as getApprovedCustomerInvoiceForCurrentUser()
 * -- Date: 28 November 2018 By: Nazir Description: Added new functions named as approveCustomerInvoice()
 * -- Date: 28 November 2018 By: Nazir Description: Added new functions named as rejectCustomerInvoice()
 * -- Date: 28 November 2018 By: Nazir Description: Added new functions named as getCustomerInvoiceAmend()
 * -- Date: 01 January 2019 By: Nazir Description: Added new functions named as customerInvoiceCancel()
 * -- Date: 11 January 2019 By: Mubashir Description: Added new functions named as approvalPreCheckCustomerInvoice()
 * -- Date: 06 February 2019 By: Fayas Description: Added new functions named as updateCustomerInvoiceGRV()
 * -- Date: 13 June 2019 By: Fayas Description: Added new functions named as amendCustomerInvoiceReview()
 */

namespace App\Http\Controllers\API;

use App\Constants\ContractMasterType;
use App\helper\CreateExcel;
use App\helper\CustomerInvoiceService;
use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateCustomerInvoiceDirectAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectAPIRequest;
use App\Models\AccountsReceivableLedger;
use App\Models\BankAccount;
use App\Models\CustomerInvoiceUploadDetail;
use App\Models\DocumentSystemMapping;
use App\Models\ErpProjectMaster;
use App\Models\BankAssign;
use App\Models\QuotationMaster;
use App\Models\QuotationDetails;
use App\Models\BookInvSuppMaster;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceDirectDetRefferedback;
use App\Models\CustomerInvoiceDirectRefferedback;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceItemDetailsRefferedback;
use App\Models\CustomerInvoiceStatusType;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\FreeBillingMasterPerforma;
use App\Models\GeneralLedger;
use App\Models\Months;
use App\Models\PerformaDetails;
use App\Models\PerformaMaster;
use App\Models\SegmentMaster;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\TaxMaster;
use App\Models\TicketMaster;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\ErpDocumentTemplate;
use App\Models\SecondaryCompany;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Services\API\CustomerInvoiceAPIService;
use App\Services\ChartOfAccountValidationService;
use App\Services\UserTypeService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Storage;
use App\helper\ItemTracking;
use App\Jobs\CustomerInvoiceUpload\CustomerInvoiceUpload;
use App\Models\CustomerContactDetails;
use App\Models\CustomerInvoiceLogistic;
use App\Models\DeliveryTermsMaster;
use App\Models\LogUploadCustomerInvoice;
use App\Models\PortMaster;
use App\Models\UploadCustomerInvoice;
use App\Services\CustomerInvoiceServices;
use App\Services\ValidateDocumentAmend;
use PHPExcel_IOFactory;
use Exception;
/**
 * Class CustomerInvoiceDirectController
 * @package App\Http\Controllers\API
 */
class CustomerInvoiceDirectAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;
    private $vatReturnFillingMasterRepo;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo,VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
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
    public function store(Request $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'companyFinanceYearID', 'custTransactionCurrencyID'));

        if (isset($input['isPerforma']) && $input['isPerforma'] == 2) {
            $wareHouse = isset($input['wareHouseSystemCode']) ? $input['wareHouseSystemCode'] : 0;
            if (!$wareHouse) {
                return $this->sendError('Please select a warehouse', 500);
            }
        }

        if (!isset($input['custTransactionCurrencyID']) || (isset($input['custTransactionCurrencyID']) && ($input['custTransactionCurrencyID'] == 0 || $input['custTransactionCurrencyID'] == null))) {
            return $this->sendError('Please select a currency', 500);
        }

        if (!isset($input['companyFinanceYearID']) || is_null($input['companyFinanceYearID'])) {
            return $this->sendError('Financial year is not selected', 500);
        }

        if (!isset($input['companyFinancePeriodID']) || is_null($input['companyFinancePeriodID'])) {
            return $this->sendError('Financial period is not selected', 500);
        }

        $data = CustomerInvoiceAPIService::customerInvoiceStore($input);

        if($data['status']){
            return $this->sendResponse($data['data'],$data['message']);
        }
        else{
            return $this->sendError($data['message']);
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
        }, 'bankaccount', 'currency', 'report_currency', 'local_currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'grv','customer','warehouse','segment'])->findWithoutFail($id);


        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

         
        $customerInvoiceLogisticData = [
            'consignee_address'=>$customerInvoiceDirect->customer->consignee_address, 
            'consignee_contact_no'=>$customerInvoiceDirect->customer->consignee_contact_no, 
            'consignee_name'=>$customerInvoiceDirect->customer->consignee_name, 
            'payment_terms'=>$customerInvoiceDirect->customer->payment_terms,
            'custInvoiceDirectAutoID'=>$id
        ];

        $invoiceLogistic = CustomerInvoiceLogistic::where('custInvoiceDirectAutoID',$id)->first();
        if($invoiceLogistic){
            $customerInvoiceLogistic = CustomerInvoiceLogistic::where('id', $invoiceLogistic['id'])->update($customerInvoiceLogisticData);
        } else {
            $customerInvoiceLogistic = CustomerInvoiceLogistic::create($customerInvoiceLogisticData);
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
    public function update($id,Request $request)
    {
        $input = $request->all();

        $customerInvoiceDirect = CustomerInvoiceDirect::find($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found',500);
        }

        $isPerforma = $customerInvoiceDirect->isPerforma;

        if ($isPerforma == 1) {
            $input = $this->convertArrayToSelectedValue($input, array('customerID', 'secondaryLogoCompanySystemID', 'companyFinancePeriodID', 'companyFinanceYearID','isPerforma'));
        }
        else {
            $input = $this->convertArrayToSelectedValue($input, array('customerID', 'secondaryLogoCompanySystemID', 'custTransactionCurrencyID', 'bankID', 'bankAccountID', 'companyFinancePeriodID', 'companyFinanceYearID', 'wareHouseSystemCode', 'serviceLineSystemID', 'isPerforma'));
        }

        $customerInvoiceUpdate = CustomerInvoiceAPIService::customerInvoiceUpdate($id, $input);

        if($customerInvoiceUpdate['status']){
            return $this->sendResponse($customerInvoiceUpdate['data'],$customerInvoiceUpdate['message']);
        }
        else{
            return $this->sendError(
                $customerInvoiceUpdate['message'],
                $customerInvoiceUpdate['code'] ?? 404,
                $customerInvoiceUpdate['type'] ?? array('type' => '')
            );
        }
    }


    public function updateCurrency($id, UpdateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        }

        $isPerforma = $customerInvoiceDirect->isPerforma;

        if ($isPerforma == 2 || $isPerforma == 3 || $isPerforma == 4|| $isPerforma == 5) {
            $detail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)->get();
        } else {
            $detail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->get();
        }




        if ($isPerforma == 1) {
            $input = $this->convertArrayToSelectedValue($input, array('customerID', 'secondaryLogoCompanySystemID', 'companyFinancePeriodID', 'companyFinanceYearID','isPerforma'));
        } else {
            $input = $this->convertArrayToSelectedValue($input, array('customerID', 'secondaryLogoCompanySystemID', 'custTransactionCurrencyID', 'bankID', 'bankAccountID', 'companyFinancePeriodID', 'companyFinanceYearID', 'wareHouseSystemCode', 'serviceLineSystemID', 'isPerforma'));
            if (isset($input['isPerforma']) && ($input['isPerforma'] == 2 || $input['isPerforma'] == 3|| $input['isPerforma'] == 4|| $input['isPerforma'] == 5)) {
                $wareHouse = isset($input['wareHouseSystemCode']) ? $input['wareHouseSystemCode'] : 0;

                if (!$wareHouse) {
                    return $this->sendError('Please select a warehouse', 500);
                }
                $_post['wareHouseSystemCode'] = $input['wareHouseSystemCode'];


                $serviceLine = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : 0;
                if (!$serviceLine) {
                    return $this->sendError('Please select a Segment', 500);
                }
                $segment = SegmentMaster::find($input['serviceLineSystemID']);
                $_post['serviceLineSystemID'] = $input['serviceLineSystemID'];
                $_post['serviceLineCode'] = isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null;
            }


            $_post['custTransactionCurrencyID'] = $input['custTransactionCurrencyID'];
            $_post['bankID'] = $input['bankID'];
            $_post['bankAccountID'] = $input['bankAccountID'];

            if ($_post['custTransactionCurrencyID'] != $customerInvoiceDirect->custTransactionCurrencyID) {
                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You cannot change the currency.', 500);
                } else {
                    $myCurr = $_post['custTransactionCurrencyID'];
                    //$companyCurrency = \Helper::companyCurrency($customerInvoiceDirect->companySystemID);
                    //$companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $_post['custTransactionCurrencyER'] = 1;
                    /* $_post['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                     $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                     $_post['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                     $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];*/
                    $_post['bankAccountID'] = NULL;

                }
            }

            /*if ($_post['bankID'] != $customerInvoiceDirect->bankID) {
                $_post['bankAccountID'] = NULL;
            }*/

        }

        if ($customerInvoiceDirect->customerCodeSystem != $input['customerID']) {
            $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
                                                    ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                                                    ->first();
            if ($customerGLCodeUpdate) {
                $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
            }
        }

        $_post['customerVATEligible'] = $input['customerVATEligible'];

        $input['departmentSystemID'] = 4;
        /*financial Year check*/
        if ($isPerforma == 0) {
            $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYearCheck["success"]) {
                return $this->sendError($companyFinanceYearCheck["message"], 500);
            }
        }

        if ($isPerforma == 0) {
            /*financial Period check*/
            $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
            if (!$companyFinancePeriodCheck["success"]) {
                return $this->sendError($companyFinancePeriodCheck["message"], 500);
            }
        }
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $_post['companyFinancePeriodID'] = $input['companyFinancePeriodID'];

        $_post['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $_post['FYEnd'] = $CompanyFinanceYear->endingDate;
        $_post['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $_post['FYPeriodDateTo'] = $FYPeriodDateTo;
        $_post['companyFinancePeriodID'] = $input['companyFinancePeriodID'];
        $_post['companyFinanceYearID'] = $input['companyFinanceYearID'];
        $_post['wanNO'] = $input['wanNO'];
        $_post['secondaryLogoCompanySystemID'] = isset($input['secondaryLogoCompanySystemID']) ? $input['secondaryLogoCompanySystemID'] : null;
        $_post['servicePeriod'] = $input['servicePeriod'];
        $_post['comments'] = $input['comments'];
        $_post['customerID'] = $input['customerID'];
        $_post['rigNo'] = $input['rigNo'];
        $_post['PONumber'] = $input['PONumber'];
        $_post['customerGRVAutoID'] = $input['customerGRVAutoID'];
        $_post['isPerforma'] = $input['isPerforma'];

        if (isset($input['customerGRVAutoID']) && $input['customerGRVAutoID']) {
            $checkGrv = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $id)
                ->where('customerGRVAutoID', $input['customerGRVAutoID'])
                ->first();

            if (!empty($checkGrv)) {
                return $this->sendError('Selected GRV is already assigned to ' . $checkGrv->bookingInvCode, 500, array('type' => 'grvAssigned'));
            }
        } else {
            $input['customerGRVAutoID'] = null;
        }


        if (isset($input['secondaryLogoCompanySystemID']) && $input['secondaryLogoCompanySystemID'] != $customerInvoiceDirect->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompID'] != '') {
                $company = Company::where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $_post['secondaryLogoCompID'] = $company->CompanyID;
                $_post['secondaryLogo'] = $company->logo_url;
            } else {
                $_post['secondaryLogoCompID'] = NULL;
                $_post['secondaryLogo'] = NULL;
            }

        } else {
            $_post['secondaryLogoCompID'] = NULL;
            $_post['secondaryLogo'] = NULL;
        }

        if ($input['customerInvoiceNo'] != $customerInvoiceDirect->customerInvoiceNo) {
            $_post['customerInvoiceNo'] = $input['customerInvoiceNo'];
        } else {
            $_post['customerInvoiceNo'] = $customerInvoiceDirect->customerInvoiceNo;
        }

        if ($_post['customerInvoiceNo'] != '') {
            /*checking customer invoice no already exist*/
            $verifyCompanyInvoiceNo = CustomerInvoiceDirect::select("bookingInvCode")->where('customerInvoiceNo', $_post['customerInvoiceNo'])->where('customerID', $input['customerID'])->where('companySystemID', $input['companySystemID'])->where('custInvoiceDirectAutoID', '<>', $id)->first();
            if ($verifyCompanyInvoiceNo) {
                return $this->sendError("Entered customer invoice number was already used ($verifyCompanyInvoiceNo->bookingInvCode). Please check again.", 500);
            }
        }


        if ($input['customerID'] != $customerInvoiceDirect->customerID) {
            if (count($detail) > 0) {
                return $this->sendError('Invoice details exist. You cannot change the customer.', 500);
            }
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            if ($customer->creditDays == 0 || $customer->creditDays == '') {
                return $this->sendError($customer->CustomerName . ' - Credit days not mentioned for this customer', 500, array('type' => 'customer_credit_days'));
            }

            /*if customer change*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $_post['customerGLCode'] = $customer->custGLaccount;
            $_post['customerGLSystemID'] = $customer->custGLAccountSystemID;
            $currency = CustomerCurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
            if ($currency) {
                $_post['custTransactionCurrencyID'] = $currency->currencyID;
                $myCurr = $currency->currencyID;

                //$companyCurrency = \Helper::companyCurrency($currency->currencyID);
                $companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $myCurr, $myCurr, 0);
                /*exchange added*/
                $_post['custTransactionCurrencyER'] = 1;

                    //$_post['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    //$_post['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                $_post['bankID'] = null;
                $_post['bankAccountID'] = null;
                $bank = BankAssign::select('bankmasterAutoID')
                    ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                    ->where('isDefault', -1)
                    ->first();

                if ($bank) {
                    $_post['bankID'] = $bank->bankmasterAutoID;
                    $bankAccount = BankAccount::where('companySystemID', $customerInvoiceDirect->companySystemID)
                        ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                        ->where('isDefault', 1)
                        ->where('accountCurrencyID', $currency->currencyID)
                        ->first();

                    if ($bankAccount) {
                        $_post['bankAccountID'] = $bankAccount->bankAccountAutoID;
                    }
                }
            }
            /**/

        } else {
            $companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $input['custTransactionCurrencyID'], $input['custTransactionCurrencyID'], 0);

                $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

        }


        if(isset($input['serviceStartDate']) && $input['serviceStartDate'] != ''){
            $_post['serviceStartDate'] = Carbon::parse($input['serviceStartDate'])->format('Y-m-d') . ' 00:00:00';
        }

        if(isset($input['serviceEndDate']) && $input['serviceEndDate'] != ''){
            $_post['serviceEndDate'] = Carbon::parse($input['serviceEndDate'])->format('Y-m-d') . ' 00:00:00';
        }

        if (isset($input['serviceStartDate']) && isset($input['serviceEndDate']) && $input['serviceStartDate'] != '' && $input['serviceEndDate'] != '') {
            if (($_post['serviceStartDate'] > $_post['serviceEndDate'])) {
                return $this->sendError('Service start date cannot be greater than service end date.', 500);
            }
        }

        $_post['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($_post['bookingDate'] > $curentDate) {
            return $this->sendError('Document date cannot be greater than current date', 500);
        }

        if ($input['invoiceDueDate'] != '') {
            $_post['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
        } else {
            $_post['invoiceDueDate'] = null;
        }

        /*validaation*/
        $_post['customerInvoiceDate'] = $customerInvoiceDirect->customerInvoiceDate;
        if ($input['customerInvoiceDate'] != '') {
            $_post['customerInvoiceDate'] = Carbon::parse($input['customerInvoiceDate'])->format('Y-m-d') . ' 00:00:00';
        } else {
            $_post['customerInvoiceDate'] = null;
        }


        if (($_post['bookingDate'] >= $_post['FYPeriodDateFrom']) && ($_post['bookingDate'] <= $_post['FYPeriodDateTo'])) {

        } else {
            $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
            $_post['bookingDate'] = $curentDate;
            // return $this->sendError('Document Date should be between financial period start date and end date.', 500);

        }

        if ($isPerforma == 2 || $isPerforma == 3|| $isPerforma == 4|| $isPerforma == 5) {
            $detailAmount = CustomerInvoiceItemDetails::select(DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMargin),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt),0) as bookingAmountRpt"))->where('custInvoiceDirectAutoID', $id)->first();
        } else {
            $detailAmount = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $id)->first();
        }


        $_post['bookingAmountTrans'] = \Helper::roundValue($detailAmount->bookingAmountTrans);
        $_post['bookingAmountLocal'] = \Helper::roundValue($detailAmount->bookingAmountLocal);
        $_post['bookingAmountRpt'] = \Helper::roundValue($detailAmount->bookingAmountRpt);

        if ($input['confirmedYN'] == 1) {
            if ($customerInvoiceDirect->confirmedYN == 0) {

                if (($_post['bookingDate'] >= $_post['FYPeriodDateFrom']) && ($_post['bookingDate'] <= $_post['FYPeriodDateTo'])) {

                } else {
                    return $this->sendError('Document date should be between the selected financial period start date and end date.', 500);
                }

                /**/
                if ($isPerforma != 1) {


                    $messages = [

                        'custTransactionCurrencyID.required' => 'Currency is required.',
                        'bankID.required' => 'Bank is required.',
                        'bankAccountID.required' => 'Bank account is required.',

                        'customerInvoiceNo.required' => 'Customer invoice no is required.',
                        'customerInvoiceDate.required' => 'Customer invoice date is required.',
                        'PONumber.required' => 'Po number is required.',
                        'servicePeriod.required' => 'Service period is required.',
                        'serviceStartDate.required' => 'Service start date is required.',
                        'serviceEndDate.required' => 'Service end date is required.',
                        'bookingDate.required' => 'Document date is required.'

                    ];
                    $validator = \Validator::make($_post, [
                        'custTransactionCurrencyID' => 'required|numeric|min:1',
                        'bankID' => 'required|numeric|min:1',
                        'bankAccountID' => 'required|numeric|min:1',

                        'customerInvoiceNo' => 'required',
                        'customerInvoiceDate' => 'required',
                        // 'PONumber' => 'required',
                        // 'servicePeriod' => 'required',
                        // 'serviceStartDate' => 'required',
                        // 'serviceEndDate' => 'required',
                        'bookingDate' => 'required'
                    ], $messages);


                } else {

                    $messages = [
                        'custTransactionCurrencyID.required' => 'Currency is required.',
                        'bankID.required' => 'Bank is required.',
                        'bankAccountID.required' => 'Bank account is required.',

                        'customerInvoiceNo.required' => 'Customer invoice no is required.',
                        'customerInvoiceDate.required' => 'Customer invoice date is required.',
                        'PONumber.required' => 'Po number is required.',
                        'servicePeriod.required' => 'Service period is required.',
                        'serviceStartDate.required' => 'Service start date is required.',
                        'serviceEndDate.required' => 'Service end date is required.',
                        'bookingDate.required' => 'Document date is required.'

                    ];
                    $validator = \Validator::make($_post, [
                        'customerInvoiceNo' => 'required',
                        'customerInvoiceDate' => 'required',
                        'PONumber' => 'required',
                        'servicePeriod' => 'required',
                        'serviceStartDate' => 'required',
                        'serviceEndDate' => 'required',
                        'bookingDate' => 'required'
                    ], $messages);

                }
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                /**/
                /*                if ($isPerforma != 1) {

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


                                }*/


                if (count($detail) == 0) {
                    return $this->sendError('You cannot confirm. Invoice Details not found.', 500);
                } else {

                    if ($isPerforma == 2 || $isPerforma == 3|| $isPerforma == 4|| $isPerforma == 5) {   // item sales invoice || From Delivery Note|| From Sales Order|| From Quotation

                        $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($customerInvoiceDirect->documentSystemiD, $id);

                        if (!$trackingValidation['status']) {
                            return $this->sendError($trackingValidation["message"], 500, ['type' => 'confirm']);
                        }

                        $checkQuantity = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
                            ->where(function ($q) {
                                $q->where('qtyIssued', '<=', 0)
                                    ->orWhereNull('qtyIssued');
                            })
                            ->count();
                        if ($checkQuantity > 0) {
                            return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
                        }

                        $details = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)->get();

                        $financeCategories = $details->pluck('itemFinanceCategoryID')->toArray();

                        if (count(array_unique($financeCategories)) > 1) {
                            return $this->sendError('Multiple finance category cannot be added. Different finance category found on saved details.',500);
                        }

                        foreach ($details as $item) {

//                            If the revenue account or cost account or BS account is null do not allow to confirm

                            if ((!($item->financeGLcodebBSSystemID > 0)) && $item->itemFinanceCategoryID != 2) {
                                return $this->sendError('BS account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
                            } elseif (!($item->financeGLcodePLSystemID > 0)) {
                                return $this->sendError('Cost account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
                            } elseif (!($item->financeGLcodeRevenueSystemID > 0)) {
                                return $this->sendError('Revenue account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
                            }

                            $updateItem = CustomerInvoiceItemDetails::find($item['customerItemDetailID']);
                            $data = array('companySystemID' => $customerInvoiceDirect->companySystemID,
                                'itemCodeSystem' => $updateItem->itemCodeSystem,
                                'wareHouseId' => $customerInvoiceDirect->wareHouseSystemCode);
                            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
                            $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                            $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $updateItem->currentStockQtyInDamageReturn = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
                            $updateItem->issueCostLocal = $itemCurrentCostAndQty['wacValueLocal'];
                            $updateItem->issueCostRpt = $itemCurrentCostAndQty['wacValueReporting'];
                            $updateItem->issueCostLocalTotal = $itemCurrentCostAndQty['wacValueLocal'] * $updateItem->qtyIssuedDefaultMeasure;
                            $updateItem->issueCostRptTotal = $itemCurrentCostAndQty['wacValueReporting'] * $updateItem->qtyIssuedDefaultMeasure;

                            if ($isPerforma == 2 && $updateItem->itemFinanceCategoryID == 1) {
                                $companyCurrencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $customerInvoiceDirect->companyReportingCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $updateItem->issueCostRpt);
                                $updateItem->sellingCost = $companyCurrencyConversion['documentAmount'];
                            }

                            /*margin calculation*/
                            if ($updateItem->marginPercentage != 0 && $updateItem->marginPercentage != null) {
                                $updateItem->sellingCostAfterMargin = $updateItem->sellingCost + ($updateItem->sellingCost * $updateItem->marginPercentage / 100);
                            } else {
                                $updateItem->sellingCostAfterMargin = $updateItem->sellingCost;
                            }

                            if ($updateItem->sellingCurrencyID != $updateItem->localCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $updateItem->sellingCurrencyID, $updateItem->localCurrencyID, $updateItem->sellingCostAfterMargin);
                                if (!empty($currencyConversion)) {
                                    $updateItem->sellingCostAfterMarginLocal = $currencyConversion['documentAmount'];
                                }
                            } else {
                                $updateItem->sellingCostAfterMarginLocal = $updateItem->sellingCostAfterMargin;
                            }

                            if ($updateItem->sellingCurrencyID != $updateItem->reportingCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $updateItem->sellingCurrencyID, $updateItem->reportingCurrencyID, $updateItem->sellingCostAfterMargin);
                                if (!empty($currencyConversion)) {
                                    $updateItem->sellingCostAfterMarginRpt = $currencyConversion['documentAmount'];
                                }
                            } else {
                                $updateItem->sellingCostAfterMarginRpt = $updateItem->sellingCostAfterMargin;
                            }

                            $updateItem->sellingTotal = $updateItem->sellingCostAfterMargin * $updateItem->qtyIssuedDefaultMeasure;

                            /*round to 7 decimal*/

                            $updateItem->issueCostLocal = Helper::roundValue($updateItem->issueCostLocal);
                            $updateItem->issueCostRpt = Helper::roundValue($updateItem->issueCostRpt);
                            $updateItem->issueCostLocalTotal = Helper::roundValue($updateItem->issueCostLocalTotal);
                            $updateItem->issueCostRptTotal = Helper::roundValue($updateItem->issueCostRptTotal);
                            $updateItem->sellingCost = Helper::roundValue($updateItem->sellingCost);
                            $updateItem->sellingCostAfterMargin = Helper::roundValue($updateItem->sellingCostAfterMargin);
                            $updateItem->sellingCostAfterMarginLocal = Helper::roundValue($updateItem->sellingCostAfterMarginLocal);
                            $updateItem->sellingCostAfterMarginRpt = Helper::roundValue($updateItem->sellingCostAfterMarginRpt);
                            $updateItem->sellingTotal = Helper::roundValue($updateItem->sellingTotal);

                            $updateItem->save();

                            if ($isPerforma == 2 || $isPerforma == 4 || $isPerforma == 5) {// only item sales invoice. we won't get from delivery note type.

                                if($updateItem->itemFinanceCategoryID == 1){
                                    if ($updateItem->issueCostLocal == 0 || $updateItem->issueCostRpt == 0) {
                                        return $this->sendError('Item must not have zero cost', 500);
                                    }
                                    if ($updateItem->issueCostLocal < 0 || $updateItem->issueCostRpt < 0) {
                                        return $this->sendError('Item must not have negative cost', 500);
                                    }
                                    if ($updateItem->currentWareHouseStockQty <= 0) {
                                        return $this->sendError('Warehouse stock Qty is 0 for ' . $updateItem->itemDescription, 500);
                                    }
                                    if ($updateItem->currentStockQty <= 0) {
                                        return $this->sendError('Stock Qty is 0 for ' . $updateItem->itemDescription, 500);
                                    }
                                    if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentStockQty) {
                                        return $this->sendError('Insufficient Stock Qty for ' . $updateItem->itemDescription, 500);
                                    }

                                    if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentWareHouseStockQty) {
                                        return $this->sendError('Insufficient Warehouse Qty for ' . $updateItem->itemDescription, 500);
                                    }
                                }else{
                                    if ($updateItem->sellingCostAfterMargin == 0) {
                                        // return $this->sendError('Item must not have zero selling cost', 500);
                                    }
                                }


                            }
                        }

                        // VAT configuration validation
                        $taxSum = Taxdetail::where('documentSystemCode', $id)
                            ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                            ->where('documentSystemID', $customerInvoiceDirect->documentSystemiD)
                            ->sum('amount');

                        if($taxSum  > 0 && empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                            return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
                        }

                        if($taxSum  > 0 && empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                            return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                        }

                        $amount = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
                            ->sum('issueCostRptTotal');

                        $params = array('autoID' => $id,
                            'company' => $customerInvoiceDirect->companySystemID,
                            'document' => $customerInvoiceDirect->documentSystemiD,
                            'segment' => '',
                            'category' => '',
                            'amount' => $amount
                        );

                        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($_post, $id);
                        $confirm = Helper::confirmDocument($params);
                        if (!$confirm["success"]) {
                            return $this->sendError($confirm["message"], 500);
                        } else {
                            return $this->sendResponse($customerInvoiceDirect->toArray(), 'Customer invoice confirmed successfully');
                        }


                    } else {
                        $detailValidation = CustomerInvoiceDirectDetail::selectRaw("glSystemID,IF ( serviceLineCode IS NULL OR serviceLineCode = '', null, 1 ) AS serviceLineCode,IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( unitOfMeasure IS NULL OR unitOfMeasure = '' OR unitOfMeasure = 0, null, 1 ) AS unitOfMeasure, IF ( invoiceQty IS NULL OR invoiceQty = '' OR invoiceQty = 0, null, 1 ) AS invoiceQty, IF ( contractID IS NULL OR contractID = '' OR contractID = 0, null, 1 ) AS contractID,
                    IF ( invoiceAmount IS NULL OR invoiceAmount = '' OR invoiceAmount = 0, null, 1 ) AS invoiceAmount,
                    IF ( unitCost IS NULL OR unitCost = '' OR unitCost = 0, null, 1 ) AS unitCost")->
                        where('custInvoiceDirectID', $id)
                            ->where(function ($query) {

                                $query->whereRaw('serviceLineSystemID IS NULL OR serviceLineSystemID =""')
                                    ->orwhereRaw('serviceLineCode IS NULL OR serviceLineCode =""')
                                    ->orwhereRaw('unitOfMeasure IS NULL OR unitOfMeasure =""')
                                    ->orwhereRaw('invoiceQty IS NULL OR invoiceQty =""')
                                    ->orwhereRaw('contractID IS NULL OR contractID =""')
                                    ->orwhereRaw('invoiceAmount IS NULL OR invoiceAmount =""')
                                    ->orwhereRaw('unitCost IS NULL OR unitCost =""');
                            });

                        if (!empty($detailValidation->get()->toArray())) {

                                /*
                                 * check policy 15
                                 *  Allow to confirm the Customer invoice with contract number
                                 *  This policy should work only for Revenue GL
                                 * */

                            $policyRGLCID = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                                ->where('companySystemID', $input['companySystemID'])
                                ->where('isYesNO', 1)
                                ->exists();

                            foreach ($detailValidation->get()->toArray() as $item) {

                                $validators = \Validator::make($item, [
                                    'serviceLineSystemID' => 'required|numeric|min:1',
                                    'serviceLineCode' => 'required|min:1',
                                    'unitOfMeasure' => 'required|numeric|min:1',
                                    'invoiceQty' => 'required|numeric|min:1',
                                    'invoiceAmount' => 'required|numeric|min:1',
                                    'unitCost' => 'required|numeric|min:1',
                                ], [

                                    'serviceLineSystemID.required' => 'Segment is required.',
                                    'serviceLineCode.required' => 'Cannot confirm. Segment is not updated.',
                                    'unitOfMeasure.required' => 'UOM is required.',
                                    'invoiceQty.required' => 'Qty is required.',
                                    'invoiceAmount.required' => 'Amount is required.',
                                    'unitCost.required' => 'Unit cost is required.'

                                ]);

                                if ($validators->fails()) {
                                    return $this->sendError($validators->messages(), 422);
                                }

                                if(!$policyRGLCID){

                                    $glSystemID = isset($item['glSystemID'])?$item['glSystemID']:0;
                                    $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')
                                        ->where('chartOfAccountSystemID', $glSystemID)
                                        ->where('controlAccountsSystemID',1)// Revenue
                                        ->exists();

                                    if($chartOfAccount){
                                        $contractValidator = \Validator::make($item, [
                                            'contractID' => 'required|numeric|min:1'
                                        ], [
                                            'contractID.required' => 'Contract no. is required.'
                                        ]);
                                        if ($contractValidator->fails()) {
                                            return $this->sendError($contractValidator->messages(), 422);
                                        }
                                    }

                                }

                            }

                        }
                        $groupby = CustomerInvoiceDirectDetail::select('serviceLineCode')->where('custInvoiceDirectID', $id)->groupBy('serviceLineCode')->get();
                        $groupbycontract = CustomerInvoiceDirectDetail::select('contractID')->where('custInvoiceDirectID', $id)->groupBy('contractID')->get();

                        if (count($groupby) != 0) {

                            if (count($groupby) > 1 || count($groupbycontract) > 1) {
                                return $this->sendError('You cannot continue . multiple Segment or contract exist in details.', 500);
                            } else {

                                // VAT configuration validation
                                $taxSum = Taxdetail::where('documentSystemCode', $id)
                                    ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                                    ->where('documentSystemID', $customerInvoiceDirect->documentSystemiD)
                                    ->sum('amount');

                                if($taxSum  > 0 && empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                                    return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
                                }

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

            }
        }
        else {
            $this->customerInvoiceDirectRepository->update($_post, $id);
            return $this->sendResponse($_post, 'Invoice Updated Successfully');
        }
    }

    public function updateCustomerInvoiceGRV(Request $request)
    {
        $input = $request->all();
        $id = isset($input['custInvoiceDirectAutoID']) ? $input['custInvoiceDirectAutoID'] : 0;
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice found');
        }

        if ($customerInvoiceDirect->interCompanyTransferYN == -1) {
            return $this->sendError('This is an intercompany transfer, You can not assign GRV.', 500, array('type' => 'grvAssigned'));
        }

        if (isset($input['customerGRVAutoID']) && $input['customerGRVAutoID']) {
            $checkGrv = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $id)
                ->where('customerGRVAutoID', $input['customerGRVAutoID'])
                ->first();

            if (!empty($checkGrv)) {
                return $this->sendError('Selected GRV is already assigned to ' . $checkGrv->bookingInvCode, 500, array('type' => 'grvAssigned'));
            }
        } else {
            $input['customerGRVAutoID'] = null;
        }

        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update(array_only($input, ['customerGRVAutoID']), $id);

        return $this->sendResponse($customerInvoiceDirect, 'Invoice Updated Successfully');
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

    public function customerInvoiceLocalUpdate($id,Request $request) {
        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {
        $details = CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$id)->get();

        $masterINVID = CustomerInvoice::findOrFail($id);
            $bookingAmountLocal = \Helper::roundValue($masterINVID->bookingAmountTrans/$value);

            $masterVATAmountLocal = \Helper::roundValue($masterINVID->VATAmount / $value);
        $masterInvoiceArray = array('localCurrencyER'=>$value, 'VATAmountLocal'=>$masterVATAmountLocal, 'bookingAmountLocal'=>$bookingAmountLocal);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $localAmount = \Helper::roundValue($item->invoiceAmount / $value);
            $VATAmountLocal = \Helper::roundValue($item->VATAmount / $value);
            $directInvoiceDetailsArray = array('localCurrencyER'=>$value, 'localAmount'=>$localAmount,'VATAmountLocal'=>$VATAmountLocal);
            $updatedLocalER = CustomerInvoiceDirectDetail::findOrFail($item->custInvDirDetAutoID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], 'Update Local ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function customerInvoiceReportingUpdate($id,Request $request) {
        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$id)->get();

        $masterINVID = CustomerInvoice::findOrFail($id);
            $bookingAmountRpt = \Helper::roundValue($masterINVID->bookingAmountTrans/$value);

            $masterVATAmountRpt = \Helper::roundValue($masterINVID->VATAmount / $value);
        $masterInvoiceArray = array('companyReportingER'=>$value, 'VATAmountRpt'=>$masterVATAmountRpt, 'bookingAmountRpt'=>$bookingAmountRpt);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $reportingAmount = \Helper::roundValue($item->invoiceAmount / $value);
            $itemVATAmountRpt = \Helper::roundValue($item->VATAmount / $value);
            $directInvoiceDetailsArray = array('comRptCurrencyER'=>$value, 'comRptAmount'=>$reportingAmount, 'VATAmountRpt'=>$itemVATAmountRpt);
            $updatedLocalER = CustomerInvoiceDirectDetail::findOrFail($item->custInvDirDetAutoID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], 'Update Reporting ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function customerInvoiceDetails(request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $cusBasicData = CustomerInvoiceDirect::find($id);

        $createdDateAndTime = ($cusBasicData) ? Carbon::parse($cusBasicData->createdDateAndTime) : null;

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->with(['company','logistic', 'secondarycompany' => function ($query) use ($createdDateAndTime) {
                $query->whereDate('cutOffDate', '<=', $createdDateAndTime);
        }, 'customer', 'tax', 'createduser', 'bankaccount', 'currency', 'report_currency', 'local_currency', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        }, 'invoicedetails'
        => function ($query) {
                $query->with(['unit', 'department', 'project','contract' => function ($q) {
                    $q->with(['secondary_bank_account']);
                }, 'performadetails' => function ($query) {
                    $query->with(['freebillingmaster' => function ($query) {
                        $query->with(['ticketmaster' => function ($query) {
                            $query->with(['field']);
                        }]);
                    }]);
                }]);
            },
            'issue_item_details' => function ($query) {
                $query->with(['uom_default', 'uom_issuing', 'project']);
            }

        ])->findWithoutFail($id);
        

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        }

        $detail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->first();

        if ($detail) {
            $customerInvoiceDirect['clientContractID'] = $detail->clientContractID;
        }

        $customerInvoiceDirect->projectEnabled = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $customerInvoiceDirect->companySystemID)
            ->where('isYesNO', 1)
            ->exists();

        $customerInvoiceDirect->isVATEligible = TaxService::checkCompanyVATEligible($customerInvoiceDirect->companySystemID);
        
        return $this->sendResponse($customerInvoiceDirect, 'Customer Invoice Direct retrieved successfully');
    }

    public function getCIUploadStatus(Request $request)
    {
        $companyId = $request['companyId'];
        $output = UploadCustomerInvoice::where('companySystemID',$companyId)
                                                ->where('uploadStatus',-1)->count();
        return $this->sendResponse($output, 'Record retrieved successfully');

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

        $customer = CustomerAssigned::select('*')->where('companySystemID', $companyId)->where('isAssigned', '-1')->where('isActive', '1')->get();

        $years = CustomerInvoiceDirect::select(DB::raw("YEAR(bookingDate) as year"))
            ->whereNotNull('bookingDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        // check policy 24 is on for CI
        $isAICINVPolicyOn = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 24)
            ->where('isYesNO', 1)
            ->exists();

        // check policy 42 is on for CI
        $isEDOINVPolicyOn = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 42)
            ->where('isYesNO', 1)
            ->exists();

        // check policy 43 is on for CI
        $isESOINVPolicyOn = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 43)
            ->where('isYesNO', 1)
            ->exists();

        // check policy 44 is on for CI
        $isEQOINVPolicyOn = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 44)
            ->where('isYesNO', 1)
            ->exists();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'customer' => $customer,
            'isAICINVPolicyOn' => $isAICINVPolicyOn,
            'isEDOINVPolicyOn' => $isEDOINVPolicyOn,
            'isESOINVPolicyOn' => $isESOINVPolicyOn,
            'isEQOINVPolicyOn' => $isEQOINVPolicyOn
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function downloadCITemplate(Request $request){

        $file_type = $request->type;

        $companySystemID = $request->companySystemID;
        $sentNotificationAt = $request->sentNotificationAt;



        $templateName = "download_template.ci_template";
        $fileName = 'customer_invoice_template';
        $path = 'accounts-receivable/transactions/customer-invoice-template/excel/';

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $companySystemID)
        ->where('isYesNO', 1)
        ->exists();


        $isVATEligible = TaxService::checkCompanyVATEligible($companySystemID);

        $company = Company::with(['reportingcurrency', 'localcurrency'])->find($companySystemID);


        $output = array(
            'company' => $company,
            'companyCode' =>$company->companyShortCode,
            'sentNotificationAt' => $sentNotificationAt,
            'isProjectBase' => $isProjectBase,
            'isVATEligible' => $isVATEligible,
   
        );

        $basePath = CreateExcel::loadView($output,$file_type,$fileName,$path,$templateName);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function uploadCustomerInvoice(Request $request) {
        $input = $request->all();
        if($input['uploadComment']== ''){
            return $this->sendError('Description is required',500);
        }

        if($input['excelUploadCustomerInvoice']== null){
            return $this->sendError('Please Select a File',500);
        }

        $excelUpload = $input['excelUploadCustomerInvoice'];
        $input = array_except($request->all(), 'excelUploadCustomerInvoice');
        $input = $this->convertArrayToValue($input);

        $decodeFile = base64_decode($excelUpload[0]['file']);
        $originalFileName = $excelUpload[0]['filename'];
        $extension = $excelUpload[0]['filetype'];
        $size = $excelUpload[0]['size'];

        $allowedExtensions = ['xlsx','xls'];

        if (!in_array($extension, $allowedExtensions))
        {
            return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
        }

        if ($size > 20000000) {
            return $this->sendError('The maximum size allow to upload is 20 MB',500);
        }

        $employee = \Helper::getEmployeeInfo();

        $uploadArray = array(
            'companySystemID' => $input['companySystemID'],
            'uploadComment' => $input['uploadComment'],
            'uploadedDate' => \Helper::currentDateTime(),
            'uploadedBy' => $employee->empID,
            'uploadStatus' => -1
        );

        DB::beginTransaction();
        try {

            $uploadCustomerInvoice = UploadCustomerInvoice::create($uploadArray);

            $uploadLogArray = array(
                'companySystemID' => $input['companySystemID'],
                'customerInvoiceUploadID' => $uploadCustomerInvoice->id,
            );

            $logUploadCustomerInvoice = LogUploadCustomerInvoice::create($uploadLogArray);



            $db = isset($request->db) ? $request->db : "";

            $disk = 'local';

            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $objPHPExcel = PHPExcel_IOFactory::load(Storage::disk($disk)->path($originalFileName));

            $uploadData = ['objPHPExcel' => $objPHPExcel,
                'uploadCustomerInvoice' => $uploadCustomerInvoice,
                'logUploadCustomerInvoice' => $logUploadCustomerInvoice,
                'employee' => $employee,
                'uploadedCompany' =>  $input['companySystemID'],
            ];

            CustomerInvoiceUpload::dispatch($db, $uploadData);

            DB::commit();
            return $this->sendResponse([], 'Customer Invoice uploaded successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function checkCustomerInvoiceUploadStatus(Request $request)
    {
        $input = $request->all();

        $checkStatus = CustomerInvoiceUploadDetail::where('custInvoiceDirectID', $input['customerInvoiceID'])
                                                  ->whereHas('uploaded_data', function($query) {
                                                        $query->where('uploadStatus', 1);
                                                  })
                                                  ->first();

        if ($checkStatus) {
            return $this->sendResponse([], 'Customer Invoice can be edit successfully');
        } else {
            return $this->sendError("Unable to edit customer invoice. Upload is currently in progress.");
        }

    }

    public function getCustomerInvoiceUploads(Request $request) {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $uploadCustomerInvoice = UploadCustomerInvoice::where('companySystemID', $input['companyId'])->with('uploaded_by','log')->select('*');


        return \DataTables::eloquent($uploadCustomerInvoice)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function deleteCustomerInvoiceUploads(Request $request){

        $input = $request->all();

        $customerInvoiceUploadID = $input['customerInvoiceUploadID'];
        $uploadCustomerInvoiceObj = UploadCustomerInvoice::find($customerInvoiceUploadID);

        if(!isset($uploadCustomerInvoiceObj)) {
            return $this->sendError('Customer Invoice Upload details not found');
        }

        if($uploadCustomerInvoiceObj->uploadStatus == -1) {
            return $this->sendError('Upload in progress. Cannot be deleted.');
        }

        DB::beginTransaction();
        try {
            if($uploadCustomerInvoiceObj->uploadStatus == 1) {
                $customerInvoiceUploadDetailsIds = CustomerInvoiceUploadDetail::where('customerInvoiceUploadID',$uploadCustomerInvoiceObj->id)->pluck('custInvoiceDirectID')->toArray();

                $validateInvoiceToDelete = $this->validateInvoiceToDelete($customerInvoiceUploadDetailsIds,$uploadCustomerInvoiceObj);
                if(isset($validateInvoiceToDelete['status']) && !$validateInvoiceToDelete['status'])
                    return $this->sendError($validateInvoiceToDelete['message']);
                $customerInvoiceUploadDetails = CustomerInvoiceUploadDetail::where('customerInvoiceUploadID',$uploadCustomerInvoiceObj->id)->get();

                foreach ($customerInvoiceUploadDetails as $customerInvoiceUploadDetail) {
                    $deleteCustomerInvoice  = CustomerInvoiceService::deleteCustomerInvoice($customerInvoiceUploadDetail);
                    if(isset($deleteCustomerInvoice['status']) && !$deleteCustomerInvoice['status'])
                        return $this->sendError($deleteCustomerInvoice['message']);
                }

            }

            UploadCustomerInvoice::where('id', $customerInvoiceUploadID)->delete();
            DB::commit();
            return $this->sendResponse([], 'customer invoice upload deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function validateInvoiceToDelete($customerInvoiceUploadDetailsIds,$uploadCustomerInvoiceObj):array
    {

        $isFailedProcessExists = UploadCustomerInvoice::where('uploadStatus',0)->where('companySystemID',$uploadCustomerInvoiceObj->companySystemID)->orderBy('id', 'DESC')->first();
        $lastCustomerInvoice = CustomerInvoiceDirect::orderBy('custInvoiceDirectAutoID', 'DESC')->where('companySystemID', $uploadCustomerInvoiceObj->companySystemID)->select('custInvoiceDirectAutoID')->first();
        if(!in_array($lastCustomerInvoice->custInvoiceDirectAutoID,$customerInvoiceUploadDetailsIds))
            return ['status' => false , 'message' => 'Additional Invoices had been created after the upload. Cannot delete the uploaded invoices'];


        if($isFailedProcessExists && ($isFailedProcessExists->id > $uploadCustomerInvoiceObj->id))
             return ['status' => false , 'message' => 'There is a failed customer invoice to be delete'];


        return ['status' => true, 'message' => ''];
    }


    public function getCustomerInvoiceMasterView(Request $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('invConfirmedYN', 'customerID', 'month', 'approved', 'canceledYN', 'year', 'isProforma'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $customerID = $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');
        
        $search = $request->input('search.value');

        $invMaster = $this->customerInvoiceDirectRepository->customerInvoiceListQuery($request, $input, $search, $customerID);


        return \DataTables::of($invMaster)
                ->addColumn('total', function($inv) {
                    return $this->getTotalAfterGL($inv);
                })
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

        if ($type == 'getCurrency') {
            $customerID = $request['customerID'];
            $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
            return $this->sendResponse($output, 'Record retrieved successfully');
        }
        if ($id) {
            $master = customerInvoiceDirect::select('bankID', 'custTransactionCurrencyID', 'customerID', 'isPerforma','vatRegisteredYN')
                ->where('custInvoiceDirectAutoID', $id)->first();
        }

        if (!$bankID && $id) {
            if($master){
                $bankID = $master->bankID;
            }
        }

        $output['portMasters'] = PortMaster::where('is_deleted', 0)->get();
        $output['deliveryTermsMasters'] = DeliveryTermsMaster::where('is_deleted', 0)->get();

        $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName,creditDays"))
            ->whereHas('customer_master',function($q){
                $q->where('isCustomerActive',1);
            })
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
        $output['invoiceType'] = array(array('value' => 1, 'label' => 'Proforma Invoice'), array('value' => 0, 'label' => 'Direct Invoice'));
        $output['companyFinanceYear'] = Helper::companyFinanceYear($companyId, 1);
        $output['company'] = Company::select('CompanyName', 'CompanyID', 'companySystemID')->where('companySystemID', $companyId)->first();
        $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')
            ->where('companySystemID', '!=', $companyId)
            ->get();
        $output['yesNoSelectionForMinus'] = YesNoSelectionForMinus::all();
        $output['yesNoSelection'] = YesNoSelection::all();
        $companySystemID = (isset($output['company']) && $output['company']) ? $output['company']['companySystemID'] : 0;
        $output['tax'] = TaxMaster::where('taxType', 2)
            ->where('companySystemID', $companySystemID)
            ->get();
        $output['collectionType'] = CustomerInvoiceStatusType::all();
        $output['segment'] = [];
        $output['customerVATPercentage'] = 0;
        if ($id) {
            if ($master->customerID != '') {
                $output['currencies'] = DB::table('customercurrency')
                    ->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')
                    ->where('customerCodeSystem', $master->customerID)
                    ->where('isAssigned', -1)
                    ->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault', 'DecimalPlaces')
                    ->get();
                $customerAssign = CustomerAssigned::where('customerCodeSystem',$master->customerID)
                                                    ->where('companySystemID',$companySystemID)
                                                    ->first();
                if(!empty($customerAssign)) {
                    $output['customerVATPercentage'] = $customerAssign->vatPercentage;
                }else{
                    $customer = CustomerMaster::find($master->customerID);
                    if(!empty($customer) && $output['customerVATPercentage'] == 0){
                        $output['customerVATPercentage'] = $customer->vatPercentage;
                    }
                }
            } else {
                $output['currencies'] = [];
            }



            $output['bankDropdown'] = BankAssign::where('isActive', 1)
                ->where('isAssigned', -1)
                ->where('companySystemID', $companySystemID)
                ->get();

            $output['bankAccount'] = [];
            if ($bankID != '' && $master->custTransactionCurrencyID != '') {
                $output['bankAccount'] = BankAccount::where('companySystemID', $companySystemID)
                    ->leftjoin('currencymaster', 'currencyID', 'accountCurrencyID')
                    ->where('bankmasterAutoID', $bankID)
                    //->where('accountCurrencyID', $master->custTransactionCurrencyID)
                    ->where('isAccountActive', 1)
                    ->where('approvedYN', 1)
                    ->get();
            }

            $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->get();
            $output['uom'] = Unit::select('UnitID', 'UnitShortCode')->get();

        }

        // check policy 24 is on for CI
        $output['isPolicyOn'] = 0;
        $output['wareHouses'] = [];
        if ($id) {
            $output['isPerforma'] = $master->isPerforma;
        }

        $AICINV = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 24)
            ->where('isYesNO', 1)
            ->first();

        if (isset($AICINV->isYesNO) && $AICINV->isYesNO == 1) {
            $output['isPolicyOn'] = 1;
            $output['invoiceType'][] = array('value' => 2, 'label' => 'Item Sales Invoice');
            $output['wareHouses'] = WarehouseMaster::where("companySystemID", $companyId)->where('isActive', 1)->get();
            $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->get();
        }

        $EDOINV = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 42)
            ->where('isYesNO', 1)
            ->exists();

        if ($EDOINV) {
            $output['isEDOINVPolicyOn'] = 1;
            $output['invoiceType'][] = array('value' => 3, 'label' => 'From Delivery Note');
        }

        $ESOINV = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 43)
            ->where('isYesNO', 1)
            ->exists();

        if ($ESOINV) {
            $output['isESOINVPolicyOn'] = 1;
            $output['invoiceType'][] = array('value' => 4, 'label' => 'From Sales Order');
        }

        $EQOINV = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 44)
            ->where('isYesNO', 1)
            ->exists();

        if ($EQOINV) {
            $output['isEQOINVPolicyOn'] = 1;
            $output['invoiceType'][] = array('value' => 5, 'label' => 'From Quotation');
        }

        if($EDOINV || $ESOINV || $EQOINV) {
            $output['wareHouses'] = WarehouseMaster::where("companySystemID", $companyId)->where('isActive', 1)->get();
            $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->get();
        }

        if ($id) {
            $customer = CustomerMaster::find($master->customerID);
            if (!empty($customer)) {
                $output['isCustomerCatalogPolicyOn'] = CompanyPolicyMaster::where('companySystemID', $customer->primaryCompanySystemID)
                    ->where('companyPolicyCategoryID', 39)
                    ->where('isYesNO', 1)
                    ->exists();
            }

        }

        $output['isProjectBase'] = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();
        $output['projects'] = [];
        if ($output['isProjectBase']) {
            $output['projects'] = ErpProjectMaster::where('companySystemID', $companyId)->get();
        }

        if ($id) {
            $output['isVATEligible'] = $master->vatRegisteredYN;
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
        $PerformaMaster = PerformaMaster::with(['performaTemp' => function ($q) {
            $q->where('isDiscount',1);
        },'ticket' => function ($query) {
            $query->with(['rig']);
        }])->where('companySystemID', $master->companySystemID)
            ->where('customerSystemID', $master->customerID)
            ->where('performaStatus', 0)
            ->where('PerformaOpConfirmed', 1);
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
        }])->where('companySystemID', $master->companySystemID)
            ->where('customerSystemID', $master->customerID)
            ->where('performaStatus', 0)
            ->where('PerformaOpConfirmed', 1)
            ->where('PerformaInvoiceNo', $performaMasterID)
            ->first();

        if (empty($performa)) {
            return $this->sendResponse('e', 'Already pulled');
        }


        /*get bank check bank details from performaDetails*/
        $bankAccountDetails = PerformaDetails::select('currencyID', 'bankID', 'accountID')->where('companyID', $master->companyID)->where('performaMasterID', $performaMasterID)->first();

        if (empty($bankAccountDetails)) {
            return $this->sendResponse('e', 'No details records found');
        }

        //code commented by Nazir : requested by Zahlan
        /*   $detailsAlreadyExist = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();

           if (!empty($detailsAlreadyExist)) {
               return $this->sendResponse('e', 'Already a proforma added to this customer invoice');
           }*/
        $contract = Contract::select('contractUID', 'isRequiredStamp', 'paymentInDaysForJob', 'contractType')
            ->where('CompanyID', $master->companyID)
            ->where('ContractNumber', $performa->contractID)
            ->first();

        if (empty($contract)) {
            return $this->sendError( 'Contract not exist', 500);
        }


        $getRentalDetailFromFreeBilling = FreeBillingMasterPerforma::select('companyID', 'PerformaInvoiceNo', 'rentalStartDate', 'ticketNo', 'rentalEndDate')
            ->where('companyID', $master->companyID)
            ->where('PerformaInvoiceNo', $performaMasterID)
            ->first();

        if (empty($getRentalDetailFromFreeBilling)) {
            return $this->sendError( 'Free Billing Master Performa not found', 500);
        }

        $ticket = TicketMaster::select('Timedatejobstra', 'Timedatejobend')
            ->where('ticketidAtuto', $getRentalDetailFromFreeBilling->ticketNo)
            ->first();

        if (empty($ticket)) {
            return $this->sendError( 'Ticket Master not found', 500);
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
        $updatedInvoiceNo = PerformaDetails::select('*')
            ->where('companyID', $master->companyID)
            ->where('performaMasterID', $performaMasterID)
            ->get();
        //$companyCurrency = \Helper::companyCurrency($myCurr);
        $transDecimalPlace = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $x = 0;
        if (!empty($updatedInvoiceNo)) {
            foreach ($updatedInvoiceNo as $updateInvoice) {
                $serviceLine = SegmentMaster::select('serviceLineSystemID')->where('ServiceLineCode', $updateInvoice->serviceLine)->first();
                $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('AccountCode', $updateInvoice->financeGLcode)->first();
                $companyCurrencyConversion = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $updateInvoice->totAmount);
                $companyCurrencyConversionVAT = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $updateInvoice->totalVatAmount);
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
                $addToCusInvDetails[$x]['isDiscount'] = $updateInvoice->isDiscount;
                if($updateInvoice->isDiscount) {
                    $addToCusInvDetails[$x]['invoiceAmount'] = -$updateInvoice->totAmount;

                }else {
                    $addToCusInvDetails[$x]['invoiceAmount'] = $updateInvoice->totAmount;

                }
                $addToCusInvDetails[$x]['VATAmount'] = $updateInvoice->totalVatAmount;
                $addToCusInvDetails[$x]['VATAmountLocal'] = \Helper::roundValue($companyCurrencyConversionVAT['localAmount']);
                $addToCusInvDetails[$x]['VATAmountRpt'] = \Helper::roundValue($companyCurrencyConversionVAT['reportingAmount']);
                $vatPercentage = 0;
                if ($updateInvoice->totalVatAmount > 0 && ($updateInvoice->totAmount - $updateInvoice->totalVatAmount) != 0) {
                    $vatPercentage = ($updateInvoice->totalVatAmount * 100)/ ($updateInvoice->totAmount - $updateInvoice->totalVatAmount);
                }
                $addToCusInvDetails[$x]['VATPercentage'] = round($vatPercentage, $transDecimalPlace);

                if ($master->isVatEligible) {
                    $vatDetails = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
                    $addToCusInvDetails[$x]['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                    $addToCusInvDetails[$x]['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                }

                $addToCusInvDetails[$x]['localCurrency'] = $master->localCurrencyID;
                $addToCusInvDetails[$x]['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                if($updateInvoice->isDiscount) { 
                    $addToCusInvDetails[$x]['localAmount'] = -$companyCurrencyConversion['localAmount'];
                }else {
                    $addToCusInvDetails[$x]['localAmount'] = $companyCurrencyConversion['localAmount'];
                }
                $addToCusInvDetails[$x]['comRptCurrency'] = $master->companyReportingCurrencyID;
                $addToCusInvDetails[$x]['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                if($updateInvoice->isDiscount) { 
                    $addToCusInvDetails[$x]['comRptAmount'] = -$companyCurrencyConversion['reportingAmount'];

                }else {
                    $addToCusInvDetails[$x]['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];

                }
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

            //$companyCurrencyConversion = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, 0);
            /*exchange added*/
            $bankdetails['custTransactionCurrencyER'] = 1;
            //$bankdetails['companyReportingCurrencyID'] = $master->companyReportingCurrencyID;
            $bankdetails['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            //$bankdetails['localCurrencyID'] = $master->localCurrencyID;
            $bankdetails['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

            $now = Carbon::now();
            $new_date = $now->addDays($contract->paymentInDaysForJob);


            $rentalStartDate = $ticket->Timedatejobstra;
            $rentalEndDate = $ticket->Timedatejobend;

            if ($contract->contractType == 1) {

                $rentalStartDate = $getRentalDetailFromFreeBilling->rentalStartDate;
                $rentalEndDate = $getRentalDetailFromFreeBilling->rentalEndDate;

            }

            $date1 = Carbon::parse($rentalStartDate);
            $month = $date1->format('F');


            $bankdetails['invoiceDueDate'] = $new_date;
            $bankdetails['paymentInDaysForJob'] = $contract->paymentInDaysForJob;
            $bankdetails['performaDate'] = $performa->performaDate;
            $bankdetails['rigNo'] = ($performa->ticket ? $performa->ticket->regNo . ' - ' . ((isset($performa->ticket->rig->RigDescription)) ? $performa->ticket->rig->RigDescription : '') : '');
            $bankdetails['servicePeriod'] = $month;
            $bankdetails['serviceStartDate'] = $rentalStartDate;
            $bankdetails['serviceEndDate'] = $rentalEndDate;
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

                $resVat = $this->updatePerformaTotalVAT($custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 

                DB::commit();
                return $this->sendResponse('s', 'successfully created');
            } catch (\Exception $exception) {
                DB::rollback();
                return $this->sendResponse('e', 'Error Occured !');
            }

        }


    }

     public function updatePerformaTotalVAT($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)
                                                    ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->VATAmount;
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                              ->where('documentSystemID', 20)
                              ->delete();

        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceProformaTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

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

    public function savecustomerInvoiceTaxDetails(Request $request)
    {
        $input = $request->all();
        $custInvoiceDirectAutoID = isset($input['custInvoiceDirectAutoID'])?$input['custInvoiceDirectAutoID']:0;
        $percentage = isset($input['percentage'])?$input['percentage']:0;

        if (empty($input['taxMasterAutoID'])) {
            $input['taxMasterAutoID'] = 0;
            //return $this->sendResponse('e', 'Please select a tax.');
        }

        $taxMasterAutoID = $input['taxMasterAutoID'];

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return $this->sendResponse('e', 'Customer Invoice not found.');
        }

        if ($master->isPerforma == 2 || $master->isPerforma == 3 || $master->isPerforma == 4|| $master->isPerforma == 5) {
            $invoiceDetail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        } else {
            $invoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        }

        if (empty($invoiceDetail)) {
            return $this->sendResponse('e', 'Invoice Details not found.');
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        if ($master->isPerforma == 2 || $master->isPerforma == 3|| $master->isPerforma == 4|| $master->isPerforma == 5) {
            $totalDetail = CustomerInvoiceItemDetails::select(DB::raw("SUM(sellingTotal) as amount"))->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
            if (!empty($totalDetail)) {
                $totalAmount = $totalDetail->amount;
            }
            $totalAmount = ($percentage / 100) * $totalAmount;
        } else {
            $totalDetail = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as amount"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
            if (!empty($totalDetail)) {
                $totalAmount = $totalDetail->amount;
            }
            $totalAmount = ($percentage / 100) * $totalAmount;
        }

        /*$taxMaster = TaxMaster::where('taxType', 2)
            ->where('companySystemID', $master->companySystemID)
            ->first();

        if (empty($taxMaster)) {
            return $this->sendResponse('e', 'VAT Master not found');
        }*/

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return $this->sendResponse('e', 'VAT Detail Already exist');
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalAmount);


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
            return $this->sendError('Error Occurred',500);
        }
    }

    public function AllDeleteCustomerInvoiceDetails(Request $request)
    {
        $id = $request['id'];
        $getPerformaMasterID = CustomerInvoiceDirectDetail::select('performaMasterID', 'companyID', 'custInvoiceDirectID')->where('custInvoiceDirectID', $id)->groupBy('performaMasterID', 'companyID', 'custInvoiceDirectID')->get();


        if (empty($getPerformaMasterID)) {
            return $this->sendResponse('e', 'No details found');
        }


        $Taxdetail = Taxdetail::where('documentSystemCode', $id)
                              ->where('documentSystemID', 20)
                              ->first();
       

        DB::beginTransaction();
        try {
            if (!empty($Taxdetail)) {
                $TaxdetailDeleteRes = Taxdetail::where('documentSystemCode', $id)
                                              ->where('documentSystemID', 20)
                                              ->delete();
            }

            if ($getPerformaMasterID) {
                foreach ($getPerformaMasterID as $val) {
                    $peformaMasterID = $val->performaMasterID;
                    PerformaMaster::where('companyID', $val->companyID)->where('PerformaInvoiceNo', $peformaMasterID)->update(array('performaStatus' => 0));

                    PerformaDetails::where('companyID', $val->companyID)->where('PerformaMasterID', $peformaMasterID)->update(array('invoiceSsytemCode' => 0));
                    CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->delete();


                }
            }

            $details = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $id)->first()->toArray();
            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $id)->update($details);

            DB::commit();
            return $this->sendResponse('s', 'Successfully Deleted');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('e', 'Error Occurred');
        }


        
    }


    
    private function storeImage($imageData, $picName, $picBasePath,$disk)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); 

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new Exception('invalid image type');
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                throw new Exception('image decode failed');
            }

            $picNameExtension = "{$picName}.{$type}";
            $picFullPath = $picBasePath . $picNameExtension;
            Storage::disk($disk)->put($picFullPath, $imageData);
        } else if (preg_match('/^https/', $imageData)) {
            $imageData = basename($imageData);
            $picFullPath = $picBasePath;
        } else {
            throw new Exception('did not match data URI with image data');
        }

        return $picFullPath;
    }


    public static function quickRandom($length = 6)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
        return substr(str_shuffle(str_repeat($pool, 2)), 0, $length);
    }
    public function printCustomerInvoice(Request $request)
    {

        $id = $request->get('id');
        $type = $request->get('type');

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $id)->first();
        if (!$master) {
            return $this->sendError("Customer invoice not found");
        }
        $companySystemID = $master->companySystemID;
        $localCurrencyER = $master->localCurrencyER;

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companySystemID)
            ->where('isYesNO', 1)
            ->exists();

        if ($master->isPerforma == 2 || $master->isPerforma == 3 || $master->isPerforma == 4 || $master->isPerforma == 5) {
            $detail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)->first();
        } else {
            $detail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->first();
        }

        $customerInvoice = (object)[];
        if ($master->isPerforma == 1) {
            $customerInvoice = $this->customerInvoiceDirectRepository->getAudit($id);
        } else if ($master->isPerforma == 2 || $master->isPerforma == 3 || $master->isPerforma == 4 || $master->isPerforma == 5) {
            $customerInvoice = $this->customerInvoiceDirectRepository->getAuditItemInvoice($id);
        } else {
            $customerInvoice = $this->customerInvoiceDirectRepository->getAudit2($id);
        }

        if (!$customerInvoice) {
            return $this->sendError("Customer invoice not found");
        }
        $accountIBAN = '';
        if ($customerInvoice && $customerInvoice->bankAccount) {
            $accountIBAN = $customerInvoice->bankAccount['accountIBAN#'];
        }

        $company = Company::with('country')->where('companySystemID', $companySystemID)->first();
        $companyLogo = '';
        $CompanyName = '';
        $CompanyAddress = '';
        $CompanyAddressSecondaryLanguage = '';
        $CompanyCountry = '';
        $CompanyTelephone = '';
        $vatRegistratonNumber = '';
        $CompanyFax = '';
        if ($company) {
            $companyLogo = $company->logo_url;
            $CompanyName = $company->CompanyName;
            $vatRegistratonNumber = $company->vatRegistratonNumber;
            $CompanyFax = $company->CompanyFax;
            $CompanyTelephone = $company->CompanyTelephone;
            $CompanyAddress = $company->CompanyAddress;
            $CompanyAddressSecondaryLanguage = $company->CompanyAddressSecondaryLanguage;
            $CompanyCountry = $company->country->countryName;
        }

        $checkCompanyIsMerged = SecondaryCompany::where('companySystemID', $companySystemID)
                                                ->whereDate('cutOffDate', '<=', Carbon::parse($master->createdDateAndTime))
                                                ->first();

        if ($checkCompanyIsMerged) {
            $companyLogo = $checkCompanyIsMerged['logo_url'];
            $CompanyName = $checkCompanyIsMerged['name'];
        }

        if ($master->secondaryLogoCompanySystemID > 0) {
            $company = Company::with('country')->where('companySystemID', $master->secondaryLogoCompanySystemID)->first();
            if ($company) {
                $CompanyName = $company->CompanyName;
                $CompanyFax = $company->CompanyFax;
                $CompanyTelephone = $company->CompanyTelephone;
                $vatRegistratonNumber = $company->vatRegistratonNumber;
                $CompanyAddress = $company->CompanyAddress;
                $CompanyAddressSecondaryLanguage = $company->CompanyAddressSecondaryLanguage;
                $companyLogo = $company->logo_url;
                $CompanyCountry = $company->country->countryName;
            }

        }


        $line_invoiceNO = true;
        $line_invoiceDate = true;
        $line_seNo = true;
        $line_poNumber = true;
        $line_unit = true;
        $line_jobNo = true;
        $line_contractNo = true;
        $line_subcontractNo = true;
        $line_dueDate = true;
        $line_customerShortCode = true;
        $invoiceDetails = false;
        $template = 1;
        $lineSecondAddress = false;
        $lineApprovedBy = false;
        $linePageNo = false;

        $linefooterAddress = false;
        $linePdoinvoiceDetails = false;
        $logo = true;
        $line_performaCode = false;
        $line_paymentTerms = false;
        $line_rentalPeriod = false;
        $line_po_detail = false;
        $footerDate = true;
        $temp = [];
        if ($master->isPerforma == 1) {
            $temp = DB::select("SELECT 	myStdTitle,sumofsumofStandbyAmount,sortOrder, invoiceQty, unitCost FROM (SELECT
                            performaMasterID ,companyID, invoiceQty, unitCost
                        FROM
                            erp_custinvoicedirectdet 
                        WHERE
                            custInvoiceDirectID = $id
                            GROUP BY performaMasterID ) erp_custinvoicedirectdet 	INNER JOIN performatemp ON erp_custinvoicedirectdet.performaMasterID = performatemp.performaInvoiceNo 
                            AND erp_custinvoicedirectdet.companyID = performatemp.companyID
                            WHERE sumofsumofStandbyAmount <> 0 	ORDER BY sortOrder ASC ");
        }
        $customerInvoice->is_po_in_line = false;
        $customerInvoice->isProjectBase = $isProjectBase;
        switch ($companySystemID) {
            case 7:
                /*BO*/
                if ($master->isPerforma == 1) {

                    $template = 1;
                    $line_dueDate = false;
                    $line_contractNo = false;
                    $line_customerShortCode = false;
                    $lineSecondAddress = true;
                    $lineApprovedBy = true;
                } else {
                    $template = 2;
                    $line_unit = false;
                    $line_jobNo = false;
                    $line_subcontractNo = false;
                    $lineSecondAddress = true;
                    $lineApprovedBy = true;
                }

                break;
            case 11:
                /*FREE*/
                $lineApprovedBy = true;
                if ($master->isPerforma == 1) {
                    $template = 1;
                    $line_dueDate = false;
                    $line_contractNo = false;
                    $line_customerShortCode = false;
                    $lineSecondAddress = true;
                } else {
                    $template = 2;
                    $line_unit = false;
                    $line_jobNo = false;
                    $line_subcontractNo = false;

                }
                break;
            case 31: /*IPCP*/
            case 24 : /*GEY - Yemen*/
            case 42: /*MOS*/
            case 60: /*WMS*/
            case 63: /*WSS*/
            case 43: /*Makamen*/
                if ($companySystemID == 31) {
                    $linefooterAddress = false;
                } else {
                    $linefooterAddress = true;
                }

                if ($master->isPerforma == 1) {
                    $template = 1;
                    $line_unit = false;
                    $line_jobNo = false;
                    $line_subcontractNo = false;
                    $linePageNo = true;

                    /*requested by zahlan on 2018-12-20 remove group for midwest company*/

                    if (in_array($companySystemID, [42, 31, 24])) {
                        $invoiceDetails = DB::select("SELECT ClientRef, qty, rate,  qty * rate  AS amount,assetDescription FROM ( SELECT freebilling.ContractDetailID, billProcessNo, assetDescription, freebilling.qtyServiceProduct AS qty, IFNULL( standardRate, 0 ) + IFNULL( operationRate, 0 ) AS rate, freebilling.performaInvoiceNo, freebilling.TicketNo, freebilling.companyID,freebilling.mitID FROM ( SELECT performaMasterID FROM `erp_custinvoicedirectdet` WHERE `custInvoiceDirectID` = $master->custInvoiceDirectAutoID GROUP BY performaMasterID ) t INNER JOIN freebilling ON freebilling.companyID = '$master->companyID' AND freebilling.performaInvoiceNo = t.performaMasterID INNER JOIN ticketmaster ON freebilling.TicketNo = ticketmaster.ticketidAtuto LEFT JOIN rigmaster on ticketmaster.regName = rigmaster.idrigmaster ) t LEFT JOIN contractdetails ON contractdetails.ContractDetailID = t.ContractDetailID  ORDER BY  t.mitID ASC");
                    } else {
                        $invoiceDetails = DB::select("SELECT ClientRef, SUM(qty) as qty, rate, SUM( qty * rate ) AS amount,assetDescription, pl3 FROM ( SELECT freebilling.ContractDetailID, billProcessNo, assetDescription, freebilling.qtyServiceProduct AS qty, IFNULL( standardRate, 0 ) + IFNULL( operationRate, 0 ) AS rate, freebilling.performaInvoiceNo, freebilling.pl3, freebilling.TicketNo, freebilling.companyID,freebilling.mitID FROM ( SELECT performaMasterID FROM `erp_custinvoicedirectdet` WHERE `custInvoiceDirectID` = $master->custInvoiceDirectAutoID GROUP BY performaMasterID ) t INNER JOIN freebilling ON freebilling.companyID = '$master->companyID' AND freebilling.performaInvoiceNo = t.performaMasterID INNER JOIN ticketmaster ON freebilling.TicketNo = ticketmaster.ticketidAtuto LEFT JOIN rigmaster on ticketmaster.regName = rigmaster.idrigmaster ) t LEFT JOIN contractdetails ON contractdetails.ContractDetailID = t.ContractDetailID GROUP BY t.ContractDetailID, rate ORDER BY  t.mitID ASC");
                    }

                    if ($customerInvoice->customerID == 79 && $companySystemID == 63) {
                        $customerInvoice->is_po_in_line = true;
                    }
                } else {
                    $linePageNo = true;
                    $template = 2;
                    $line_unit = false;
                    $line_jobNo = false;
                    $line_subcontractNo = false;
                    $line_dueDate = false;
                    $invoiceDetails = false;
                }
                break;
            case 52: /*SGEE*/
                $lineApprovedBy = true;
                /*$linefooterAddress = true;*/

                if ($master->isPerforma == 1) {
                    if ($master->customerID == 79) {
                        $footerDate = false;
                        $logo = true;
                        $line_seNo = false;
                        $line_subcontractNo = false;
                        $line_contractNo = true;
                        $line_customerShortCode = true;
                        $line_dueDate = false;
                        $line_jobNo = false;
                        $lineSecondAddress = true;
                        $line_poNumber = true;
                        $line_performaCode = true;
                        $line_paymentTerms = true;
                        $line_rentalPeriod = true;
                        if (isset($customerInvoice->invoicedetail->contract->contractType) && $customerInvoice->invoicedetail->contract->contractType == ContractMasterType::SERVICE_PRODUCT_BASED) {
                            $line_rentalPeriod = false;
                            $line_po_detail = true;
                            $linePdoinvoiceDetails = $this->getPerformaPDOInvoiceDetail($master, 'C000071');
                        } else {
                            $line_rentalPeriod = false;
                            $linePdoinvoiceDetails = DB::select("SELECT wellNo, netWorkNo, SEno, sum(wellAmount) as wellAmount FROM ( SELECT performaMasterID, companyID, contractID, clientContractID FROM erp_custinvoicedirectdet WHERE custInvoiceDirectID = $master->custInvoiceDirectAutoID GROUP BY performaMasterID ) t INNER JOIN performamaster ON performamaster.companyID = '$master->companyID' AND performamaster.PerformaInvoiceNo = t.performaMasterID AND t.clientContractID = performamaster.contractID INNER JOIN performa_service_entry_wellgroup ON performamaster.PerformaMasterID = performa_service_entry_wellgroup.performaMasID GROUP BY wellNo, netWorkNo, SEno ");
                        }


                        $template = 1;
                    } else {

                        $linefooterAddress = true;
                        $logo = true;
                        $template = 1;
                        $line_seNo = true;
                        $line_unit = true;
                        $line_jobNo = true;
                        $line_dueDate = false;
                        $line_subcontractNo = true;
                        $line_contractNo = false;
                        $line_customerShortCode = false;
                        $linePageNo = true;
                    }
                } else {
                    $template = 2;
                    $line_unit = false;
                    $line_jobNo = false;
                    $line_subcontractNo = false;
                }
                break;
            default:
                $lineApprovedBy = true;
                $linefooterAddress = true;
                if ($master->isPerforma == 1) {
                    $template = 1;
                    $line_contractNo = false;
                    $line_dueDate = false;
                    $line_customerShortCode = false;
                } else {
                    $template = 2;
                    $line_unit = false;
                    $line_jobNo = false;
                    $line_subcontractNo = false;
                }
        }

        $custom = (array)$customerInvoice;
        if (empty($custom)) {
            return $this->sendError('Customer Invoice detail not found.');
        }
        $customerInvoice->companySystemID = $companySystemID;
        $customerInvoice->CompanyName = $CompanyName;
        $customerInvoice->CompanyTelephone = $CompanyTelephone;
        $customerInvoice->CompanyFax = $CompanyFax;
        $customerInvoice->vatRegistratonNumber = $vatRegistratonNumber;
        $customerInvoice->CompanyCountry = $CompanyCountry;
        $customerInvoice->CompanyAddress = $CompanyAddress;
        $customerInvoice->CompanyAddressSecondaryLanguage = $CompanyAddressSecondaryLanguage;
        $customerInvoice->companyLogo = $companyLogo;

        $customerInvoice->docRefNo = \Helper::getCompanyDocRefNo($customerInvoice->companySystemID, $customerInvoice->documentSystemiD);

        /*  $template = false;
          if ($master->isPerforma == 1) {
              $detail = CustomerInvoiceDirectDetail::with(['contract'])->where('custInvoiceDirectID', $id)->first();
              $template = $detail->contract->performaTempID + 1;
          }*/

        $customerInvoice->line_invoiceNO = $line_invoiceNO;
        $customerInvoice->line_invoiceDate = $line_invoiceDate;
        $customerInvoice->line_seNo = $line_seNo;
        $customerInvoice->line_poNumber = $line_poNumber;
        $customerInvoice->line_unit = $line_unit;
        $customerInvoice->line_jobNo = $line_jobNo;
        $customerInvoice->template = $template;
        $customerInvoice->line_dueDate = $line_dueDate;
        $customerInvoice->line_contractNo = $line_contractNo;
        $customerInvoice->line_subcontractNo = $line_subcontractNo;
        $customerInvoice->line_customerShortCode = $line_customerShortCode;
        $customerInvoice->line_invoiceDetails = $invoiceDetails;
        $customerInvoice->lineSecondAddress = $lineSecondAddress;
        $customerInvoice->lineApprovedBy = $lineApprovedBy;
        $customerInvoice->linePageNo = $linePageNo;
        $customerInvoice->linefooterAddress = $linefooterAddress;
        $customerInvoice->linePdoinvoiceDetails = $linePdoinvoiceDetails;
        $customerInvoice->line_performaCode = $line_performaCode;
        $customerInvoice->line_paymentTerms = $line_paymentTerms;
        $customerInvoice->line_rentalPeriod = $line_rentalPeriod;
        $customerInvoice->logo = $logo;
        $customerInvoice->footerDate = $footerDate;
        $customerInvoice->temp = $temp;
        $customerInvoice->localCurrencyER = $localCurrencyER;

        $customerInvoice->is_pdo_vendor = false;
        $customerInvoice->vatNumber = '';
        $customerInvoice->vendorCode = '';

        if ($customerInvoice->customerID == 79) {
            $customerInvoice->is_pdo_vendor = true;
            $customerInvoice->bookingInvCode = str_replace('INV', '', $customerInvoice->bookingInvCode);

            $customerAssign = CustomerAssigned::where('customerCodeSystem', $customerInvoice->customerID)
                ->where('companySystemID', $customerInvoice->companySystemID)
                ->first();

            if (!empty($customerAssign)) {
                $customerInvoice->vatNumber = $customerAssign->vatNumber;
                $customerInvoice->vendorCode = $customerAssign->vendorCode;
            }
        } else {
            $customerAssign = CustomerAssigned::where('customerCodeSystem', $customerInvoice->customerID)
                ->where('companySystemID', $customerInvoice->companySystemID)
                ->first();

            if (!empty($customerAssign)) {
                $customerInvoice->vatNumber = $customerAssign->vatNumber;
                $customerInvoice->vendorCode = $customerAssign->vendorCode;
            }
        }

        $secondaryBankAccount = (object)[];
        if ($customerInvoice->secondaryLogoCompanySystemID) {
            $secondaryBankAccount = CustomerInvoiceDirectDetail::with(['contract' => function ($q) {
                $q->with(['secondary_bank_account' => function ($query) {
                    $query->with('currency');
                }]);
            }])->where('contractID', '>', 0)
                ->where('custInvoiceDirectID', $id)->first();
        }

        $month = date("F", strtotime($customerInvoice->bookingDate));
        $year = date('Y', strtotime($customerInvoice->bookingDate));
        $customerInvoice->monthOfInvoice = strtoupper($month) . " " . $year;

        $accountIBANSecondary = '';
        if (!empty((array)$secondaryBankAccount)) {
            if ($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account) {
                $accountIBANSecondary = $secondaryBankAccount->contract->secondary_bank_account['accountIBAN#'];
            }
        }

        $customerInvoice->accountIBAN = $accountIBAN;
        $customerInvoice->accountIBANSecondary = $accountIBANSecondary;

        $awsPolicy = Helper::checkPolicy($companySystemID, 50);

        $customerInvoice->logoExists = false;
        if ($awsPolicy) {
            if (Storage::disk(Helper::policyWiseDisk($companySystemID, 'local_public'))->exists($company->logoPath)) {
                $customerInvoice->logoExists = true;
            }            
        } else {
            if (Storage::disk(Helper::policyWiseDisk($companySystemID, 'local_public'))->exists($company->logoPath)) {
                $customerInvoice->logoExists = true;
            }      
        }

        $directTraSubTotal = 0;
        if ($master->isPerforma == 2 || $master->isPerforma == 3 || $master->isPerforma == 4 || $master->isPerforma == 5) {
            $customerInvoice->item_invoice = true;
            foreach ($customerInvoice->issue_item_details as $key => $item) {
                $directTraSubTotal += $item->sellingTotal;
            }

            if ($customerInvoice->tax) {
                $directTraSubTotal += $customerInvoice->tax->amount;
            }
        } else {
            $customerInvoice->item_invoice = false;
            foreach ($customerInvoice->invoicedetails as $key => $item) {
                $directTraSubTotal += $item->invoiceAmount;
            }

            if ($customerInvoice->tax) {
                $directTraSubTotal += $customerInvoice->tax->amount;
            }
        }
        $directTraSubTotalnumberformat=  number_format( $directTraSubTotal,empty($customerInvoice->currency) ? 2 : $customerInvoice->currency->DecimalPlaces);
        $stringReplacedDirectTraSubTotal = str_replace(',', '', $directTraSubTotalnumberformat);
        $amountSplit = explode(".", $stringReplacedDirectTraSubTotal);
        $intAmt = 0;
        $floatAmt = 00;

        if (count($amountSplit) == 1) {
            $intAmt = $amountSplit[0];
            $floatAmt = 00;
        } else if (count($amountSplit) == 2) {
            $intAmt = $amountSplit[0];
            $floatAmt = $amountSplit[1];
        }
        $numFormatter = new \NumberFormatter("ar", \NumberFormatter::SPELLOUT);
        $floatAmountInWords = '';
        $intAmountInWords = ($intAmt > 0) ? strtoupper($numFormatter->format($intAmt)) : '';
        $floatAmountInWords = ($floatAmt > 0) ? " و هللة‎" . strtoupper($numFormatter->format($floatAmt)) . " فقط." : '';

        $customerInvoice->amountInWords = ($floatAmountInWords != "") ? "الريال السعودي " . $intAmountInWords . $floatAmountInWords : "الريال السعودي " . $intAmountInWords . " فقط";

        $numFormatterEn = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        $customerInvoice->floatAmt = (string)$floatAmt;

        //add zeros to decimal point
        if($customerInvoice->floatAmt != 00){
            $length = strlen($customerInvoice->floatAmt);
            if($length<$customerInvoice->currency->DecimalPlaces){
                $count = $customerInvoice->currency->DecimalPlaces-$length;
                for ($i=0; $i<$count; $i++){
                    $customerInvoice->floatAmt .= '0';
                }
            }
        }
        $customerInvoice->amount_word = ucfirst($numFormatterEn->format($intAmt));
        $customerInvoice->amount_word = str_replace('-', ' ', $customerInvoice->amount_word);

        $numberfrmtDirectTraSubTotal = number_format( $directTraSubTotal,empty($customerInvoice->currency) ? 2 : $customerInvoice->currency->DecimalPlaces);
        $numberfrmtDirectTraSubTotal = str_replace(',', '', $numberfrmtDirectTraSubTotal);
        $floatedDirectTraSubTotal = floatval($numberfrmtDirectTraSubTotal);
        $floatedAmountInWordsEnglish = ucwords($numFormatterEn->format($floatedDirectTraSubTotal));
        $customerInvoice->floatedAmountInWordsEnglish = $floatedAmountInWordsEnglish.' Only';



        $amountInWordsEnglish = ucwords($numFormatterEn->format($directTraSubTotal));

        $customerInvoice->amountInWordsEnglish = (isset($customerInvoice->currency->CurrencyName) ? $customerInvoice->currency->CurrencyName : '') ." ".$amountInWordsEnglish.' Only';
        $printTemplate = ErpDocumentTemplate::with('printTemplate')->where('companyID', $companySystemID)->where('documentID', 20);

        
        $contractID = 0;
        if ($master->isPerforma == 1) {
            $contractID = isset($detail->contractID) ? $detail->contractID : 0;
        }

        if ($contractID > 0) {
            $printTemplate = $printTemplate->where('contractUID', $contractID);
        } else {
            $printTemplate = $printTemplate->whereNull('contractUID');
        }

        $printTemplate = $printTemplate->first();

        if (!is_null($printTemplate)) {
            $printTemplate = $printTemplate->toArray();
        }


        if ($printTemplate['printTemplateID'] == 15) {
            $customerInvoice->amount_word = ucwords($customerInvoice->amount_word);
        }
    
        if ($printTemplate['printTemplateID'] == 2 && $master->isPerforma == 1) {
            $proformaBreifData = $this->getProformaInvoiceDetailDataForPrintInvoice($id);
            $customerInvoice->profomaDetailData = $proformaBreifData;
        } else if ($printTemplate['printTemplateID'] == 4 && $master->isPerforma == 1) {
            $proformaBreifData = $this->getProformaInvoiceDetailDataForProductServiceContract($id, $master->companyID);
            $customerInvoice->profomaDetailData = $proformaBreifData;
        }


        if (($printTemplate['printTemplateID'] == 7 || $printTemplate['printTemplateID'] == 8) && $master->isPerforma == 1) {
            if (isset($customerInvoice->invoicedetail->contract->contractType) && ($customerInvoice->invoicedetail->contract->contractType == ContractMasterType::SERVICE_PRODUCT_BASED || $customerInvoice->invoicedetail->contract->contractType == ContractMasterType::SERVICE_PRODUCT_ISSUE_BASED)) {
                $linePdoinvoiceDetails = $this->getPerformaPDOInvoiceDetail($master, $customerInvoice->customer->CutomerCode);
                $customerInvoice->linePdoinvoiceDetails = $linePdoinvoiceDetails;
            } else {
                $customerInvoice->linePdoinvoiceDetails = false;
            }
        }

        $customerID = $customerInvoice->customerID;
        $CustomerContactDetails = CustomerContactDetails::where('customerID', $customerID)->where('isDefault', -1)->first();
        if($CustomerContactDetails){
            $customerInvoice['CustomerContactDetails'] = $CustomerContactDetails;
        }
       
        $customerInvoiceLogistic = CustomerInvoiceLogistic::with('port_of_loading','port_of_discharge')
                                                            ->where('custInvoiceDirectAutoID', $id)
                                                            ->first();
        
        if($customerInvoiceLogistic){
            $customerInvoiceLogistic = $customerInvoiceLogistic->toArray();
            $customerInvoice['customerInvoiceLogistic'] = $customerInvoiceLogistic;
        }
        
        if(!isset($secondaryBankAccount)){
            return $this->sendError('Bank account not found.');
        }

        $array = array('type'=>$type,'request' => $customerInvoice, 'secondaryBankAccount' => $secondaryBankAccount);
        $time = strtotime("now");
        $fileName = 'customer_invoice_' . $id . '_' . $time . '.pdf';
        $fileName_csv = 'customer_invoice_' . $id . '_' . $time . '.csv';
        $fileName_xls = 'customer_invoice_' . $id . '_' . $time;

     

        if ($printTemplate['printTemplateID'] == 2) {
            if($type == 1)
            {
                $html = view('print.customer_invoice_tue', $array);
                $htmlFooter = view('print.customer_invoice_tue_footer', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter($htmlFooter);
    
                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.customer_invoice_tue', $array)->with('no_asset', true);
                    });
                    
                })->download('csv');
            }
        
        } else if ($printTemplate['printTemplateID'] == 13) {
            if($type == 1)
            {
                $html = view('print.APMC_customer_invoice', $array);
                $htmlFooter = view('print.APMC_customer_invoice_footer', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter($htmlFooter);

                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_xls, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.APMC_customer_invoice', $array)->with('no_asset', true);
                    });
                    
                })->download('xls');
            }
        
        } else if ($printTemplate['printTemplateID'] == 15) {
            if($type == 1)
            {
                $html = view('print.BNI_customer_invoice', $array);
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
    
                return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_xls, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.BNI_customer_invoice', $array)->with('no_asset', true);
                    });
                    
                })->download('xls');
            }
        
        } else if ($printTemplate['printTemplateID'] == 11) {
            if($type == 1)
            {
                $html = view('print.chromite_customer_invoice', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_xls, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.chromite_customer_invoice', $array)->with('no_asset', true);
                    });
                    
                })->download('xls');
            }
        
        } else if ($printTemplate['printTemplateID'] == 1 || $printTemplate['printTemplateID'] == null) {
            
            if($type == 1)
            {
                $html = view('print.customer_invoice', $array);
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
    
                return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.customer_invoice', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }


        } else if ($printTemplate['printTemplateID'] == 5) {
            
            if($type == 1)
            {
                $html = view('print.customer_invoice_tax', $array);
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
    
                return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.customer_invoice_tax', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }

        } else if ($printTemplate['printTemplateID'] == 12) {
            if($type == 1)
            {
                $html = view('print.rihal_customer_invoice', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_xls, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.rihal_customer_invoice', $array)->with('no_asset', true);
                    });
                    
                })->download('xls');
            }
        
        } else if ($printTemplate['printTemplateID'] == 6) {

            if($type == 1)
            {
                $html = view('print.invoice_template.customer_invoice_hlb', $array);
                $htmlFooter = view('print.invoice_template.customer_invoice_hlb_footer', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter($htmlFooter);
    
                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.invoice_template.customer_invoice_hlb', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }


        } else if ($printTemplate['printTemplateID'] == 3) {

            if($type == 1)
            {
                $html = view('print.customer_invoice_with_po_detail', $array);
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
    
                return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.customer_invoice_with_po_detail', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }
   
        } else if ($printTemplate['printTemplateID'] == 7) {


            if($type == 1)
            {
                $html = view('print.invoice_template.customer_invoice_gulf_vat', $array);
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
    
                return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.invoice_template.customer_invoice_gulf_vat', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }


        } else if ($printTemplate['printTemplateID'] == 8) {
            if($type == 1)
            {
                $html = view('print.invoice_template.customer_invoice_gulf_vat_usd', $array);
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
    
                return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.invoice_template.customer_invoice_gulf_vat_usd', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }

        } else if ($printTemplate['printTemplateID'] == 4) {

            if($type == 1)
            {
                $html = view('print.customer_invoice_tue_product_service', $array);
                $htmlFooter = view('print.customer_invoice_tue_footer', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter($htmlFooter);
    
                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.customer_invoice_tue_product_service', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }

        }  else if ($printTemplate['printTemplateID'] == 16) {
            if($type == 1)
            {
                // return $array;
                $html = view('print.customer_invoice_template_ksa', $array);
                $htmlFooter = view('print.customer_invoice_template_ksa_footer', $array);
                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter($htmlFooter);
                $mpdf->WriteHTML($html);
                return $mpdf->Output($fileName, 'I');
            }
            else if($type == 2)
            {
                return \Excel::create($fileName_csv, function ($excel) use ($array) {
                    $excel->sheet('New sheet', function ($sheet) use ($array) {
                        $sheet->loadView('export_report.customer_invoice_template_ksa', $array)->with('no_asset', true);
                    });
                })->download('csv');
            }

        }
    }

    public function getProformaInvoiceDetailDataForProductServiceContract($id, $companyID)
    {
        $invoiceDetails = DB::select("SELECT ClientRef, OurRef, SUM(qty) as qty, rate, SUM( qty * rate ) AS amount,assetDescription FROM ( SELECT freebilling.ContractDetailID, billProcessNo, assetDescription, freebilling.qtyServiceProduct AS qty, IFNULL( standardRate, 0 ) + IFNULL( operationRate, 0 ) AS rate, freebilling.performaInvoiceNo, freebilling.pl3, freebilling.TicketNo, freebilling.companyID,freebilling.mitID FROM ( SELECT performaMasterID FROM `erp_custinvoicedirectdet` WHERE `custInvoiceDirectID` = $id GROUP BY performaMasterID ) t INNER JOIN freebilling ON freebilling.companyID = '$companyID' AND freebilling.performaInvoiceNo = t.performaMasterID INNER JOIN ticketmaster ON freebilling.TicketNo = ticketmaster.ticketidAtuto LEFT JOIN rigmaster on ticketmaster.regName = rigmaster.idrigmaster ) t LEFT JOIN contractdetails ON contractdetails.ContractDetailID = t.ContractDetailID GROUP BY t.ContractDetailID, rate ORDER BY  t.mitID ASC");

        return $invoiceDetails;
    }

    public function getProformaInvoiceDetailDataForPrintInvoice($id)
    {
        $output = DB::select("SELECT
    performamaster.PerformaCode,
    freebilling.idBillingNO,
    contractdetails.ItemDescrip AS description,
    freebilling.mitQty AS Qty,
    freebilling.operationTimeOnLoc AS Days_OP,
    freebilling.operationRate AS Price_OP,
    freebilling.StandardTimeOnLoc AS Days_STB,
    freebilling.standardRate AS Price_STB,
    (
        ( freebilling.mitQty * freebilling.StandardTimeOnLoc * freebilling.standardRate ) + ( freebilling.mitQty * freebilling.operationTimeOnLoc * freebilling.operationRate ) 
    ) AS total 
FROM
    erp_custinvoicedirectdet
    INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
    INNER JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
    AND performamaster.companySystemID = erp_custinvoicedirect.companySystemID 
    AND performamaster.customerSystemID = erp_custinvoicedirectdet.customerID
    INNER JOIN contractdetails ON contractdetails.CompanyID = erp_custinvoicedirect.companyID 
    AND contractdetails.contractUID = erp_custinvoicedirectdet.ContractID
    INNER JOIN contractdetailsassets ON contractdetailsassets.contractDetailID = contractdetails.ContractDetailID
    INNER JOIN freebilling ON freebilling.performaInvoiceNo = performamaster.PerformaInvoiceNo 
    AND freebilling.companyID = erp_custinvoicedirectdet.companyID 
    AND freebilling.ContractDetailID = contractdetails.ContractDetailID 
    AND freebilling.AssetUnitID = contractdetailsassets.assetUnitID 
WHERE
    erp_custinvoicedirectdet.custInvoiceDirectID = $id 
GROUP BY
    contractdetails.ContractDetailID,
    contractdetailsassets.assetUnitID UNION
SELECT
    performamaster.PerformaCode,
    otherscharges.idOtherCharges,
    mubbadrahop.otherscharges.Description,
    mubbadrahop.otherscharges.qty AS Qty,
    '1' AS Days_OP,
    mubbadrahop.otherscharges.Rate AS Price_OP,
    '0' AS Days_STB,
    '0' AS Price_STB,
    ( mubbadrahop.otherscharges.qty * mubbadrahop.otherscharges.Rate ) AS total 
FROM
    erp_custinvoicedirectdet
    INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
    INNER JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
    AND performamaster.companySystemID = erp_custinvoicedirect.companySystemID 
    AND performamaster.customerSystemID = erp_custinvoicedirectdet.customerID
    #LEFT JOIN freebilling ON freebilling.performaInvoiceNo = performamaster.PerformaInvoiceNo 
    #AND freebilling.companyID = erp_custinvoicedirectdet.companyID
    LEFT JOIN freebillingmasterperforma ON freebillingmasterperforma.companyID= performamaster.companyID
    AND freebillingmasterperforma.PerformaInvoiceNo=performamaster.PerformaInvoiceNo
    #LEFT JOIN contractdetails ON contractdetails.ContractDetailID = freebilling.ContractDetailID 
    #AND contractdetails.CompanyID = freebillingmasterperforma.companyID
    LEFT JOIN mubbadrahop.otherscharges ON mubbadrahop.otherscharges.BillProcessNO = freebillingmasterperforma.billProcessNo 
WHERE
    erp_custinvoicedirectdet.custInvoiceDirectID = $id  
GROUP BY
    mubbadrahop.otherscharges.Description UNION
SELECT
    performamaster.PerformaCode,
    fishingengineerscharges.idFECharges,
    contractdetails.ItemDescrip AS Description,
    '1' AS Qty,
    mubbadrahop.fishingengineerscharges.TotalDays AS Days_OP,
    mubbadrahop.fishingengineerscharges.feRate AS Price_OP,
    '0' AS Days_STB,
    '0' AS Price_STB,
    mubbadrahop.fishingengineerscharges.TotalAmount AS total 
FROM
    erp_custinvoicedirectdet
    INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
    INNER JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
    AND performamaster.companySystemID = erp_custinvoicedirect.companySystemID 
    AND performamaster.customerSystemID = erp_custinvoicedirectdet.customerID
    INNER JOIN contractdetails ON contractdetails.contractUID = erp_custinvoicedirectdet.contractID 
    AND contractdetails.CompanyID = erp_custinvoicedirectdet.companyID
    INNER JOIN mubbadrahop.fishingengineerscharges ON mubbadrahop.fishingengineerscharges.feContractDetailID = contractdetails.ContractDetailID 
    AND mubbadrahop.fishingengineerscharges.feContractID = erp_custinvoicedirectdet.clientContractID 
WHERE
    erp_custinvoicedirectdet.custInvoiceDirectID = $id  
GROUP BY
    mubbadrahop.fishingengineerscharges.feContractDetailID,
    mubbadrahop.fishingengineerscharges.feDateFrom UNION
SELECT
    performamaster.PerformaCode,
    mitmaster.mitReturnMasterID,
    CONCAT( mitmaster.mitIDText, '-', mubbadrahop.mittrasportationbilling.Description ) AS Description,
    '1' AS Qty,
    '1' AS Days_OP,
    mubbadrahop.mittrasportationbilling.charges AS Price_OP,
    '0' AS Days_STB,
    '0' AS Price_STB,
    mubbadrahop.mittrasportationbilling.charges AS total 
FROM
    erp_custinvoicedirectdet
    INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
    INNER JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
    AND performamaster.companySystemID = erp_custinvoicedirect.companySystemID 
    AND performamaster.customerSystemID = erp_custinvoicedirectdet.customerID
    LEFT JOIN freebilling ON freebilling.performaInvoiceNo = performamaster.PerformaInvoiceNo 
    AND freebilling.companyID = erp_custinvoicedirectdet.companyID
    LEFT JOIN contractdetails ON contractdetails.ContractDetailID = freebilling.ContractDetailID 
    AND contractdetails.CompanyID = freebilling.companyID
    LEFT JOIN mubbadrahop.mittrasportationbilling ON mubbadrahop.mittrasportationbilling.BillProcessNO = freebilling.billProcessNo
    LEFT JOIN mitmaster ON mitmaster.mitReturnMasterID = mubbadrahop.mittrasportationbilling.mitID 
WHERE
    erp_custinvoicedirectdet.custInvoiceDirectID = $id  
    AND mitmaster.mitReturnMasterID>0
GROUP BY
    mubbadrahop.mittrasportationbilling.mitID UNION
SELECT
    performamaster.PerformaCode,
    motmaster.motID,
    CONCAT( motmaster.motNoText, '-', mubbadrahop.mottrasportationbilling.Description ) AS Description,
    '1' AS Qty,
    '1' AS Days_OP,
    mubbadrahop.mottrasportationbilling.charges AS Price_OP,
    '0' AS Days_STB,
    '0' AS Price_STB,
    mubbadrahop.mottrasportationbilling.charges AS total 
FROM
    erp_custinvoicedirectdet
    INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
    INNER JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
    AND performamaster.companySystemID = erp_custinvoicedirect.companySystemID 
    AND performamaster.customerSystemID = erp_custinvoicedirectdet.customerID
    LEFT JOIN freebilling ON freebilling.performaInvoiceNo = performamaster.PerformaInvoiceNo 
    AND freebilling.companyID = erp_custinvoicedirectdet.companyID
    LEFT JOIN contractdetails ON contractdetails.ContractDetailID = freebilling.ContractDetailID 
    AND contractdetails.CompanyID = freebilling.companyID
    LEFT JOIN mubbadrahop.mottrasportationbilling ON mubbadrahop.mottrasportationbilling.BillProcessNO = freebilling.billProcessNo
    LEFT JOIN motmaster ON motmaster.motID = mubbadrahop.mottrasportationbilling.motID 
WHERE
    erp_custinvoicedirectdet.custInvoiceDirectID = $id  
    AND motmaster.motID>0
GROUP BY
    mubbadrahop.mottrasportationbilling.motID UNION
SELECT
    performamaster.PerformaCode,
    freebillingmasterperforma.billProcessNo,
    billingusagecharges.usageTypeDes AS description,
    '1' AS Qty,
    '0' AS Days_OP,
    '0' AS Price_OP,
    '0' AS Days_STB,
    '0' AS Price_STB,
    billingusagecharges.totalRate AS total 
FROM
    erp_custinvoicedirectdet
    INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
    INNER JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
    AND performamaster.companySystemID = erp_custinvoicedirect.companySystemID 
    AND performamaster.customerSystemID = erp_custinvoicedirectdet.customerID
    INNER JOIN freebillingmasterperforma ON freebillingmasterperforma.PerformaInvoiceNo = performamaster.PerformaInvoiceNo 
    AND freebillingmasterperforma.clientID = performamaster.clientID 
    AND freebillingmasterperforma.companyID = performamaster.companyID
    INNER JOIN (
    SELECT
        billingusagecharges.billProcessNo,
        billingusagecharges.companyID,
        mubbadrahop.usagetypes.usageTypeDes,
        billingusagecharges.usageRateTypeId,
        sum( billingusagecharges.usageRate ) AS totalRate 
    FROM
        billingusagecharges
        INNER JOIN mubbadrahop.usagetypes ON billingusagecharges.usageRateTypeId = mubbadrahop.usagetypes.usageTypeID 
    GROUP BY
        billingusagecharges.billProcessNo,
        billingusagecharges.usageRateTypeId 
    ) AS billingusagecharges ON billingusagecharges.billProcessNo = freebillingmasterperforma.BillProcessNO 
    AND billingusagecharges.companyID = freebillingmasterperforma.companyID 
WHERE
    erp_custinvoicedirectdet.custInvoiceDirectID = $id  
GROUP BY
    erp_custinvoicedirectdet.custInvoiceDirectID,
    erp_custinvoicedirectdet.performaMasterID,
    billingusagecharges.usageRateTypeId");

        return $output;
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

        DocumentApproved::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $invoice->companySystemID)
            ->where('documentSystemID', $invoice->documentSystemiD)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($invoice->documentSystemiD,$custInvoiceDirectAutoID,$input['reopenComments'],'Reopened');

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
        },'audit_trial.modified_by'])->findWithoutFail($id);


        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'GRV retrieved successfully');
    }

    public function getAllcontractbyclient(request $request)
    {
        $input = $request->all();
        $custInvDirDetAutoID = $input['custInvDirDetAutoID'];
        $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $custInvDirDetAutoID)->first();
        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();

        if ($master->customerID != '' || $master->customerID != 0) {
            $qry = "SELECT contractUID, ContractNumber FROM contractmaster WHERE companySystemID = $master->companySystemID AND clientID = $master->customerID;";
            $contract = DB::select($qry);

            return $this->sendResponse($contract, 'Record retrieved successfully');
        }

    }

    public function getAllcontractbyclientbase(request $request)
    {
        $input = $request->all();

        $custInvDirDetAutoID = $input['creditNoteDetailsID'];
        $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $custInvDirDetAutoID)->first();
        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();

        $qry = "SELECT contractUID, ContractNumber FROM contractmaster WHERE ServiceLineCode = '{$detail->serviceLineCode}' AND companySystemID = $master->companySystemID AND clientID = $master->customerID;";
        $contract = DB::select($qry);


        return $this->sendResponse($contract, 'Record retrieved successfully');
    }


    function customerInvoiceReceiptStatus(Request $request)
    {
        $input = $request->all();
        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $input['id'])->first();

        $master = DB::select("SELECT
	erp_custreceivepaymentdet.custReceivePaymentAutoID,
	erp_custreceivepaymentdet.companyID,
	erp_customerreceivepayment.custPaymentReceiveCode,
	erp_customerreceivepayment.custPaymentReceiveDate,
IF
	(erp_custreceivepaymentdet.matchingDocID = 0 
	OR erp_custreceivepaymentdet.matchingDocID IS NULL,
	erp_customerreceivepayment.custPaymentReceiveCode,
	erp_matchdocumentmaster.matchingDocCode) AS docCode,
	
IF
	(erp_custreceivepaymentdet.matchingDocID = 0 
	OR erp_custreceivepaymentdet.matchingDocID IS NULL,
	erp_customerreceivepayment.custPaymentReceiveDate,
	erp_matchdocumentmaster.matchingDocdate ) AS docDate,
	erp_custreceivepaymentdet.bookingInvCodeSystem,
	erp_custreceivepaymentdet.addedDocumentID,
	erp_custreceivepaymentdet.custTransactionCurrencyID,
	currencymaster.CurrencyCode,currencymaster.DecimalPlaces,
	erp_custreceivepaymentdet.receiveAmountTrans as amount,
	erp_customerreceivepayment.confirmedYN,
	erp_customerreceivepayment.approved,
	erp_matchdocumentmaster.matchingConfirmedYN 
FROM
	erp_custreceivepaymentdet
	LEFT JOIN currencymaster ON erp_custreceivepaymentdet.custTransactionCurrencyID = currencymaster.currencyID
	LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
	LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID 
WHERE
	erp_custreceivepaymentdet.companySystemID = $master->companySystemID
	AND erp_custreceivepaymentdet.bookingInvCodeSystem = $master->custInvoiceDirectAutoID 
	AND erp_custreceivepaymentdet.addedDocumentSystemID = $master->documentSystemiD");

        return $this->sendResponse($master, 'Contract deleted successfully');
    }

    public function getCustomerInvoiceApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $fromPms = (isset($input['fromPms']) && $input['fromPms']) ? true : false;

        $companyID = $request->companyId;

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $empID = $employee->employeeSystemID;
        }
        else{
            $empID = \Helper::getEmployeeSystemID();
        }

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 20)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_custinvoicedirect.custInvoiceDirectAutoID',
            'erp_custinvoicedirect.bookingInvCode',
            'erp_custinvoicedirect.documentSystemiD as documentSystemID',
            'erp_custinvoicedirect.bookingDate',
            'erp_custinvoicedirect.comments',
            'erp_custinvoicedirect.createdDateAndTime',
            'erp_custinvoicedirect.confirmedDate',
            'erp_custinvoicedirect.bookingAmountTrans',
            'erp_custinvoicedirect.VATAmount',
            'erp_custinvoicedirect.isPerforma',
            'erp_custinvoicedirect.documentType',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        );

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
        }
        else{
            $grvMasters->addSelect('employeesdepartments.approvalDeligated');
        }

        $grvMasters->join('erp_custinvoicedirect', function ($query) use ($companyID, $empID, $fromPms) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'custInvoiceDirectAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_custinvoicedirect.companySystemID', $companyID)
                ->where('erp_custinvoicedirect.approved', 0)
                ->where('erp_custinvoicedirect.confirmedYN', 1)
                ->when(!$fromPms, function($query) {
                    $query->where('erp_custinvoicedirect.createdFrom', '!=', 5);
                })
                ->when($fromPms, function($query) {
                    $query->where('erp_custinvoicedirect.createdFrom', 5);
                });
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'custTransactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 20)
            ->where('erp_documentapproved.companySystemID', $companyID);

        if(!isset($input['isAutoCreateDocument'])){

            $grvMasters->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }
                $query->where('employeesdepartments.documentSystemID', 20)
                    ->where('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            });

            $search = $request->input('search.value');

            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $grvMasters = $grvMasters->where(function ($query) use ($search) {
                    $query->where('bookingInvCode', 'LIKE', "%{$search}%")
                        ->orWhere('comments', 'LIKE', "%{$search}%")
                        ->orWhere('CustomerName', 'LIKE', "%{$search}%");
                });
            }
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        $inovicePolicy =  \Helper::checkPolicy($input['companyId'],44);

        if ($isEmployeeDischarched == 'true') {
            $grvMasters = [];
        }

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if(!empty($grvMasters)){
                $grvMasters = $grvMasters->where('erp_custinvoicedirect.custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->first();
                return [
                    "success" => true,
                    "data" => $grvMasters
                ];
            }
            else{
                return [
                    "success" => false,
                    "message" => "Employee discharged"
                ];
            }
        }
        else{
            return \DataTables::of($grvMasters)
                ->order(function ($query) use ($input) {
                    if (request()->has('order')) {
                        if ($input['order'][0]['column'] == 0) {
                            $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                        }
                    }
                })
                ->addIndexColumn()
                ->with('orderCondition', $sort)
                ->addColumn('Actions', 'Actions', "Actions")
                ->addColumn('total', function($inv) {
                    return $this->getTotalAfterGL($inv);
                })
                //->addColumn('Index', 'Index', "Index")
                ->make(true);
        }
    }

    public function getApprovedCustomerInvoiceForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $fromPms = (isset($input['fromPms']) && $input['fromPms']) ? true : false;

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_custinvoicedirect.custInvoiceDirectAutoID',
            'erp_custinvoicedirect.bookingInvCode',
            'erp_custinvoicedirect.documentSystemiD',
            'erp_custinvoicedirect.bookingDate',
            'erp_custinvoicedirect.comments',
            'erp_custinvoicedirect.createdDateAndTime',
            'erp_custinvoicedirect.confirmedDate',
            'erp_custinvoicedirect.bookingAmountTrans',
            'erp_custinvoicedirect.VATAmount',
            'erp_custinvoicedirect.isPerforma',
            'erp_custinvoicedirect.documentType',
            'erp_custinvoicedirect.approvedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_custinvoicedirect', function ($query) use ($companyID, $empID, $fromPms) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'custInvoiceDirectAutoID')
                ->where('erp_custinvoicedirect.companySystemID', $companyID)
                ->where('erp_custinvoicedirect.approved', -1)
                ->where('erp_custinvoicedirect.confirmedYN', 1)
                 ->when(!$fromPms, function($query) {
                    $query->where('erp_custinvoicedirect.createdFrom', '!=', 5);
                })
                ->when($fromPms, function($query) {
                    $query->where('erp_custinvoicedirect.createdFrom', 5);
                });
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'custTransactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->where('erp_documentapproved.documentSystemID', 20)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::of($grvMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('total', function($inv) {
                return $this->getTotalAfterGL($inv);
            })
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function approvalPreCheckCustomerInvoice(Request $request)
    {
        $approve = \Helper::postedDatePromptInFinalApproval($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"], 500, ['type' => $approve["type"]]);
        } else {
            return $this->sendResponse(array('type' => $approve["type"]), $approve["message"]);
        }

    }

    public function approveCustomerInvoice(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }
    }


    public function rejectCustomerInvoice(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getCustomerInvoiceAmend(Request $request)
    {
        $input = $request->all();

        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $customerInvoiceDirectData = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);
        if (empty($customerInvoiceDirectData)) {
            return $this->sendError('Customer Invoice not found');
        }
        
        // if ($customerInvoiceDirectData->refferedBackYN != -1) {
        //     return $this->sendError('You cannot refer back this Customer Invoice');
        // }

        $customerInvoiceArray = $customerInvoiceDirectData->toArray();
        unset($customerInvoiceArray['isVatEligible']);

        CustomerInvoiceDirectRefferedback::insert($customerInvoiceArray);


        if($customerInvoiceDirectData->isPerforma == 0 || $customerInvoiceDirectData->isPerforma == 1){
            $fetchCustomerInvoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)
                ->get();
        }else{
            $fetchCustomerInvoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                ->get();
        }

        if (!empty($fetchCustomerInvoiceDetails)) {
            foreach ($fetchCustomerInvoiceDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $customerInvoiceDirectData->timesReferred;
            }
        }

        $customerInvoiceDetailArray = $fetchCustomerInvoiceDetails->toArray();

        if($customerInvoiceDirectData->isPerforma == 0 || $customerInvoiceDirectData->isPerforma == 1){
            CustomerInvoiceDirectDetRefferedback::insert($customerInvoiceDetailArray);
        }else{
            foreach ($customerInvoiceDetailArray as $key => $valueItem) {
                $res = CustomerInvoiceItemDetailsRefferedback::create($valueItem);
            }
        }

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $customerInvoiceDirectData->companySystemID)
            ->where('documentSystemID', $customerInvoiceDirectData->documentSystemiD)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $customerInvoiceDirectData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $customerInvoiceDirectData->companySystemID)
            ->where('documentSystemID', $customerInvoiceDirectData->documentSystemiD)
            ->delete();

        if ($deleteApproval) {
            $customerInvoiceDirectData->refferedBackYN = 0;
            $customerInvoiceDirectData->confirmedYN = 0;
            $customerInvoiceDirectData->confirmedByEmpSystemID = null;
            $customerInvoiceDirectData->confirmedByEmpID = null;
            $customerInvoiceDirectData->confirmedByName = null;
            $customerInvoiceDirectData->confirmedDate = null;
            $customerInvoiceDirectData->RollLevForApp_curr = 1;
            $customerInvoiceDirectData->save();
        }

        // delete tax details
        /*$checkTaxExist = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $customerInvoiceDirectData->companySystemID)
            ->where('documentSystemID', 20)
            ->first();

        if ($checkTaxExist) {
            $deleteTaxDetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                ->where('companySystemID', $customerInvoiceDirectData->companySystemID)
                ->where('documentSystemID', 20)
                ->delete();
        }*/

        return $this->sendResponse($customerInvoiceDirectData->toArray(), 'Customer Invoice Amend successfully');
    }

    public function customerInvoiceCancel(Request $request)
    {
        $input = $request->all();

        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $customerInvoiceDirectData = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);
        if (empty($customerInvoiceDirectData)) {
            return $this->sendError('Customer Invoice not found');
        }

        if ($customerInvoiceDirectData->confirmedYN == 1) {
            return $this->sendError('You cannot cancel this customer invoice, this is already confirmed');
        }

        if ($customerInvoiceDirectData->approved == -1) {
            return $this->sendError('You cannot cancel this customer invoice, this is already approved');
        }

        if ($customerInvoiceDirectData->canceledYN == -1) {
            return $this->sendError('You cannot cancel this customer invoice, this is already cancelled');
        }

        $customerDirectDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->get();

        if (count($customerDirectDetail) > 0) {
            return $this->sendError('You cannot cancel this customer invoice, invoice details are exist');
        }

        $employee = \Helper::getEmployeeInfo();

        $customerInvoiceDirectData->canceledYN = -1;
        $customerInvoiceDirectData->canceledComments = $request['cancelComments'];
        $customerInvoiceDirectData->canceledDateTime = NOW();
        $customerInvoiceDirectData->canceledByEmpSystemID = \Helper::getEmployeeSystemID();
        $customerInvoiceDirectData->canceledByEmpID = $employee->empID;
        $customerInvoiceDirectData->canceledByEmpName = $employee->empFullName;
        $customerInvoiceDirectData->customerInvoiceNo = null;
        $customerInvoiceDirectData->save();

        /*Audit entry*/
        AuditTrial::createAuditTrial($customerInvoiceDirectData->documentSystemiD,$custInvoiceDirectAutoID,$request['cancelComments'],'Cancelled');

        return $this->sendResponse($customerInvoiceDirectData->toArray(), 'Customer invoice cancelled successfully');
    }

    public function customerInvoiceCancelAPI(Request $request)
    {
        $input = $request->all();

        $customerInvoiceNo = $input['customerInvoiceNo'];

        $masterData = CustomerInvoiceDirect::where('customerInvoiceNo',$customerInvoiceNo)->first();
        if (empty($masterData)) {
            return $this->sendError('Customer Invoice not found');
        }



        $id = $masterData->custInvoiceDirectAutoID;

        $errorMessageAPI = 'The invoice ' . $customerInvoiceNo . ' / ' . $masterData->bookingInvCode .  ' cannot be cancelled as ';
        $successMessage = 'The invoice ' . $customerInvoiceNo . ' / ' . $masterData->bookingInvCode .  ' has been cancelled Successfully';

        if($masterData->isPerforma != 0) {
            $errorMessage = $errorMessageAPI . 'You can do the cancellation for Direct Customer invoice only';
            return $this->sendError($errorMessage, 422);
        }


        $documentAutoId = $id;
        $documentSystemID = $masterData->documentSystemiD;



        $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID);
        if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
            $errorMessage = $errorMessageAPI . 'the Invoice posted period is not Active and Current for cancellation';
            return $this->sendError($errorMessage, 422);
        }

        DB::beginTransaction();
        try {

            if ($masterData->approved == -1) {
                $amendCustomerInvoice = CustomerInvoiceServices::amendCustomerInvoice($input,$id,$masterData,$isFromAPI = true);

                if(isset($amendCustomerInvoice['status']) && $amendCustomerInvoice['status'] == false){
                   $errorMessage = $errorMessageAPI . $amendCustomerInvoice['message'];
                   return $this->sendError($errorMessage, 422);
               }
            } 


            $customerInvoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->count();

            if ($customerInvoiceDetail > 0) {
                
                $deleteDetails = CustomerInvoiceServices::deleteDetails($id, $isFromAPI = true);
                if(isset($deleteDetails['status']) && $deleteDetails['status'] == false){
                    $errorMessage = $errorMessageAPI . $deleteDetails['message'];
                    return $this->sendError($errorMessage, 422);
                }
                
            }
            

            $masterData = CustomerInvoiceDirect::where('customerInvoiceNo',$customerInvoiceNo)->first();
            $cancelCustomerInvoice = CustomerInvoiceServices::cancelCustomerInvoice($input,$id,$masterData,$isFromAPI = true);
            if(isset($cancelCustomerInvoice['status']) && $cancelCustomerInvoice['status'] == false){
               $errorMessage = $errorMessageAPI . $cancelCustomerInvoice['message'];
               return $this->sendError($errorMessage, 422);
            }

            DB::commit();
            return $this->sendResponse($masterData->toArray(), $successMessage );
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function amendCustomerInvoiceReview(Request $request)
    {
        $input = $request->all();

        $id = $input['custInvoiceDirectAutoID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = CustomerInvoiceDirect::find($id);

        if (empty($masterData)) {
            return $this->sendError('Customer Invoice not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this Customer Invoice, it is not confirmed');
        }

        $isAPIDocument = DocumentSystemMapping::where('documentId',$id)->where('documentSystemID',20)->exists();
        if ($isAPIDocument){
            return $this->sendError('This is an autogenerated document. This cannot be returned back to amend');
        }

        $documentAutoId = $id;
        $documentSystemID = $masterData->documentSystemiD;

        if($masterData->approved == -1){
            $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID);
            if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                    return $this->sendError($validateFinanceYear['message']);
                }
            }
            
            $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID);
            if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                    return $this->sendError($validateFinancePeriod['message']);
                }
            }

            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId,$documentSystemID);
            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                    return $this->sendError($validatePendingGlPost['message']);
                }
            }

            $validateVatReturnFilling = ValidateDocumentAmend::validateVatReturnFilling($documentAutoId,$documentSystemID,$masterData->companySystemID);
            if(isset($validateVatReturnFilling['status']) && $validateVatReturnFilling['status'] == false){
                $errorMessage = "Customer Invoice " . $validateVatReturnFilling['message'];
                return $this->sendError($errorMessage);
            }
        }


        if($masterData->isPerforma == 2){
            $checkForInventoryItems = CustomerInvoiceItemDetails::where('itemFinanceCategoryID', 1)
                                                                ->where('custInvoiceDirectAutoID', $id)
                                                                ->first();

            if ($checkForInventoryItems) {
                return $this->sendError('Selected customer invoice cannot be returned back to amend as the invoice is Item Sales Invoice, it contains inventory items');
            }
        }elseif ($masterData->isPerforma == 4){
            return $this->sendError('Selected customer invoice cannot be returned back to amend as the invoice is From Sales Order');
        }elseif ($masterData->isPerforma == 5){
            return $this->sendError('Selected customer invoice cannot be returned back to amend as the invoice is From Quotation');
        }




        DB::beginTransaction();
        try {

             $amendCI = CustomerInvoiceServices::amendCustomerInvoice($input,$id,$masterData);

             if(isset($amendCI['status']) && $amendCI['status'] == false){
                $errorMessage = "Customer Invoice " . $amendCI['message'];
                return $this->sendError($errorMessage);
            }
             $emailBody = '<p>' . $masterData->bookingInvCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
             $emailSubject = $masterData->bookingInvCode . ' has been return back to amend';
             
            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemiD,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $id,
                    'docCode' => $masterData->bookingInvCode
                );
            }

            $documentApproval = DocumentApproved::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $id)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemiD,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $id,
                        'docCode' => $masterData->bookingInvCode
                    );
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            DB::commit();
            return $this->sendResponse($masterData->toArray(), 'Customer Invoice amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getPerformaPDOInvoiceDetail($master, $customerCode)
    {

//         $output = DB::select("
//                             SELECT
// 	client_referance,
// 	po_detail_id,
// 	item_description,
// 	qty,
// 	unit_price,
//     percentage,
//     vatAmount,
// 	amount 
// FROM
// 	(
// 			SELECT
// 				poLineNo as po_detail_id,
// 				ClientRef as client_referance,
// 				ItemDescrip as item_description,
// 				prod_serv.TicketNo as TicketNo,
// 				qty,
// 				unit_price,
//                 prod_serv.percentage,
//                 prod_serv.vatAmount,
// 				amount
// 			FROM
// 			erp_custinvoicedirect 
// 			JOIN erp_custinvoicedirectdet ON erp_custinvoicedirect.custInvoiceDirectAutoID=erp_custinvoicedirectdet.custInvoiceDirectID  AND custInvoiceDirectAutoID=" . $master->custInvoiceDirectAutoID . "  AND erp_custinvoicedirectdet.customerID=" . $master->customerID . " 
// 				JOIN performatemp ON erp_custinvoicedirectdet.performaMasterID = performatemp.performaInvoiceNo 

// 				JOIN (
// 								SELECT
// 									mubbadrahop.productdetails.contractDetailID as contractDetailID,
// 									mubbadrahop.productdetails.TicketNo as TicketNo,
// 									mubbadrahop.productdetails.Qty AS qty,
// 									mubbadrahop.productdetails.UnitRate AS unit_price,
//                                     mubbadrahop.productdetails.vatAmount AS vatAmount,
//                                     mubbadrahop.productdetails.percentage AS percentage,
// 									mubbadrahop.productdetails.TotalCharges AS amount 
// 								FROM
// 									mubbadrahop.productdetails 
// 									WHERE mubbadrahop.productdetails.companyID = '" . $master->companyID . "' AND mubbadrahop.productdetails.CustomerID='" . $customerCode . "'
									
// 									UNION
									
// 								SELECT
// 									mubbadrahop.servicedetails.contractDetailID as contractDetailID,
// 									mubbadrahop.servicedetails.TicketNo as TicketNo,
// 									mubbadrahop.servicedetails.Qty AS qty,
// 									mubbadrahop.servicedetails.UnitRate AS unit_price,
//                                     mubbadrahop.servicedetails.vatAmount AS vatAmount,
//                                     mubbadrahop.servicedetails.percentage AS percentage,
// 									mubbadrahop.servicedetails.TotalCharges AS amount 
// 								FROM 
// 									mubbadrahop.servicedetails 
// 									WHERE mubbadrahop.servicedetails .companyID = '" . $master->companyID . "' AND mubbadrahop.servicedetails.CustomerID='" . $customerCode . "'
									
// 				) as prod_serv ON performatemp.TicketNo=prod_serv.TicketNo 
// 				JOIN contractdetails ON prod_serv.contractDetailID=contractdetails.ContractDetailID
// 				WHERE contractdetails.CompanyID='" . $master->companyID . "' AND contractdetails.CustomerID='" . $customerCode . "'
// 				GROUP BY contractdetails.ContractDetailID
// 	) AS temp
//                             ");


        $output = DB::select("SELECT
                                client_referance,
                                po_detail_id,
                                item_description,
                                qty,
                                unit_price,
                                percentage,
                                vatAmount,
                                amount
                            FROM
                                (
                                SELECT
                                    ticketId,
                                    prod_serv.poLineNo AS po_detail_id,
                                    ClientRef AS client_referance,
                                    ItemDescrip AS item_description,
                                    prod_serv.TicketNo AS TicketNo,
                                    qty,
                                    unit_price,
                                    prod_serv.percentage,
                                    prod_serv.vatAmount,
                                    amount
                                FROM
                                    erp_custinvoicedirect
                                    JOIN erp_custinvoicedirectdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
                                    AND custInvoiceDirectAutoID = " . $master->custInvoiceDirectAutoID . " 
                                    AND erp_custinvoicedirectdet.customerID = " . $master->customerID . " 
                                    JOIN performatemp ON erp_custinvoicedirectdet.performaMasterID = performatemp.performaInvoiceNo
                                    AND erp_custinvoicedirectdet.glCode = performatemp.stdGlCode
                                    JOIN (
                                    SELECT
                                        CONCAT(mubbadrahop.productdetails.TicketproductID, 'pr') as ticketId,
                                        mubbadrahop.productdetails.contractDetailID AS contractDetailID,
                                        mubbadrahop.productdetails.TicketNo AS TicketNo,
                                        mubbadrahop.productdetails.Qty AS qty,
                                        mubbadrahop.productdetails.UnitRate AS unit_price,
                                        mubbadrahop.productdetails.vatAmount AS vatAmount,
                                        mubbadrahop.productdetails.percentage AS percentage,
                                        mubbadrahop.productdetails.TotalCharges AS amount,
                                        mubbadrahop.productdetails.glCodeProduct AS glCodeService,
                                        mubbadrahop.productdetails.pl3 AS poLineNo
                                    FROM
                                        mubbadrahop.productdetails
                                    WHERE
                                        mubbadrahop.productdetails.companyID = '" . $master->companyID . "'
                                        AND mubbadrahop.productdetails.CustomerID = '" . $customerCode . "' UNION
                                    SELECT
                                        CONCAT(mubbadrahop.servicedetails.TicketServiceID, 'sr') as ticketId,
                                        mubbadrahop.servicedetails.contractDetailID AS contractDetailID,
                                        mubbadrahop.servicedetails.TicketNo AS TicketNo,
                                        mubbadrahop.servicedetails.Qty AS qty,
                                        mubbadrahop.servicedetails.UnitRate AS unit_price,
                                        mubbadrahop.servicedetails.vatAmount AS vatAmount,
                                        mubbadrahop.servicedetails.percentage AS percentage,
                                        mubbadrahop.servicedetails.TotalCharges AS amount,
                                        mubbadrahop.servicedetails.glCodeService AS glCodeService,
                                        '' AS poLineNo
                                    FROM
                                        mubbadrahop.servicedetails
                                    WHERE
                                        mubbadrahop.servicedetails.companyID = '" . $master->companyID . "'
                                        AND mubbadrahop.servicedetails.CustomerID = '" . $customerCode . "'
                                    ) AS prod_serv ON performatemp.TicketNo = prod_serv.TicketNo
                                    AND performatemp.stdGLCode = prod_serv.glCodeService
                                    JOIN contractdetails ON prod_serv.contractDetailID = contractdetails.ContractDetailID
                                WHERE
                                    contractdetails.CompanyID ='" . $master->companyID . "'
                                    AND contractdetails.CustomerID = '" . $customerCode . "'
                                    GROUP BY ticketId
                                
                                ) AS temp");

        return $output;
    }

    public function clearCustomerInvoiceNumber(Request $request)
    {
        $input = $request->all();

        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $customerInvoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);
        if (empty($customerInvoice)) {
            return $this->sendError('Customer Invoice not found');
        }

        // updating fields
        $customerInvoice->customerInvoiceNo = null;
        $customerInvoice->save();

        return $this->sendResponse($customerInvoice, 'Record updated successfully');
    }

    public function savecustomerInvoiceProformaTaxDetails($custInvoiceDirectAutoID, $totalVATAmount)
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

        $totalDetail = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as amount, SUM(VATAmount) as vatAmount"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount - $totalDetail->vatAmount;
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
        // $vatAmount['VATPercentage'] = $percentage;
        // $vatAmount['VATAmount'] = $_post['amount'];
        // $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        // $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }

    public static function getTotalAfterGL($invoice) {
            $total = 0;
            $_customerInvoiceDirectDetails = CustomerInvoiceDirectDetail::with(['chart_Of_account'])->where('custInvoiceDirectID',$invoice->custInvoiceDirectAutoID)->get();
            $total = $invoice->bookingAmountTrans;
            if(count($_customerInvoiceDirectDetails) > 0 && $invoice->isPerforma == 2) {
                foreach ($_customerInvoiceDirectDetails as $item) {

                    if(isset($item->chart_Of_account)) {
                        if($item->chart_Of_account->controlAccountsSystemID == 2 || $item->chart_Of_account->controlAccountsSystemID == 5 || $item->chart_Of_account->controlAccountsSystemID == 3) {
                            $total -= ($item->invoiceAmount + $item->VATAmountTotal);
                        }else{
                            $total += ($item->invoiceAmount + $item->VATAmountTotal);
                        }
                    }
                }
            }

           return $total;
    }
}
