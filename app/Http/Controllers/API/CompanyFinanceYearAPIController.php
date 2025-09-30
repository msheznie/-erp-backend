<?php
/**
 * =============================================
 * -- File Name : CompanyFinanceYearAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Year
 * -- Author : Mohamed Nazir
 * -- Create date : 12-June 2018
 * -- Description : This file contains the all CRUD for Company Finance Year
 * -- REVISION HISTORY
 * -- Date: 27-December 2018 By: Fayas Description: Added new functions named as getFinancialYearsByCompany()
 */
namespace App\Http\Controllers\API;

use App\helper\SupplierInvoice;
use App\Http\Requests\API\CreateCompanyFinanceYearAPIRequest;
use App\Http\Requests\API\UpdateCompanyFinanceYearAPIRequest;
use App\Jobs\CreateFinancePeriod;
use App\Models\AssetCapitalization;
use App\Models\AssetDisposalMaster;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\CreditNote;
use App\Models\CustomerInvoice;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\DeliveryOrder;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnMaster;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PurchaseReturn;
use App\Models\SalesReturn;
use App\Models\StockAdjustment;
use App\Models\StockCount;
use App\Models\StockReceive;
use App\Models\StockTransfer;
use App\Models\Year;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DepartmentMaster;
use App\Repositories\CompanyFinancePeriodRepository;
use App\Repositories\CompanyFinanceYearRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyFinanceYearController
 * @package App\Http\Controllers\API
 */
class CompanyFinanceYearAPIController extends AppBaseController
{
    /** @var  CompanyFinanceYearRepository */
    private $companyFinanceYearRepository;
    private $companyFinancePeriodRepository;

