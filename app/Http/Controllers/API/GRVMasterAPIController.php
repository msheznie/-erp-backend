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
 * -- Date: 11-June 2018 By: Nazir Description: Added new functions named as getGoodReceiptVoucherMasterView() For load Master View
 * -- Date: 11-June 2018 By: Nazir Description: Added new functions named as getGRVFormData() For load Master View
 * -- Date: 16-June 2018 By: Nazir Description: Added new functions named as GRVSegmentChkActive() For load Master View
 * -- Date: 19-June 2018 By: Nazir Description: Added new functions named as goodReceiptVoucherAudit() For load Master View
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVMasterAPIRequest;
use App\Http\Requests\API\UpdateGRVMasterAPIRequest;
use App\Models\GRVMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Months;
use App\Models\SupplierAssigned;
use App\Models\CurrencyMaster;
use App\Models\Location;
use App\Models\GRVTypes;
use App\Models\SupplierMaster;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\SupplierCurrency;
use App\Models\WarehouseMaster;
use App\Models\GRVDetails;
use App\Models\CompanyFinanceYear;
use App\Repositories\GRVMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;

/**
 * Class GRVMasterController
 * @package App\Http\Controllers\API
 */
class GRVMasterAPIController extends AppBaseController
{
    /** @var  GRVMasterRepository */
    private $gRVMasterRepository;
    private $userRepository;

    public function __construct(GRVMasterRepository $gRVMasterRepo, UserRepository $userRepo)
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the GRVMaster.
     * GET|HEAD /gRVMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->gRVMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gRVMasters = $this->gRVMasterRepository->all();

