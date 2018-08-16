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
use App\Models\CompanyFinancePeriod;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerMaster;
use App\Models\PerformaDetails;
use App\Models\PerformaMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Months;
use App\Models\Taxdetail;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\chartOfAccount;
use App\Models\FreeBillingMasterPerforma;
use App\Repositories\CustomerInvoiceDirectRepository;
use Carbon\Carbon;
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

        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'companyFinanceYearID'));
        $companyFinanceYearID = $input['companyFinanceYearID'];
        $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();

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
        $input['companySystemID'] = $input['companyID'];
        $input['companyID'] = $company['CompanyID'];
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['documentType'] = 11;
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();


        if (($input['bookingDate'] > $FYPeriodDateFrom) && ($input['bookingDate'] < $FYPeriodDateTo)) {
            $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
            return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice Direct saved successfully');
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
        }, 'bankaccount', 'currency'])->findWithoutFail($id);

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

        dd($input);
        exit;

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirect->toArray(), 'CustomerInvoiceDirect updated successfully');
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
        $input = $this->convertArrayToSelectedValue($input, array('invConfirmedYN', 'month', 'approved', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $invMaster = CustomerInvoiceDirect::where('companySystemID', $input['companyId']);
        $invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with(['currency', 'createduser', 'customer']);


        $invMaster->where('isPerforma', 1);

        if (array_key_exists('invConfirmedYN', $input)) {
            if (($input['invConfirmedYN'] == 0 || $input['invConfirmedYN'] == 1) && !is_null($input['invConfirmedYN'])) {
                $invMaster->where('confirmedYN', $input['invConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }


        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invoiceDate = $input['year'] . '-12-01';
                if (array_key_exists('month', $input)) {
                    if ($input['month'] && !is_null($input['month'])) {
                        $invoiceDate = $input['year'] . '-' . $input['month'] . '-01';
                    }
                }

                $invMaster->where('bookingDate', '<=', $invoiceDate);

            }
        }

        /*    $grvMaster = $invMaster->select(
                [ 'erp_custinvoicedirect.custInvoiceDirectAutoID',
                    'erp_custinvoicedirect.custInvoiceDirectAutoID','bookingDate',
                    'erp_custinvoicedirect.comments',
                    'bookingInvCode',
                    'erp_custinvoicedirect.customerID',
                    'custTransactionCurrencyID',
                    'bookingAmountTrans',
                    'erp_custinvoicedirect.createdUserID',
                    'erp_custinvoicedirect.approvedDate',

                  'erp_custinvoicedirect.confirmedYN','approved','VATAmount'
                ]);*/

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('employees.empName', 'LIKE', "%{$search}%")
                    ->orWhere('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('customermaster.CustomerName', 'LIKE', "%{$search}%");
            });
        }


        /*       $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
                   ->where('companySystemID', $input['companyId'])->first();

               $policy = 0;

               if (!empty($historyPolicy)) {
                   $policy = $historyPolicy->isYesNO;
               }*/

        return \DataTables::eloquent($invMaster)
            /*  ->addColumn('Actions', $policy)*/
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
        $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companyId)->where('isAssigned', '-1')->where('isActive', '1')->get();
        $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
        $output['invoiceType'] = array(array('value' => 1, 'label' => 'Performa Invoice'), array('value' => 0, 'label' => 'Direct Invoice'));
        $output['companyFinanceYear'] = \Helper::companyFinanceYear($companyId);
        $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companyId)->first();
        $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();

        $output['tax'] = \DB::select("SELECT * FROM erp_taxmaster WHERE taxType=2 AND companyID='{$output['company']['CompanyID']}'");

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

        $contract = Contract::select('isRequiredStamp', 'paymentInDaysForJob')->where('CompanyID', $master->companyID)->where('ContractNumber', $performa->contractID)->first();


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
                $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL')->where('AccountCode', $updateInvoice->financeGLcode)->first();

                $companyCurrencyConversion = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $updateInvoice->totAmount);
                /*    trasToLocER,trasToRptER,transToBankER,reportingAmount,localAmount,documentAmount,bankAmount*/
                /*define input*/

                $addToCusInvDetails[$x]['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                $addToCusInvDetails[$x]['companyID'] = $master->companyID;
                $addToCusInvDetails[$x]['serviceLineCode'] = $updateInvoice->serviceLine;
                $addToCusInvDetails[$x]['customerID'] = $updateInvoice->customerID;
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
            $bankdetails['rigNo'] = $performa->ticket->regNo . ' - ' . $performa->ticket->rig->RigDescription;
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
              $details =  CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"),DB::raw("SUM(localAmount) as bookingAmountLocal"),DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID',$custInvoiceDirectAutoID)->first()->toArray();

                CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);



                DB::commit();
                return $this->sendResponse('s', 'successfully created');
            } catch (\Exception $exception) {
                DB::rollback();
                return $this->sendResponse('e', 'Error Occured');
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

            $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCode;
            $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCodeSystemID;
            $vatAmount['VATPercentage'] = $percentage;
            $vatAmount['VATAmount'] = $_post['amount'];
            $vatAmount['VATAmountLocal'] = $_post["localAmount"];
            $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
            DB::commit();
            return $this->sendResponse('s', 'Successfully Added');
        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('e', 'Error Occurred');
        }
    }

}
