<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentAPIRequest;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerAssigned;
use App\Models\CurrencyMaster;
use App\Models\customercurrency;
use App\Models\Company;
use App\Models\CustomerMaster;
use App\Models\BankAccount;
use App\Models\SegmentMaster;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DirectReceiptDetail;
use App\Models\BankAssign;
use App\Models\CompanyFinancePeriod;
use App\Models\YesNoSelectionForMinus;
use App\Models\YesNoSelection;
use App\Models\Months;
use App\Repositories\CustomerReceivePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;

/**
 * Class CustomerReceivePaymentController
 * @package App\Http\Controllers\API
 */
class CustomerReceivePaymentAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentRepository */
    private $customerReceivePaymentRepository;

    public function __construct(CustomerReceivePaymentRepository $customerReceivePaymentRepo)
    {
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePayments",
     *      summary="Get a listing of the CustomerReceivePayments.",
     *      tags={"CustomerReceivePayment"},
     *      description="Get all CustomerReceivePayments",
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
     *                  @SWG\Items(ref="#/definitions/CustomerReceivePayment")
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
        $this->customerReceivePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->customerReceivePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerReceivePayments = $this->customerReceivePaymentRepository->all();

        return $this->sendResponse($customerReceivePayments->toArray(), 'Customer Receive Payments retrieved successfully');
    }

    /**
     * @param CreateCustomerReceivePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerReceivePayments",
     *      summary="Store a newly created CustomerReceivePayment in storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Store CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePayment")
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
     *                  ref="#/definitions/CustomerReceivePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerReceivePaymentAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'documentType', 'companyFinanceYearID', 'custTransactionCurrencyID', 'customerID'));
        $company = Company::select('CompanyID')->where('companySystemID', $input['companySystemID'])->first();
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        $serialNo = CustomerReceivePayment::select(DB::raw('IFNULL(MAX(serialNo),0)+1 as serialNo'))->where('documentSystemID', 21)->where('companySystemID', $input['companySystemID'])->orderBy('serialNo', 'desc')->first();
        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $custPaymentReceiveCode = ($company->CompanyID . '\\' . $y . '\\BRV' . str_pad($serialNo['serialNo'], 6, '0', STR_PAD_LEFT));

/*POST
companySystemID
companyFinanceYearID
companyFinancePeriodID
narration
customerID
documentType
custTransactionCurrencyID
*/

        $input['companyID'] = $company->CompanyID;
        $input['documentSystemID'] = 21;
        $input['documentID'] = 'BRV';
        $input['serialNo'] = $serialNo->serialNo;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        $input['custPaymentReceiveCode'] = $custPaymentReceiveCode;
        $input['custPaymentReceiveDate'] =  Carbon::parse($input['custPaymentReceiveDate'])->format('Y-m-d') . ' 00:00:00';

        /*currency*/
        $myCurr = $input['custTransactionCurrencyID'];

        $companyCurrency = \Helper::companyCurrency($input['custTransactionCurrencyID']);
        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $myCurr, $myCurr, 0);

        $input['custTransactionCurrencyER'] = 1;
        $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
        $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

        $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $company['CompanyID'])->where('isDefault', -1)->first();
        if ($bank) {
            $input['bankID'] = $bank->bankmasterAutoID;
            $bankAccount = BankAccount::where('companyID', $company['CompanyID'])->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $myCurr)->first();
            if($bankAccount){
                $input['bankAccount'] =  $bankAccount->bankAccountAutoID;

                $input['bankCurrency']=$myCurr;
                $input['bankCurrencyER']=1;
            }

        }


        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');


        if ($input['documentType'] == 13) {
            /* Customer Invoice Receipt*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
            $input['customerGLCode'] = $customer->custGLaccount;
        }

        if ($input['documentType'] == 14) {
            $input= array_except($input,'customerID');
            /* Direct Invoice*/
        }

        if (($input['custPaymentReceiveDate'] >= $companyfinanceperiod->dateFrom) && ($input['creditNoteDate'] <= $companyfinanceperiod->dateTo)) {
            $customerReceivePayments = $this->customerReceivePaymentRepository->create($input);
            return $this->sendResponse($customerReceivePayments->toArray(), 'Reciept vocher created successfully');
        } else {
            return $this->sendError('Reciept vocher document date should be between financial period start and end date', 500);
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePayments/{id}",
     *      summary="Display the specified CustomerReceivePayment",
     *      tags={"CustomerReceivePayment"},
     *      description="Get CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
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
     *                  ref="#/definitions/CustomerReceivePayment"
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

        /** @var CustomerReceivePayment $customerReceivePayment */
      //  $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        $customerReceivePayment = $this->customerReceivePaymentRepository->with(['currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }])->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        return $this->sendResponse($customerReceivePayment->toArray(), 'Customer Receive Payment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerReceivePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerReceivePayments/{id}",
     *      summary="Update the specified CustomerReceivePayment in storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Update CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePayment")
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
     *                  ref="#/definitions/CustomerReceivePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerReceivePaymentAPIRequest $request)
    {
         $input = $request->all();



        $input = $this->convertArrayToSelectedValue($input, array('companyFinanceYearID','customerID', 'companyFinancePeriodID', 'custTransactionCurrencyID', 'bankID', 'bankAccount', 'bankCurrency','confirmedYN'));

        $input= array_except($input,['currency','finance_year_by','finance_period_by']);
      $bankcurrencyID=  $input['bankCurrency'];
        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);


        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

         $input['custPaymentReceiveDate'] =  Carbon::parse($input['custPaymentReceiveDate'])->format('Y-m-d') . ' 00:00:00';
         $input['custChequeDate'] =  Carbon::parse($input['custChequeDate'])->format('Y-m-d') . ' 00:00:00';

   /*     if (($input['custPaymentReceiveDate'] >= $input['FYPeriodDateFrom']) && ($input['custPaymentReceiveDate'] <= $input['FYPeriodDateTo'])) {

        } else {
            return $this->sendError('Document Date should be between financial period start date and end date.', 500);

        }*/

        if($input['documentType']==13){
            /*customer reciept*/
            $detail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)->get();

            if($input['customerID'] !=$customerReceivePayment->customerID){
                /*
                 * customer change
                 *
                 * */

                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the customer.', 500);
                }
                $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();


                /*if customer change*/
                $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
                $input['customerGLCode'] = $customer->custGLaccount;
                $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
                $currency = customercurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
                if ($currency) {
                    $input['custTransactionCurrencyID'] = $currency->currencyID;
                    $myCurr = $currency->currencyID;

                    $companyCurrency = \Helper::companyCurrency($currency->currencyID);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrencyER']=0;
                    $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $customerReceivePayment->companyID)->where('isDefault', -1)->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                        $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $myCurr)->first();
                        if($bankAccount){
                            $input['bankAccount'] =  $bankAccount->bankAccountAutoID;
                            $input['bankCurrency']=$myCurr;
                            $input['bankCurrencyER']=1;
                        }


                    }
                }
                /*
                 *
                 *
                 * */
            }



            if($input['bankAccount'] != $customerReceivePayment->bankAccount){

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMaster) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                    }
                }
            }

            if ($input['custTransactionCurrencyID'] != $customerReceivePayment->custTransactionCurrencyID) {
                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the currency.', 500);
                } else {
                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrency'] = null;
                    $input['bankCurrencyER']=0;


                    $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $customerReceivePayment->companyID)->where('isDefault', -1)->first();
                    $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $myCurr)->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                    }
                    if($bankAccount){
                        $input['bankAccount'] =  $bankAccount->bankAccountAutoID;

                        $input['bankCurrency']=$myCurr;
                        $input['bankCurrencyER']=1;
                    }

                }
            }

            if($input['bankID'] !=$customerReceivePayment->bankID){
                $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $input['bankID'])->where('isDefault', 1)->where('accountCurrencyID', $input['custTransactionCurrencyID'])->first();
                $input['bankAccount'] = null;
                $input['bankCurrencyER']=0;
                $input['bankCurrency'] = null;
                if($bankAccount){
                    $input['bankAccount'] =  $bankAccount->bankAccountAutoID;
                    $input['bankCurrencyER']=1;
                    $input['bankCurrency'] = $input['custTransactionCurrencyID'];
                }
            }



        }

        if($input['documentType']==14){
            /*direct receipt*/
            $detail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

            if($input['bankID'] !=$customerReceivePayment->bankID){
                $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $input['bankID'])->where('isDefault', 1)->first();


                $input['custTransactionCurrencyER'] = 0;
                $input['companyRptCurrencyID'] = 0;
                $input['companyRptCurrencyER'] = 0;
                $input['localCurrencyID'] = 0;
                $input['localCurrencyER'] = 0;

                if($bankAccount){
                    $input['bankAccount'] =  $bankAccount->bankAccountAutoID;
                    $input['bankCurrencyER']=1;
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyID'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyER'] = 1;

                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
            }

            if($input['bankAccount'] != $customerReceivePayment->bankAccount){

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrencyER']=1;
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyID'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyER'] = 1;

                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
            }
        }






        $customerReceivePayment = $this->customerReceivePaymentRepository->update($input, $id);

        return $this->sendResponse($customerReceivePayment->toArray(), 'CustomerReceivePayment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerReceivePayments/{id}",
     *      summary="Remove the specified CustomerReceivePayment from storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Delete CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
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
        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $customerReceivePayment->delete();

        return $this->sendResponse($id, 'Customer Receive Payment deleted successfully');
    }

    public function getRecieptVoucherFormData(Request $request)
    {


        $input = $request->all();
        /*companySystemID*/
        $companySystemID = $input['companyId'];
        $type = $input['type']; /*value ['filter','create','getCurrency']*/

        switch ($type) {
            case 'filter':
                $output['yesNoSelectionForMinus'] = YesNoSelectionForMinus::all();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['month'] = Months::all();
                $output['years'] = CustomerReceivePayment::select(DB::raw("YEAR(custPaymentReceiveDate) as year"))
                    ->whereNotNull('custPaymentReceiveDate')
                    ->where('companySystemID', $companySystemID)
                    ->groupby('year')
                    ->orderby('year', 'desc')
                    ->get();
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'), array('value' => 14, 'label' => 'Direct Receipt'));
                break;

            case 'create':
                $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companySystemID)->where('isAssigned', '-1')->where('isActive', '1')->get();
                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'), array('value' => 14, 'label' => 'Direct Receipt'));
                break;
            case 'getCurrency':
                $customerID = $input['customerID'];
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                break;

            case 'edit':
                $id = $input['id'];
                $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = [];
                }
                $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companySystemID)->where('isAssigned', '-1')->where('isActive', '1')->get();
                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->get();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['docType']=$master->documentType;
                $output['bankDropdown'] = BankAssign::where('isActive', 1)->where('isAssigned', -1)->where('companyID', $output['company']['CompanyID'])->get();

                $output['bankAccount'] = [];
                $output['bankCurrencies'] = [];
                if ($master->bankID != '') {
                    $output['bankAccount'] = BankAccount::where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $master->bankID)->where('isAccountActive', 1)->get();
                }
                    if ($master->bankAccount != '') {
                    $output['bankCurrencies']=DB::table('erp_bankaccount')->join('currencymaster', 'accountCurrencyID', '=', 'currencymaster.currencyID')->where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $master->bankID)->where('bankAccountAutoID',$master->bankAccount)->where('isAccountActive', 1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode')->get();
                }