    public function __construct(CompanyFinanceYearRepository $companyFinanceYearRepo,CompanyFinancePeriodRepository $companyFinancePeriodRepo)
    {
        $this->companyFinanceYearRepository = $companyFinanceYearRepo;
        $this->companyFinancePeriodRepository = $companyFinancePeriodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinanceYears",
     *      summary="Get a listing of the CompanyFinanceYears.",
     *      tags={"CompanyFinanceYear"},
     *      description="Get all CompanyFinanceYears",
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
     *                  @SWG\Items(ref="#/definitions/CompanyFinanceYear")
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
        $this->companyFinanceYearRepository->pushCriteria(new RequestCriteria($request));
        $this->companyFinanceYearRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyFinanceYears = $this->companyFinanceYearRepository->all();

        return $this->sendResponse($companyFinanceYears->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_finance_years')]));
    }

    /**
     * @param CreateCompanyFinanceYearAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyFinanceYears",
     *      summary="Store a newly created CompanyFinanceYear in storage",
     *      tags={"CompanyFinanceYear"},
     *      description="Store CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinanceYear that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinanceYear")
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
     *                  ref="#/definitions/CompanyFinanceYear"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyFinanceYearAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'bigginingDate' => 'required',
            'endingDate' => 'required|after:bigginingDate'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]));
        }

        $input['companyID'] = $company->CompanyID;
        $fromDate  = new Carbon($request->bigginingDate);
        $input['bigginingDate'] = $fromDate->format('Y-m-d');
        $toDate = new Carbon($request->endingDate);
        $input['endingDate'] = $toDate->format('Y-m-d');

        $diffMonth = (Carbon::createFromFormat('Y-m-d',$input['bigginingDate']))->diffInMonths(Carbon::createFromFormat('Y-m-d',$input['endingDate']));

        if($diffMonth != 11){
            return  $this->sendError(trans('custom.financial_year_must_contain_12_months'));
        }

        $CheckBeginDate = CompanyFinanceYear::where('companySystemID',$input['companySystemID'])->where('bigginingDate', ">=", $input['bigginingDate'])->where('bigginingDate', "<=", $input['endingDate'])->first();

        $CheckEndDate = CompanyFinanceYear::where('companySystemID',$input['companySystemID'])->where('endingDate', ">=", $input['bigginingDate'])->where('endingDate', "<=", $input['endingDate'])->first();

        if($CheckBeginDate || $CheckEndDate){
            return  $this->sendError(trans('custom.already_finance_year_has_been_created_for_this_date_range'));
        }

        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $year = $fromDate->format('Y');

        $checkYear = Year::where('yearID', $year)->first();
        if (!$checkYear) {
            $yearData = [
                            'yearID' => $year,
                            'year' => $year
                        ];

            Year::insert($yearData);

        }

        $input['endingDate'] = $input['endingDate']. " 23:59:59";

        $companyFinanceYears = $this->companyFinanceYearRepository->create($input);
        CreateFinancePeriod::dispatch($companyFinanceYears);

        return $this->sendResponse($companyFinanceYears->toArray(), trans('custom.save', ['attribute' => trans('custom.company_finance_years')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinanceYears/{id}",
     *      summary="Display the specified CompanyFinanceYear",
     *      tags={"CompanyFinanceYear"},
     *      description="Get CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYear",
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
     *                  ref="#/definitions/CompanyFinanceYear"
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
        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_finance_years')]));
        }

        return $this->sendResponse($companyFinanceYear->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_finance_years')]));
    }

    /**
     * @param int $id
     * @param UpdateCompanyFinanceYearAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyFinanceYears/{id}",
     *      summary="Update the specified CompanyFinanceYear in storage",
     *      tags={"CompanyFinanceYear"},
     *      description="Update CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYear",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinanceYear that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinanceYear")
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
     *                  ref="#/definitions/CompanyFinanceYear"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyFinanceYearAPIRequest $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_finance_years')]));
        }

        $checkFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companyFinanceYear->companySystemID)
                                                    ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                                                    ->where('isActive', -1)
                                                    ->count();

        if ($input['isActive']) {
            $input['isActive'] = -1;
        } else if ($companyFinanceYear->isActive && !$input['isActive'] && $checkFinancePeriod > 0) {
            return $this->sendError(trans('custom.cannot_deactivate_there_are_some_active_finance_periods_for_this_finance_year'));
        }

        if ($input['isCurrent']) {
            $input['isCurrent'] = -1;
            if(!$companyFinanceYear->isCurrent){
                $checkCurrentFinanceYear = CompanyFinanceYear::where('companySystemID', $companyFinanceYear->companySystemID)
                    ->where('isCurrent', -1)
                    ->count();

                if ($checkCurrentFinanceYear > 0) {
                    return $this->sendError(trans('custom.company_already_has_a_current_financial_year'));
                }
            }
        }

        if ($input['isClosed']) {
            $input['isClosed']  = -1;

            if(!$companyFinanceYear->isClosed && $checkFinancePeriod > 0 && $input['closeAllPeriods'] == 0){
                return $this->sendError(trans('custom.cannot_close_there_are_some_open_financial_periods_for_the_selected_financial_year_do_you_want_to_close_all_the_financial_periods'),500,array('type' => 'active_period_exist' ));
            }

            //if($input['closeAllPeriods'] == 1){
                $updateFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companyFinanceYear->companySystemID)
                                                            ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                                                            ->get();

                foreach ($updateFinancePeriod as $period){
                    $this->companyFinancePeriodRepository->update(['isActive' => 0,'isCurrent' => 0,'isClosed' => -1],$period->companyFinancePeriodID);
                }
            //}

            $input['isCurrent'] = 0;
            $input['isActive']  = 0;

            $input['closedByEmpSystemID'] = $employee->employeeSystemID;
            $input['closedByEmpID']       = $employee->empID;
            $input['closedByEmpName']     = $employee->empName;
            $input['closedDate']          = now();
        }else if($companyFinanceYear->isClosed == -1 && $input['isClosed'] == 0){
            return $this->sendError(trans('custom.cannot_open_this_finance_year'));
        }


        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        if($input['created_employee']){
            unset($input['created_employee']);
        }

        if($input['modified_employee']) {
            unset($input['modified_employee']);
        }

        $companyFinanceYear = $this->companyFinanceYearRepository->update($input, $id);

        return $this->sendResponse($companyFinanceYear->toArray(), trans('custom.update', ['attribute' => trans('custom.company_finance_years')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyFinanceYears/{id}",
     *      summary="Remove the specified CompanyFinanceYear from storage",
     *      tags={"CompanyFinanceYear"},
     *      description="Delete CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYear",
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
        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);
        $employee = \Helper::getEmployeeInfo();

        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_finance_years')]));
        }

        $grv = GRVMaster::where('companyFinanceYearID', $id)->first();
        $itemIssue = ItemIssueMaster::where('companyFinanceYearID', $id)->first();
        $itemReturn = ItemReturnMaster::where('companyFinanceYearID', $id)->first();
        $stockTransfer = StockTransfer::where('companyFinanceYearID', $id)->first();
        $stockReceive = StockReceive::where('companyFinanceYearID', $id)->first();
        $stockAdjustment = StockAdjustment::where('companyFinanceYearID', $id)->first();
        $purchaseReturn = PurchaseReturn::where('companyFinanceYearID', $id)->first();
        $stockCount = StockCount::where('companyFinanceYearID', $id)->first();
        $inventoryClassification = InventoryReclassification::where('companyFinanceYearID', $id)->first();
        $supplierInvoice = BookInvSuppMaster::where('companyFinanceYearID', $id)->first();
        $debitNote = DebitNote::where('companyFinanceYearID', $id)->first();
        $paymentVoucher = PaySupplierInvoiceMaster::where('companyFinanceYearID', $id)->first();
        $customerInvoice = CustomerInvoice::where('companyFinanceYearID', $id)->first();
        $creditNote = CreditNote::where('companyFinanceYearID', $id)->first();
        $receiptVoucher = CustomerReceivePayment::where('companyFinanceYearID', $id)->first();
        $deliveryOrder = DeliveryOrder::where('companyFinanceYearID', $id)->first();
        $salesReturn = SalesReturn::where('companyFinanceYearID', $id)->first();
        $journal = JvMaster::where('companyFinanceYearID', $id)->first();
        $assetDisposal = AssetDisposalMaster::where('companyFinanceYearID', $id)->first();
        $assetCapitalization = AssetCapitalization::where('companyFinanceYearID', $id)->first();

        if (!empty($grv) || !empty($itemIssue) || !empty($itemReturn) || !empty($stockTransfer) || !empty($stockReceive) || !empty($stockAdjustment) || !empty($purchaseReturn) || !empty($stockCount) || !empty($inventoryClassification) || !empty($supplierInvoice) || !empty($debitNote) || !empty($paymentVoucher) || !empty($customerInvoice) || !empty($creditNote) || !empty($receiptVoucher) || !empty($deliveryOrder) || !empty($salesReturn) || !empty($journal) || !empty($assetDisposal) || !empty($assetCapitalization)) {
            return $this->sendError(trans('custom.finance_year_cannot_be_deleted_as_transactions_are'));
        }

        $companyFinanceYear->update(['isActive' => 0,'isCurrent' => 0,'isClosed' => 0, 'deleted_at'=>date("Y-m-d H:i:s"), 'isDeleted'=>1,'deletedBy'=>$employee->empName]);

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.company_finance_years')]));
    }

    public function getFinancialYearsByCompany(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $companyFinancialYears = CompanyFinanceYear::with(['created_employee','modified_employee'])->where('isDeleted',0)->whereIn('companySystemID', $subCompanies);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $companyFinancialYears = $companyFinancialYears->where(function ($query) use ($search) {
                /*$query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('issueRefNo', 'LIKE', "%{$search}%");*/
            });
        }

        return \DataTables::eloquent($companyFinancialYears)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('companyFinanceYearID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('closeAllPeriods', function ($row) {
                return 0;
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFinanceYearFormData(Request $request){

        $input = $request->all();
        $departments = DepartmentMaster::select('departmentSystemID','DepartmentDescription','DepartmentID');


        if (array_key_exists('isFinancialYearYN', $input)) {
            if (!is_null($input['isFinancialYearYN'])) {
                $departments->where('isFinancialYearYN', $input['isFinancialYearYN']);
            }
        }

        $departments = $departments->get();

        $output = array(
            'departments' => $departments
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

}