        return $this->sendResponse($gRVMasters->toArray(), 'G R V Masters retrieved successfully');
    }

    /**
     * Store a newly created GRVMaster in storage.
     * POST /gRVMasters
     *
     * @param CreateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['grvDate'])) {
            if ($input['grvDate']) {
                $input['grvDate'] = new Carbon($input['grvDate']);
            }
        }

        if (isset($input['stampDate'])) {
            if ($input['stampDate']) {
                $input['stampDate'] = new Carbon($input['stampDate']);
            }
        }

        $documentDate= $input['grvDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate > $monthBegin) && ($documentDate < $monthEnd))
        {
        }
        else
        {
            return $this->sendError('GRV Date not between Financial period !');
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['documentSystemID'] = '3';
        $input['documentID'] = 'GRV';

        $lastSerial = GRVMaster::where('companySystemID', $input['companySystemID'])
            ->orderBy('grvAutoID', 'desc')
            ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->grvSerialNo) + 1;
        }

        $grvType = GRVTypes::where('grvTypeID', $input['grvTypeID'])->first();
        if ($grvType) {
            $input['grvType'] = $grvType->idERP_GrvTpes;
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        //var_dump($companyCurrencyConversion);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $input['grvSerialNo'] = $lastSerialNumber;
        $input['supplierTransactionER'] = 1;

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
        if ($supplier) {
            $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
            $input['supplierName'] = $supplier->supplierName;
            $input['supplierAddress'] = $supplier->address;
            $input['supplierTelephone'] = $supplier->telephone;
            $input['supplierFax'] = $supplier->fax;
            $input['supplierEmail'] = $supplier->supEmail;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if($companyfinanceyear){
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-',$startYear);
            $finYear = $finYearExp[0];
        }else{
            $finYear = date("Y");
        }
        if ($documentMaster) {
            $grvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['grvPrimaryCode'] = $grvCode;
        }

        $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])
            ->where('isDefault', -1)
            ->first();

        if ($supplierCurrency) {

            $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();

            $input['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;

            if ($erCurrency) {
                $input['supplierDefaultER'] = $erCurrency->ExchangeRate;
            }
        }

        // adding supplier grv details
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($supplierAssignedDetail) {
            $input['liabilityAccountSysemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
            $input['liabilityAccount'] = $supplierAssignedDetail->liabilityAccount;
            $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
            $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
        }

        $gRVMasters = $this->gRVMasterRepository->create($input);

        return $this->sendResponse($gRVMasters->toArray(), 'GRV Master saved successfully');
    }

    /**
     * Display the specified GRVMaster.
     * GET|HEAD /gRVMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->with(['created_by', 'confirmed_by', 'segment_by', 'location_by'])->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'Good Receipt Voucher retrieved successfully');
    }

    /**
     * Update the specified GRVMaster in storage.
     * PUT/PATCH /gRVMasters/{id}
     *
     * @param  int $id
     * @param UpdateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = array_except($input, ['created_by', 'confirmed_by', 'location_by', 'segment_by']);
        $input = $this->convertArrayToValue($input);

        if (isset($input['grvDate'])) {
            if ($input['grvDate']) {
                $input['grvDate'] = new Carbon($input['grvDate']);
            }
        }

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['stampDate'])) {
            if ($input['stampDate']) {
                $input['stampDate'] = new Carbon($input['stampDate']);
            }
        }

        $documentDate= $input['grvDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate > $monthBegin) && ($documentDate < $monthEnd))
        {
        }
        else
        {
            return $this->sendError('GRV Date not between Financial period !');
        }

        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        if ($gRVMaster->grvCancelledYN == -1) {
            return $this->sendError('Good Receipt Voucher closed. You cannot edit.', 500);
        }

        $grvType = GRVTypes::where('grvTypeID', $input['grvTypeID'])->first();
        if ($grvType) {
            $input['grvType'] = $grvType->idERP_GrvTpes;
        }

        //checking selected segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        // changing supplier related details when supplier changed
        if ($gRVMaster->supplierID != $input['supplierID']) {
            $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
            if ($supplier) {
                $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
                $input['supplierName'] = $supplier->supplierName;
                $input['supplierAddress'] = $supplier->address;
                $input['supplierTelephone'] = $supplier->telephone;
                $input['supplierFax'] = $supplier->fax;
                $input['supplierEmail'] = $supplier->supEmail;
            }

            $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])
                ->where('isDefault', -1)
                ->first();

            if ($supplierCurrency) {

                $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();

                $input['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;

                if ($erCurrency) {
                    $input['supplierDefaultER'] = $erCurrency->ExchangeRate;
                }
            }

            // adding supplier grv details
            $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if ($supplierAssignedDetail) {
                $input['liabilityAccountSysemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input['liabilityAccount'] = $supplierAssignedDetail->liabilityAccount;
                $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
            }

        }

        //getting total sum of grv detail amount
        $grvMasterSum = GRVDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('grvAutoID', $input['grvAutoID'])
            ->first();

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $grvMasterSum['masterTotalSum']);

        $input['grvTotalComRptCurrency'] = round($currencyConversionMaster['reportingAmount'], 8);
        $input['grvTotalLocalCurrency'] = round($currencyConversionMaster['localAmount'], 8);
        $input['grvTotalSupplierTransactionCurrency'] = $grvMasterSum['masterTotalSum'];
        $input['localCurrencyER'] = round($currencyConversionMaster['trasToRptER'], 8);
        $input['companyReportingER'] = round($currencyConversionMaster['trasToLocER'], 8);

        // calculating total Supplier Default currency

        $currencyConversionSupplier = \Helper::currencyConversion($input["companySystemID"], $input["supplierDefaultCurrencyID"], $input['supplierTransactionCurrencyID'], $grvMasterSum['masterTotalSum']);

        $input['grvTotalSupplierDefaultCurrency'] = round($currencyConversionSupplier['documentAmount'], 8);

        if ($gRVMaster->grvConfirmedYN == 0 && $input['grvConfirmedYN'] == 1) {

            //getting total sum of PO detail Amount
            $grvMasterSum = GRVDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->first();

            $grvDetailExist = GRVDetails::select(DB::raw('grvDetailsID'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->first();

            if (empty($grvDetailExist)) {
                return $this->sendError('GRV Document cannot confirm without details');
            }

            $checkQuantity = GRVDetails::where('grvAutoID', $id)
                ->where('noQty', '<', 1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty', 500);
            }

            unset($input['grvConfirmedYN']);
            unset($input['grvConfirmedByEmpSystemID']);
            unset($input['grvConfirmedByEmpID']);
            unset($input['grvConfirmedByName']);
            unset($input['grvConfirmedDate']);

            $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => $input["documentSystemID"], 'segment' => $input["serviceLineSystemID"], 'category' => '', 'amount' => $grvMasterSum['masterTotalSum']);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }

        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];
        $input['modifiedUserSystemID'] = $user->employee['employeeSystemID'];

        $gRVMaster = $this->gRVMasterRepository->update($input, $id);

        return $this->sendResponse($gRVMaster->toArray(), 'GRV updated successfully');
    }

    /**
     * Remove the specified GRVMaster from storage.
     * DELETE /gRVMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        $gRVMaster->delete();

        return $this->sendResponse($id, 'Good Receipt Voucher deleted successfully');
    }

    public function getGoodReceiptVoucherMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $grvMaster = GRVMaster::where('companySystemID', $input['companyId']);
        $grvMaster->where('documentSystemID', $input['documentId']);
        $grvMaster->with(['created_by' => function ($query) {
        }, 'segment_by' => function ($query) {
        }, 'location_by' => function ($query) {
        }, 'supplier_by' => function ($query) {
        }, 'currency_by' => function ($query) {
        }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $grvMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('grvLocation', $input)) {
            if ($input['grvLocation'] && !is_null($input['grvLocation'])) {
                $grvMaster->where('grvLocation', $input['grvLocation']);
            }
        }

        if (array_key_exists('grvCancelledYN', $input)) {
            if (($input['grvCancelledYN'] == 0 || $input['grvCancelledYN'] == -1) && !is_null($input['grvCancelledYN'])) {
                $grvMaster->where('grvCancelledYN', $input['grvCancelledYN']);
            }
        }

        if (array_key_exists('grvConfirmedYN', $input)) {
            if (($input['grvConfirmedYN'] == 0 || $input['grvConfirmedYN'] == 1) && !is_null($input['grvConfirmedYN'])) {
                $grvMaster->where('grvConfirmedYN', $input['grvConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $grvMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $grvMaster->whereMonth('grvDate+', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $grvMaster->whereYear('grvDate', '=', $input['year']);
            }
        }

        $grvMaster = $grvMaster->select(
            ['erp_grvmaster.grvAutoID',
                'erp_grvmaster.grvPrimaryCode',
                'erp_grvmaster.documentSystemID',
                'erp_grvmaster.grvDoRefNo',
                'erp_grvmaster.createdDateTime',
                'erp_grvmaster.createdUserSystemID',
                'erp_grvmaster.grvNarration',
                'erp_grvmaster.grvLocation',
                'erp_grvmaster.grvDate',
                'erp_grvmaster.supplierID',
                'erp_grvmaster.serviceLineSystemID',
                'erp_grvmaster.grvConfirmedDate',
                'erp_grvmaster.approvedDate',
                'erp_grvmaster.supplierTransactionCurrencyID',
                'erp_grvmaster.grvTotalSupplierTransactionCurrency',
                'erp_grvmaster.grvCancelledYN',
                'erp_grvmaster.timesReferred',
                'erp_grvmaster.grvConfirmedYN',
                'erp_grvmaster.approved',
                'erp_grvmaster.grvLocation'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMaster = $grvMaster->where(function ($query) use ($search) {
                $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('grvNarration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }


        $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $input['companyId'])->first();

        $policy = 0;

        if (!empty($historyPolicy)) {
            $policy = $historyPolicy->isYesNO;
        }

        return \DataTables::eloquent($grvMaster)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('grvAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getGRVFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $grvAutoID = $request['grvAutoID'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = GRVMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $locations = Location::all();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $grvTypes = GRVTypes::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'locations' => $locations,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'suppliers' => $supplier,
            'grvTypes' => $grvTypes,
            'companyFinanceYear' => $companyFinanceYear
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function GRVSegmentChkActive(Request $request)
    {

        $input = $request->all();

        $grvAutoID = $input['grvAutoID'];

        $grvMaster = GRVMaster::find($grvAutoID);

        if (empty($grvMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $grvMaster->serviceLineSystemID)
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        return $this->sendResponse($grvAutoID, 'sucess');
    }

    public function goodReceiptVoucherAudit(Request $request)
    {
        $id = $request->get('id');

        $gRVMaster = $this->gRVMasterRepository->with(['created_by', 'confirmed_by',
            'cancelled_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 3);
            }])->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'GRV retrieved successfully');
    }
}