break;
            default:
                $output = [];
        }
        return $this->sendResponse($output, 'Form data');

    }

    public function recieptVoucherDataTable(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'documentType', 'trsClearedYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $master = DB::table('erp_customerreceivepayment')
            ->leftjoin('currencymaster as transCurr', 'custTransactionCurrencyID', '=', 'transCurr.currencyID')
            ->leftjoin('currencymaster as bankCurr', 'bankCurrency', '=', 'bankCurr.currencyID')
            ->leftjoin('employees', 'erp_customerreceivepayment.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_customerreceivepayment.customerID')
            ->where('erp_customerreceivepayment.companySystemID', $input['companyId'])
            ->where('erp_customerreceivepayment.documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $master->where('erp_customerreceivepayment.confirmedYN', $input['confirmedYN']);
            }
        }
        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $master->where('erp_customerreceivepayment.approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $master->whereMonth('custPaymentReceiveDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $master->whereYear('custPaymentReceiveDate', '=', $input['year']);
            }
        }
        if (array_key_exists('documentType', $input)) {
            if ($input['documentType'] && !is_null($input['documentType'])) {
                $master->where('documentType', '=', $input['documentType']);
            }
        }
        if (array_key_exists('trsClearedYN', $input)) {
            if ($input['trsClearedYN'] && !is_null($input['trsClearedYN'])) {
                $master->where('trsClearedYN', '=', $input['trsClearedYN']);
            }
        }


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $master = $master->where(function ($query) use ($search) {
                $query->Where('custPaymentReceiveCode', 'LIKE', "%{$search}%")
                    ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                    ->orWhere('erp_customerreceivepayment.narration', 'LIKE', "%{$search}%");
            });
        }
        $request->request->remove('search.value');
        $master->select('custPaymentReceiveCode', 'transCurr.CurrencyCode as transCurrencyCode', 'bankCurr.CurrencyCode as bankCurrencyCode', 'erp_customerreceivepayment.approvedDate', 'custPaymentReceiveDate', 'erp_customerreceivepayment.narration', 'empName', 'transCurr.DecimalPlaces as transDecimal', 'bankCurr.DecimalPlaces as bankDecimal', 'erp_customerreceivepayment.confirmedYN', 'erp_customerreceivepayment.approved', 'custReceivePaymentAutoID', 'customermaster.CustomerName', 'receivedAmount', 'bankAmount');

        return \DataTables::of($master)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('custReceivePaymentAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function recieptDetailsRecords(){

    }
}
