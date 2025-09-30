<?php
/**
 * =============================================
 * -- File Name : CurrencyMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Currency Master
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Currency Master
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getAllCurrencies(),getCurrenciesBySupplier(),
 * addCurrencyToSupplier(),updateCurrencyToSupplier()
 * -- Date: 02-October 2018 By: Nazir Description: Added new functions named as getCompanyLocalCurrency()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCurrencyMasterAPIRequest;
use App\Http\Requests\API\UpdateCurrencyMasterAPIRequest;
use App\Models\BankMemoSupplier;
use App\Models\BankMemoSupplierMaster;
use App\Models\BankMemoTypes;
use App\Models\CurrencyMaster;
use App\Models\Company;
use App\Repositories\CurrencyConversionRepository;
use App\Repositories\CurrencyMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\SupplierMaster;
use App\Models\SupplierCurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;

/**
 * Class CurrencyMasterController
 * @package App\Http\Controllers\API
 */
class CurrencyMasterAPIController extends AppBaseController
{
    /** @var  CurrencyMasterRepository */
    private $currencyMasterRepository;
    private $userRepository;
    private $currencyConversionRepository;

    public function __construct(CurrencyMasterRepository $currencyMasterRepo, UserRepository $userRepo,
                                CurrencyConversionRepository $currencyConversionRepo)
    {
        $this->currencyMasterRepository = $currencyMasterRepo;
        $this->currencyConversionRepository = $currencyConversionRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the CurrencyMaster.
     * GET|HEAD /currencyMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->currencyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyMasters = $this->currencyMasterRepository->all();

        return $this->sendResponse($currencyMasters->toArray(), trans('custom.currency_masters_retrieved_successfully'));
    }

    /**
     * Display a listing of the CurrencyMaster.
     * GET|HEAD /getAllCurrencies
     *
     * @param Request $request
     * @return Response
     */

    public function getAllCurrencies(Request $request)
    {

        $this->currencyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyMasterRepository = $this->currencyMasterRepository->all();

        return $this->sendResponse($currencyMasterRepository->toArray(), trans('custom.country_masters_retrieved_successfully'));
    }

    /**
     * Display a listing of assigned Currency for supplier
     * GET|HEAD /getCurrenciesBySupplier
     *
     * @param Request $request
     * @return Response
     */
    public function getCurrenciesBySupplier(Request $request)
    {

        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem', '=', $supplierId)->first();

        if ($supplier) {
            $supplierCurrencies = DB::table('suppliercurrency')
                ->leftJoin('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                ->where('supplierCodeSystem', '=', $supplierId);
            if (isset($request['isAssigned'])) {
                $supplierCurrencies = $supplierCurrencies->where('isAssigned', '=', $request['isAssigned']);
            }

            $supplierCurrencies = $supplierCurrencies->orderBy('supplierCurrencyID', 'DESC')->get();

        } else {
            $supplierCurrencies = [];
        }

        return $this->sendResponse($supplierCurrencies, trans('custom.supplier_currencies_retrieved_successfully'));
    }

    public function getAllConversionByCurrency(Request $request)
    {

        $id = isset($request['id']) ? $request['id'] : 0;
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);
        $reportingId = 0;
        if (empty($currencyMaster)) {
            return $this->sendError(trans('custom.currency_master_not_found'));
        }
        $employee = Helper::getEmployeeInfo();
        if(!empty($employee)){
            $company = Helper::companyCurrency($employee->empCompanySystemID);
            if(!empty($company)){
                $reportingId =  $company->reportingCurrency;
            }
        }

        $conversions = $this->currencyConversionRepository->with(['sub_currency'])->findWhere(['masterCurrencyID' => $id]);
        $array = array(
            'reportingCurrency' => $reportingId,
            'conversions' => $conversions->toArray()
        );
        return $this->sendResponse($array, trans('custom.currency_conversions_retrieved_successfully'));
    }

    /**
     * Store a newly created CurrencyMaster in storage.
     * POST /addCurrencyToSupplier
     *
     * @param Request $request
     *
     * @return Response
     */

