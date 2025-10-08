<?php
/**
 * =============================================
 * -- File Name : SalesPersonMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  SalesPersonMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - January 2019
 * -- Description : This file contains the all CRUD for Sales Person Master
 * -- REVISION HISTORY
 * -- Date: 20-January 2019 By: Nazir Description: Added new function getAllCustomerCategories(),
 * -- Date: 21-January 2019 By: Nazir Description: Added new function getSalesPersonFormData(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesPersonMasterAPIRequest;
use App\Http\Requests\API\UpdateSalesPersonMasterAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\QuotationMaster;
use App\Models\SalesPersonMaster;
use App\Models\SalesPersonTarget;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Repositories\SalesPersonMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class SalesPersonMasterController
 * @package App\Http\Controllers\API
 */
class SalesPersonMasterAPIController extends AppBaseController
{
    /** @var  SalesPersonMasterRepository */
    private $salesPersonMasterRepository;

    public function __construct(SalesPersonMasterRepository $salesPersonMasterRepo)
    {
        $this->salesPersonMasterRepository = $salesPersonMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesPersonMasters",
     *      summary="Get a listing of the SalesPersonMasters.",
     *      tags={"SalesPersonMaster"},
     *      description="Get all SalesPersonMasters",
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
     *                  @SWG\Items(ref="#/definitions/SalesPersonMaster")
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
        $this->salesPersonMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->salesPersonMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesPersonMasters = $this->salesPersonMasterRepository->all();

        return $this->sendResponse($salesPersonMasters->toArray(), trans('custom.sales_person_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSalesPersonMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesPersonMasters",
     *      summary="Store a newly created SalesPersonMaster in storage",
     *      tags={"SalesPersonMaster"},
     *      description="Store SalesPersonMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesPersonMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesPersonMaster")
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
     *                  ref="#/definitions/SalesPersonMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalesPersonMasterAPIRequest $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();

        if ((empty($input['empSystemID']) && empty($input['SalesPersonName'])) || ($input['SalesPersonName'] == '')) {
            return $this->sendError(trans('custom.sales_person_name_is_required'));
        }

        $wareHouseData = WarehouseMaster::find($input['wareHouseAutoID']);

        if ($wareHouseData) {
            $input['wareHouseCode'] = $wareHouseData->wareHouseCode;
            $input['wareHouseDescription'] = $wareHouseData->wareHouseDescription;
            $input['wareHouseLocation'] = $wareHouseData->wareHouseLocation;
        }

        $liabilityAccountData = ChartOfAccount::find($input['receivableAutoID']);

        if ($liabilityAccountData) {
            $input['receivableGLAccount'] = $liabilityAccountData->AccountCode;
            $input['receivableDescription'] = $liabilityAccountData->AccountDescription;
            $input['receivableType'] = $liabilityAccountData->controlAccounts;
        }

        $expenseAccountData = ChartOfAccount::find($input['expenseAutoID']);

        if ($expenseAccountData) {
            $input['expenseGLAccount'] = $expenseAccountData->AccountCode;
            $input['expenseDescription'] = $expenseAccountData->AccountDescription;
            $input['expenseType'] = $expenseAccountData->controlAccounts;
        }

        $currencyData = CurrencyMaster::find($input['salesPersonCurrencyID']);

        if ($currencyData) {
            $input['salesPersonCurrency'] = $currencyData->CurrencyCode;
            $input['salesPersonCurrencyDecimalPlaces'] = $currencyData->DecimalPlaces;
        }

        $segmentData = SegmentMaster::find($input['segmentID']);

        if ($segmentData) {
            $input['segmentCode'] = $segmentData->ServiceLineCode;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        // creating document code

        $lastSerial = SalesPersonMaster::where('companySystemID', $input['companySystemID'])
            ->orderBy('salesPersonID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $salesPersonCode = ($company->CompanyID . '\\' . 'REP' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['SalesPersonCode'] = $salesPersonCode;
        $input['serialNumber'] = $lastSerialNumber;

        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;
        $salesPersonMasters = $this->salesPersonMasterRepository->create($input);

        return $this->sendResponse($salesPersonMasters->toArray(), trans('custom.sales_person_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesPersonMasters/{id}",
     *      summary="Display the specified SalesPersonMaster",
     *      tags={"SalesPersonMaster"},
     *      description="Get SalesPersonMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesPersonMaster",
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
     *                  ref="#/definitions/SalesPersonMaster"
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
        /** @var SalesPersonMaster $salesPersonMaster */
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if (empty($salesPersonMaster)) {
            return $this->sendError(trans('custom.sales_person_master_not_found'));
        }

        return $this->sendResponse($salesPersonMaster->toArray(), trans('custom.sales_person_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalesPersonMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesPersonMasters/{id}",
     *      summary="Update the specified SalesPersonMaster in storage",
     *      tags={"SalesPersonMaster"},
     *      description="Update SalesPersonMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesPersonMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesPersonMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesPersonMaster")
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
     *                  ref="#/definitions/SalesPersonMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalesPersonMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        /** @var SalesPersonMaster $salesPersonMaster */
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if ((empty($input['empSystemID']) && empty($input['SalesPersonName'])) || ($input['SalesPersonName'] == '')) {
            return $this->sendError(trans('custom.sales_person_name_is_required'));
        }

        if (empty($salesPersonMaster)) {
            return $this->sendError(trans('custom.sales_person_master_not_found'));
        }

        $wareHouseData = WarehouseMaster::find($input['wareHouseAutoID']);

        if ($wareHouseData) {
            $input['wareHouseCode'] = $wareHouseData->wareHouseCode;
            $input['wareHouseDescription'] = $wareHouseData->wareHouseDescription;
            $input['wareHouseLocation'] = $wareHouseData->wareHouseLocation;
        }

        $liabilityAccountData = ChartOfAccount::find($input['receivableAutoID']);

        if ($liabilityAccountData) {
            $input['receivableGLAccount'] = $liabilityAccountData->AccountCode;
            $input['receivableDescription'] = $liabilityAccountData->AccountDescription;
            $input['receivableType'] = $liabilityAccountData->controlAccounts;
        }

        $expenseAccountData = ChartOfAccount::find($input['expenseAutoID']);

        if ($expenseAccountData) {
            $input['expenseGLAccount'] = $expenseAccountData->AccountCode;
            $input['expenseDescription'] = $expenseAccountData->AccountDescription;
            $input['expenseType'] = $expenseAccountData->controlAccounts;
        }

        $currencyData = CurrencyMaster::find($input['salesPersonCurrencyID']);

        if ($currencyData) {
            $input['salesPersonCurrency'] = $currencyData->CurrencyCode;
            $input['salesPersonCurrencyDecimalPlaces'] = $currencyData->DecimalPlaces;
        }

        $segmentData = SegmentMaster::find($input['segmentID']);

        if ($segmentData) {
            $input['segmentCode'] = $segmentData->ServiceLineCode;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;

        $salesPersonMaster = $this->salesPersonMasterRepository->update($input, $id);

        return $this->sendResponse($salesPersonMaster->toArray(), trans('custom.sales_person_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesPersonMasters/{id}",
     *      summary="Remove the specified SalesPersonMaster from storage",
     *      tags={"SalesPersonMaster"},
     *      description="Delete SalesPersonMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesPersonMaster",
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
        /** @var SalesPersonMaster $salesPersonMaster */
        $salesPersonMaster = $this->salesPersonMasterRepository->findWithoutFail($id);

        if (empty($salesPersonMaster)) {
            return $this->sendError(trans('custom.sales_person_master_not_found'));
        }

        $checkSalesPerson = QuotationMaster::where('companySystemID', $salesPersonMaster->companySystemID)
            ->where('salesPersonID', $id)
            ->first();

        if (!empty($checkSalesPerson)) {
            return $this->sendError(trans('custom.you_cannot_delete_the_sales_person_sales_person_al'));
        }
        // deleting master record
        $salesPersonMaster->delete();

        // deleting detail records
        SalesPersonTarget::where('salesPersonID', $id)->delete();

        return $this->sendResponse($id, trans('custom.sales_person_master_deleted_successfully'));
    }

    public function getAllSalesPersons(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $salesPersonMaster = SalesPersonMaster::whereIn('companySystemID', $childCompanies);

        $search = $request->input('search.value');
        if ($search) {
            $salesPersonMaster = $salesPersonMaster->where(function ($query) use ($search) {
                $query->where('SalesPersonCode', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($salesPersonMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('salesPersonID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getSalesPersonFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }


        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $chartOfAccountBSL = ChartOfAccountsAssigned::select(DB::raw("chartOfAccountSystemID,CONCAT(AccountCode, ' | ' ,AccountDescription) as glName"))
            ->where('controlAccountsSystemID', 4)
            ->where('companySystemID', $companyId)
            ->get();

        $chartOfAccountPLE = ChartOfAccountsAssigned::select(DB::raw("chartOfAccountSystemID,CONCAT(AccountCode, ' | ' ,AccountDescription) as glName"))
            ->where('controlAccountsSystemID', 2)
            ->where('companySystemID', $companyId)
            ->get();

        $segments = SegmentMaster::where("companySystemID", $companyId);
        $segments = $segments->where('isActive', 1);
        $segments = $segments->get();

        $wareHouses = WarehouseMaster::whereIn('companySystemID', $subCompanies);
        $wareHouses = $wareHouses->where('isActive', 1);
        $wareHouses = $wareHouses->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'currencies' => $currencies,
            'segments' => $segments,
            'chartofaccountbsl' => $chartOfAccountBSL,
            'chartofaccountple' => $chartOfAccountPLE,
            'locations' => $wareHouses,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