    public function addCurrencyToSupplier(Request $request)
    {

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        $input = $this->convertArrayToValue($request);

        if($input['currencyId'] == null){
            return $this->sendError('Currency not selected',500);
        }
        $supplierCurrencyCheck = SupplierCurrency::where('supplierCodeSystem',$request['supplierId'])
                                            ->where('currencyID',$request['currencyId'])
                                            ->first();
        if($supplierCurrencyCheck){
            return $this->sendError(trans('custom.selected_currency_is_assigned_already'),500);
        }

        $supplierCurrency = new SupplierCurrency();
        $supplierCurrency->supplierCodeSystem = $request['supplierId'];
        $supplierCurrency->currencyID = $request['currencyId'];
        $supplierCurrency->isAssigned = -1;
        $supplierCurrency->isDefault = 0;
        $supplierCurrency->save();

        $supplier = SupplierMaster::where('supplierCodeSystem', $request['supplierId'])->first();

        $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();

        foreach ($companyDefaultBankMemos as $value) {
            $temBankMemo = new BankMemoSupplier();
            $temBankMemo->memoHeader = $value['bankMemoHeader'];
            $temBankMemo->bankMemoTypeID = $value['bankMemoTypeID'];
            $temBankMemo->memoDetail = '';
            $temBankMemo->supplierCodeSystem = $supplier->supplierCodeSystem;
            $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
            $temBankMemo->updatedByUserID = $empId;
            $temBankMemo->updatedByUserName = $empName;
            $temBankMemo->save();
        }

        return $this->sendResponse($supplierCurrency, trans('custom.supplier_currencies_added_successfully'));
    }

    /**
     * Update Supplier currency assign.
     * Post /updateCurrencyToSupplier
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateCurrencyToSupplier(Request $request)
    {

        $supplierCurrency = SupplierCurrency::where('supplierCurrencyID', $request['supplierCurrencyID'])->first();

        if ($supplierCurrency) {
            if ($request['isDefault'] == true) {

                if ($request['isDefault'] == -1 && $request['isAssigned'] == false) {
                    return $this->sendError(trans('custom.cannot_updateat_least_one_currency_should_be_defau'), 500);
                }
                $supplierCurrencies = SupplierCurrency::where('supplierCodeSystem', $request['supplierCodeSystem'])->get();
                foreach ($supplierCurrencies as $sc) {
                    $tem_sc = SupplierCurrency::where('supplierCurrencyID', $sc['supplierCurrencyID'])->first();
                    $tem_sc->isDefault = 0;
                    $tem_sc->save();
                }
            } else {
                $isSupplierCurrency= SupplierCurrency::where('supplierCodeSystem', $request['supplierCodeSystem'])->where('currencyID', $request['currencyID'])->first();

                if ($request['isDefault'] == false && $isSupplierCurrency->isDefault == -1) {
                    return $this->sendError(trans('custom.cannot_updateat_least_one_currency_should_be_defau'), 500);
                }

            }

            if ($request['isDefault'] == true || $request['isDefault'] == 1) {
                $request['isDefault'] = -1;
            }

            if ($request['isAssigned'] == true || $request['isAssigned'] == 1) {
                $request['isAssigned'] = -1;
            }

            $supplierCurrency->isDefault = $request['isDefault'];
            $supplierCurrency->isAssigned = $request['isAssigned'];
            $supplierCurrency->save();
        }
        return $this->sendResponse($supplierCurrency, trans('custom.supplier_currencies_updated_successfully'));
    }

    /**
     * Store a newly created CurrencyMaster in storage.
     * POST /currencyMasters
     *
     * @param CreateCurrencyMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCurrencyMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'CurrencyName' => 'required',
            'CurrencyCode' => 'required|unique:currencymaster',
            'DecimalPlaces' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = Helper::getEmployeeID();
        $input['ExchangeRate'] = 1;
        $input['isLocal'] = 0;
        $currencyMasters = $this->currencyMasterRepository->create($input);
        $allCurrency     = CurrencyMaster::all();

        DB::beginTransaction();
        try {
        foreach ($allCurrency as $currency) {
            $conversion = 0;
            $subConversion = 0;
            if ($currencyMasters->currencyID == $currency->currencyID) {
                $conversion = 1;
            }

            $temData = array(
                'masterCurrencyID' => $currencyMasters->currencyID,
                'subCurrencyID' => $currency->currencyID,
                'conversion' => $conversion
            );

            if($conversion != 0){
                $subConversion = round((1/$conversion),8);
            }

            $this->currencyConversionRepository->create($temData);

            if ($currencyMasters->currencyID != $currency->currencyID) {
                $temData1 = array(
                    'masterCurrencyID' => $currency->currencyID,
                    'subCurrencyID' => $currencyMasters->currencyID,
                    'conversion' => $subConversion
                );
                $this->currencyConversionRepository->create($temData1);
            }
        }
        DB::commit();
        return $this->sendResponse($currencyMasters->toArray(), trans('custom.currency_master_saved_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified CurrencyMaster.
     * GET|HEAD /currencyMasters/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CurrencyMaster $currencyMaster */
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);

        if (empty($currencyMaster)) {
            return $this->sendError(trans('custom.currency_master_not_found'));
        }

        return $this->sendResponse($currencyMaster->toArray(), trans('custom.currency_master_retrieved_successfully'));
    }

    /**
     * Update the specified CurrencyMaster in storage.
     * PUT/PATCH /currencyMasters/{id}
     *
     * @param int $id
     * @param UpdateCurrencyMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCurrencyMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyMaster $currencyMaster */
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);

        if (empty($currencyMaster)) {
            return $this->sendError(trans('custom.currency_master_not_found'));
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'CurrencyName' => 'required',
            'CurrencyCode' => ['required', Rule::unique('currencymaster')->ignore($id, 'currencyID')],
            'DecimalPlaces' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = Helper::getEmployeeID();
        $input['DateModified'] = Helper::getEmployeeID();
        $input['DateModified'] = now();

        $currencyMaster = $this->currencyMasterRepository->update($input, $id);

        return $this->sendResponse($currencyMaster->toArray(), trans('custom.currency_master_updated_successfully'));
    }

    /**
     * Remove the specified CurrencyMaster from storage.
     * DELETE /currencyMasters/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CurrencyMaster $currencyMaster */
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);

        if (empty($currencyMaster)) {
            return $this->sendError(trans('custom.currency_master_not_found'));
        }

        $currencyMaster->delete();

        $this->currencyConversionRepository->deleteWhere(['masterCurrencyID' => $id]);
        $this->currencyConversionRepository->deleteWhere(['subCurrencyID' => $id]);

        return $this->sendResponse($id, trans('custom.currency_master_deleted_successfully'));
    }

    public function getCompanyLocalCurrency(Request $request)
    {
        $input = $request->all();
        $localCurrency = 0;

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_master_not_found'));
        }

        if (!empty($company->localCurrencyID)) {
            $localCurrency = $company->localCurrencyID;
        } else {
            return $this->sendError(trans('custom.company_local_currency_not_found'));
        }

        return $this->sendResponse($localCurrency, trans('custom.data_retrieved_successfully'));

    }

    public function getCompanyReportingCurrency(Request $request)
    {
        $input = $request->all();
        $reportingCurrency = 0;

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_master_not_found'));
        }

        if (!empty($company->reportingCurrency)) {
            $reportingCurrency = $company->reportingCurrency;
        } else {
            return $this->sendError(trans('custom.company_reporting_currency_not_found'));
        }

        return $this->sendResponse($reportingCurrency, trans('custom.data_retrieved_successfully'));

    }

    public function getCompanyReportingCurrencyCode(Request $request)
    {
        $input = $request->all();
        $reportingCurrency = 0;

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_master_not_found'));
        }

        if (!empty($company->reportingCurrency)) {
            $reportingCurrency = $company->reportingCurrency;
            $reportingCurrencyCode = CurrencyMaster::find($reportingCurrency);
        } else {
            return $this->sendError(trans('custom.company_reporting_currency_not_found'));
        }

        return $this->sendResponse($reportingCurrencyCode, trans('custom.data_retrieved_successfully'));

    }

    public function getCompanyLocalCurrencyCode(Request $request)
    {
        $input = $request->all();
        $localCurrency = 0;

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_master_not_found'));
        }

        if (!empty($company->localCurrencyID)) {
            $localCurrency = $company->localCurrencyID;
            $localCurrencyCode = CurrencyMaster::find($localCurrency);
        } else {
            return $this->sendError(trans('custom.company_local_currency_not_found'));
        }

        return $this->sendResponse($localCurrencyCode, trans('custom.data_retrieved_successfully'));

    }

    public function getCompanyCurrency(Request $request)
    {
        $input = $request->all();

        $currencyIds = Company::where('companySystemID', $input['companyId'])
            ->get(['reportingCurrency', 'localCurrencyID'])
            ->pluck('reportingCurrency')
            ->merge(Company::where('companySystemID', $input['companyId'])->pluck('localCurrencyID'))
            ->unique();

        $companyCurrencies = CurrencyMaster::select('currencyID',
            DB::raw("CONCAT(CurrencyCode, ' | ', CurrencyName) as CurrencyName"))
            ->whereIn('currencyID', $currencyIds)
            ->get();

        return $this->sendResponse($companyCurrencies, trans('custom.data_retrieved_successfully'));
    }
}
