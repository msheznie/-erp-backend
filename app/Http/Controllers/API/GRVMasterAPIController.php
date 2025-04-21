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
 * -- Date: 28-June 2018 By: Nazir Description: Added new functions named as getGRVMasterApproval() For load Approval Master View
 * -- Date: 28-June 2018 By: Nazir Description: Added new functions named as getApprovedGRVForCurrentUser() For load Master View
 * -- Date: 28-June 2018 By: Nazir Description: Added new functions named as approveGoodReceiptVoucher() For Approve GRV Master
 * -- Date: 28-June 2018 By: Nazir Description: Added new functions named as rejectGoodReceiptVoucher() For Reject GRV Master
 * -- Date: 17-august 2018 By: Nazir Description: Added new functions named as getGoodReceiptVoucherReopen() For Reopen GRV Master
 * -- Date: 20-September 2018 By: Nazir Description: Added new functions named as getSupplierInvoiceStatusHistoryForGRV()
 * -- Date: 14-November 2018 By: Nazir Description: Added new functions named as getGoodReceiptVoucherAmend()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\ItemTracking;
use App\helper\ReversalDocument;
use App\helper\TaxService;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateGRVMasterAPIRequest;
use App\Http\Requests\API\UpdateGRVMasterAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\PurchaseOrderDetails;
use App\Models\BudgetConsumedData;
use App\Models\SupplierEvaluationTemplate;
use App\Models\Tax;
use App\Models\TaxVatCategories;
use App\Models\ErpItemLedger;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseReturnDetails;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GrvDetailsRefferedback;
use App\Models\GRVMaster;
use App\Models\GrvMasterRefferedback;
use App\Models\GRVTypes;
use App\Models\ItemAssigned;
use App\Models\Location;
use App\Models\Months;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Models\SegmentMaster;
use App\Models\ErpProjectMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\ItemCategoryTypeMaster;
use App\Models\UnbilledGRV;
use App\Models\UnbilledGrvGroupBy;
use App\Models\WarehouseBinLocation;
use App\Models\WarehouseMaster;
use App\Models\ChartOfAccountsAssigned;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\GRVMasterRepository;
use App\Repositories\UserRepository;
use App\Services\ChartOfAccountValidationService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\helper\CancelDocument;
use Response;
use App\Models\Appointment;
use App\Models\AppointmentDetails;
use App\Models\SupplierBlock;
use App\Services\GeneralLedgerService;
use App\Services\ValidateDocumentAmend;

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

        return $this->sendResponse($gRVMasters->toArray(), 'GRV Masters retrieved successfully');
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


        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 10;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);

        $currentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if (isset($input['grvDate'])) {
            if ($input['grvDate']) {
                $input['grvDate'] = new Carbon($input['grvDate']);
                if ($input['grvDate'] > $currentDate) {
                    return $this->sendError('GRV date can not be greater than current date', 500);
                }
            }
        }

        if (isset($input['stampDate'])) {
            if ($input['stampDate']) {
                $input['stampDate'] = new Carbon($input['stampDate']);
            }

            if ($input['stampDate'] > $currentDate) {
                return $this->sendError('Stamp date can not be greater than current date', 500);
            }
        }

        if (!isset($input['grvLocation'])) {
            return $this->sendError('Location not found', 500);
        }

        $warehouse = WarehouseMaster::where("wareHouseSystemCode", $input['grvLocation'])
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->first();

        if (!$warehouse) {
            return $this->sendError('Location not found', 500);
        }

        if ($warehouse->manufacturingYN == 1) {
            if (is_null($warehouse->WIPGLCode)) {
                return $this->sendError('Please assigned WIP GLCode for this warehouse', 500);
            } else {
                $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companySystemID']);
                if (!$checkGLIsAssigned) {
                    return $this->sendError('Assigned WIP GL Code is not assigned to this company!', 500);
                }
            }
        }


        $documentDate = $input['grvDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('GRV date is not within the financial period!');
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['documentSystemID'] = '3';
        $input['documentID'] = 'GRV';


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
        // get last serial number by company financial year
        $lastSerial = GRVMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('grvSerialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->grvSerialNo) + 1;
        }
        $input['grvSerialNo'] = $lastSerialNumber;
        // get document code
        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }
        if ($documentMaster) { // generate document code
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
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->with(['created_by', 'confirmed_by', 'segment_by', 'location_by', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'grvtype_by', 'supplier_by' => function ($query) {
            $query->selectRaw('CONCAT(primarySupplierCode," | ",supplierName) as supplierName,supplierCodeSystem');
        }, 'currency_by' => function ($query) {
            $query->selectRaw('CONCAT(CurrencyCode," | ",CurrencyName) as CurrencyName,currencyID');
        }])->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'Good Receipt Voucher retrieved successfully');
    }

    /**
     * Update the specified GRVMaster in storage.
     * PUT/PATCH /gRVMasters/{id}
     *
     * @param int $id
     * @param UpdateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = array_except($input, ['created_by', 'confirmed_by', 'location_by', 'segment_by', 'financeperiod_by', 'financeyear_by', 'grvtype_by', 'supplier_by', 'currency_by']);
        $input = $this->convertArrayToValue($input);

        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);


        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        if ($gRVMaster->grvCancelledYN == -1) {
            return $this->sendError('Good Receipt Voucher closed. You cannot edit.', 500);
        }


        $supplier_id = $input['supplierID'];
        $supplierMaster = SupplierMaster::where('supplierCodeSystem',$supplier_id)->first();


        $currentDate = Carbon::parse(now())->format('Y-m-d');
        if (isset($input['grvDate'])) {
            if ($input['grvDate']) {
                $input['grvDate'] = new Carbon($input['grvDate']);
                $input['grvDate'] = $input['grvDate']->format('Y-m-d');

                if ($input['grvDate'] > $currentDate) {
                    return $this->sendError('GRV date can not be greater than current date', 500);
                }
            }
        }

        if (isset($input['stampDate'])) {
            if ($input['stampDate']) {
                $input['stampDate'] = new Carbon($input['stampDate']);
                $input['stampDate'] = $input['stampDate']->format('Y-m-d');

                if ($input['stampDate'] > $currentDate) {
                    return $this->sendError('Stamp date can not be greater than current date', 500);
                }
            }
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

        //checking selected warehouse is active
        $warehouse = WarehouseMaster::where("wareHouseSystemCode", $input['grvLocation'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($warehouse)) {
            return $this->sendError('Selected location is not active. Please select an active location');
        }

        $financeYear = CompanyFinanceYear::where("companyFinanceYearID", $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isCurrent', -1)
            ->first();

        if (empty($financeYear)) {
            return $this->sendError('Selected finance year is not current. Please select current year');
        }

        if ($warehouse->manufacturingYN == 1) {
            if (is_null($warehouse->WIPGLCode)) {
                return $this->sendError('Please assigned WIP GLCode for this warehouse', 500);
            } else {
                $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companySystemID']);
                if (!$checkGLIsAssigned) {
                    return $this->sendError('Assigned WIP GL Code is not assigned to this company!', 500);
                }
            }
        }

        if ($input['grvLocation'] != $gRVMaster->grvLocation) {
            $resWareHouseUpdate = ItemTracking::updateTrackingDetailWareHouse($input['grvLocation'], $id, $gRVMaster->documentSystemID);

            if (!$resWareHouseUpdate['status']) {
                return $this->sendError($resWareHouseUpdate['message'], 500);
            }
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

        //getting transaction amount
        $grvTotalSupplierTransactionCurrency = GRVDetails::select(DB::raw('COALESCE(SUM(GRVcostPerUnitSupTransCur * noQty),0) as transactionTotalSum, COALESCE(SUM(GRVcostPerUnitComRptCur * noQty),0) as reportingTotalSum, COALESCE(SUM(GRVcostPerUnitLocalCur * noQty),0) as localTotalSum, COALESCE(SUM(GRVcostPerUnitSupDefaultCur * noQty),0) as defaultTotalSum'))
            ->where('grvAutoID', $input['grvAutoID'])
            ->first();

        //getting logistic amount
        $grvTotalLogisticAmount = PoAdvancePayment::select(DB::raw('COALESCE(SUM(reqAmountInPOTransCur),0) as transactionTotalSum, COALESCE(SUM(reqAmountInPORptCur),0) as reportingTotalSum, COALESCE(SUM(reqAmountInPOLocalCur),0) as localTotalSum'))
            ->where('grvAutoID', $input['grvAutoID'])
            ->first();

        $input['grvTotalSupplierTransactionCurrency'] = $grvTotalSupplierTransactionCurrency['transactionTotalSum'];
        $input['grvTotalComRptCurrency'] = $grvTotalSupplierTransactionCurrency['reportingTotalSum'];
        $input['grvTotalLocalCurrency'] = $grvTotalSupplierTransactionCurrency['localTotalSum'];
        $input['grvTotalSupplierDefaultCurrency'] = $grvTotalSupplierTransactionCurrency['defaultTotalSum'];


        if ($gRVMaster->grvConfirmedYN == 0 && $input['grvConfirmedYN'] == 1) {
            if ($gRVMaster->grvTypeID == 1) {
                $grvVatDetails = GRVDetails::where('grvAutoID', $input['grvAutoID'])->get();
                foreach ($grvVatDetails as $grvVatDetail) {
                    if ($grvVatDetail->VATAmount > 0) {
                        if ($grvVatDetail->vatMasterCategoryID == null || $grvVatDetail->vatSubCategoryID == null) {
                            return $this->sendError("Please assign a vat category to this item (or) setup a default vat category");
                        }
                    }
                }

                $taxes = Tax::with(['vat_categories'])->where('companySystemID',$input['companySystemID'])->get();
                $vatCategoreis = array();
                foreach ($taxes as $tax)
                {
                    $vatCategoreis[] = $tax->vat_categories;
                }

                if(count($vatCategoreis) > 0 && count(collect(array_flatten($vatCategoreis))->where('subCatgeoryType',3)) == 0)
                {
                    return $this->sendError("The exempt VAT category has not been created. Please set up the required category before proceeding",500);
                }
            }

            if($gRVMaster->grvTypeID == 2)
            {          
                $poInfo = [];
                $grvDetailsInfo = GRVDetails::select('grvDetailsID','purchaseOrderMastertID')->
                where('grvAutoID', $input['grvAutoID'])
                ->with(['po_master' => function($query) {
                    $query->select('purchaseOrderID','purchaseOrderCode','logisticsAvailable');
                }])
                ->groupBY('purchaseOrderMastertID')
                ->get();

                foreach($grvDetailsInfo as $details)
                {

                    $poLogistics = PoAdvancePayment::where('grvAutoID', $input['grvAutoID'])->where('poID', $details->purchaseOrderMastertID)
                    ->where('confirmedYN', 1)
                    ->where('approvedYN', -1)->first();

                    if($details->po_master->logisticsAvailable == -1 && !isset($poLogistics))
                    {
                            array_push($poInfo,$details->po_master->purchaseOrderCode);
                    }

                }



                if(count($poInfo) > 0)
                {
                    return $this->sendError('You cannot confirm this GRV, as the following Purchase Orders have been marked as logistics available, however no logistics has been added to the GRV',500,['type' => 'logistics','data' =>$poInfo]);
    
                }
            }

      

            if(($input['isSupplierBlocked']) && ($gRVMaster->grvTypeID == 2))
            {

                $validatorResult = \Helper::checkBlockSuppliers($input['grvDate'],$supplier_id);
                if (!$validatorResult['success']) {              
                    return $this->sendError('The selected supplier has been blocked. Are you sure you want to proceed ?', 500,['type' => 'blockSupplier']);
    
                }
            }

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            }

            $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($gRVMaster->documentSystemID, $gRVMaster->grvAutoID);

            if (!$trackingValidation['status']) {
                return $this->sendError($trackingValidation["message"], 500, ['type' => 'confirm']);
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 10;
            
            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
            if (!$companyFinancePeriod["success"]) {
                return $this->sendError($companyFinancePeriod["message"], 500);
            } else {
                $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
            }
            
            unset($inputParam);

            $documentDate = $input['grvDate'];
            $monthBegin = Carbon::parse($input['FYBiggin'])->format('Y-m-d');
            $monthEnd = Carbon::parse($input['FYEnd'])->format('Y-m-d');

            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('GRV date is not within the financial period!');
            }

            //getting total sum of PO detail Amount
            $grvMasterSum = GRVDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum, COALESCE(SUM(VATAmount * noQty),0) as masterTotalVAT'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->first();

            $grvDetailExist = GRVDetails::select(DB::raw('grvDetailsID'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->first();

            if (empty($grvDetailExist)) {
                return $this->sendError('GRV document cannot confirm without details');
            }

            $checkQuantity = GRVDetails::where('grvAutoID', $id)
                ->where('noQty', '<=', 0)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least a qty', 500);
            }

            //  remove validation GWL-657
            // check order cost should be greater than zero
//            $checkCost = GRVDetails::where('grvAutoID', $id)
//                ->whereRaw('ROUND((unitcost-addonDistCost),3) < ?', [0])
//                ->selectRaw('ROUND((unitcost-addonDistCost),3)')
//                ->count();


//            if ($checkCost > 0) { // remove validation GWL-601
//                 return $this->sendError('Every item order cost should be greater than or equal to zero', 500);
//            }

            $checkNetAmount = GRVDetails::where('grvAutoID', $id)
                ->whereRaw('ROUND(netAmount,3) < ?', [0])
                ->selectRaw('ROUND(netAmount,3)')
                ->count();
            if ($checkNetAmount > 0) {
                return $this->sendError('Every item net amount should be greater than or equal to zero', 500);
            }

            if ($grvMasterSum->masterTotalSum < 0) {
                return $this->sendError('Total net amount should be greater than or equal to zero', 500);
            }

            if ($gRVMaster->grvTypeID == 2) {
                // checking logistic details  exist and updating grv id in erp_purchaseorderadvpayment  table
                $fetchingGRVDetails = GRVDetails::select(DB::raw('purchaseOrderMastertID'))
                    ->where('grvAutoID', $input['grvAutoID'])
                    ->groupBy('purchaseOrderMastertID')
                    ->get();

                if ($fetchingGRVDetails) {
                    foreach ($fetchingGRVDetails as $der) {
                        $poMaster = ProcumentOrder::find($der['purchaseOrderMastertID']);
                        if ($poMaster->logisticsAvailable == -1) {
                            $poAdvancePaymentdetail = PoAdvancePayment::where('poID', $der['purchaseOrderMastertID'])
                                                                        ->where('isAdvancePaymentYN', 1)
                                                                        ->where('grvAutoID', 0)
                                                                        ->get();
                            if (count($poAdvancePaymentdetail) > 0) {
                                foreach ($poAdvancePaymentdetail as $advance) {
                                    if ($advance['grvAutoID'] == 0) {
                                        $updatePoAdvancePaymentdetail = PoAdvancePayment::find($advance->poAdvPaymentID);
                                        $updatePoAdvancePaymentdetail->grvAutoID = $input['grvAutoID'];
                                        $updatePoAdvancePaymentdetail->save();
                                    }
                                }
                            } else {
                                $grvCheck = PoAdvancePayment::where('poID', $der['purchaseOrderMastertID'])->where('isAdvancePaymentYN', 1)
                                    ->where('grvAutoID', $id)->get();
                                if (count($grvCheck) == 0) {
                                    //return $this->sendError('Added PO ' . $poMaster->purchaseOrderCode . ' has logistics. You can confirm the GRV only after logistics details are updated.');
                                }
                            }
                        }
                    }
                }
            }

            //getting transaction amount
            if ($gRVMaster->grvTypeID == 1) {
                $grvTotalSupplierTransactionCurrency = GRVDetails::select(DB::raw('COALESCE(SUM(GRVcostPerUnitSupTransCur * noQty),0) as transactionTotalSum, COALESCE(SUM(GRVcostPerUnitComRptCur * noQty),0) as reportingTotalSum, COALESCE(SUM(GRVcostPerUnitLocalCur * noQty),0) as localTotalSum, COALESCE(SUM(GRVcostPerUnitSupDefaultCur * noQty),0) as defaultTotalSum'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->first();

                $grvDetailsData = GRVDetails::where('grvAutoID', $input['grvAutoID'])
                                            ->get();

                $exemptVATAmount = 0;
                $lineVATAmountTotal = 0;
                foreach ($grvDetailsData as $key => $value) {
                    $checkVATCategory = TaxVatCategories::with(['type'])->find($value->vatSubCategoryID);
                    if ($checkVATCategory) {
                        if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 1 && $value->exempt_vat_portion > 0 && $value->VATAmount > 0) {
                           $exemptVAT = $value->VATAmount * ($value->exempt_vat_portion / 100);

                           $exemptVATAmount += ($exemptVAT * $value->noQty);
                        } else if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 3) {
                            $exemptVATAmount += ($value->VATAmount * $value->noQty);
                        }
                    }

                    $lineVATAmountTotal += ($value->VATAmount * $value->noQty);
                }

                $currency = \Helper::convertAmountToLocalRpt($gRVMaster->documentSystemID,$input['grvAutoID'],$exemptVATAmount);
                $currencyVAT = \Helper::convertAmountToLocalRpt($gRVMaster->documentSystemID,$input['grvAutoID'],$lineVATAmountTotal);

                $grvTotalSupplierTransactionCurrency['transactionTotalSum'] = $grvTotalSupplierTransactionCurrency['transactionTotalSum'] - $exemptVATAmount + $lineVATAmountTotal;
                $grvTotalSupplierTransactionCurrency['reportingTotalSum'] = $grvTotalSupplierTransactionCurrency['reportingTotalSum'] - $currency['reportingAmount'] + $currencyVAT['reportingAmount'];
                $grvTotalSupplierTransactionCurrency['localTotalSum'] = $grvTotalSupplierTransactionCurrency['localTotalSum'] - $currency['localAmount'] + $currencyVAT['localAmount'];
                $grvTotalSupplierTransactionCurrency['defaultTotalSum'] = $grvTotalSupplierTransactionCurrency['defaultTotalSum'] - $currency['defaultAmount'] + $currencyVAT['defaultAmount'];

            } else {
                $grvTotalSupplierTransactionCurrency = GRVDetails::select(DB::raw('COALESCE(SUM(GRVcostPerUnitSupTransCur * noQty),0) as transactionTotalSum, COALESCE(SUM(GRVcostPerUnitComRptCur * noQty),0) as reportingTotalSum, COALESCE(SUM(GRVcostPerUnitLocalCur * noQty),0) as localTotalSum, COALESCE(SUM(GRVcostPerUnitSupDefaultCur * noQty),0) as defaultTotalSum'))
                    ->where('grvAutoID', $input['grvAutoID'])
                    ->first();
            }

            //getting logistic amount
            $grvTotalLogisticAmount = PoAdvancePayment::select(DB::raw('COALESCE(SUM(reqAmountInPOTransCur),0) as transactionTotalSum, COALESCE(SUM(reqAmountInPORptCur),0) as reportingTotalSum, COALESCE(SUM(reqAmountInPOLocalCur),0) as localTotalSum'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->first();


            $input['grvTotalSupplierTransactionCurrency'] = $grvTotalSupplierTransactionCurrency['transactionTotalSum'];
            $input['grvTotalComRptCurrency'] = $grvTotalSupplierTransactionCurrency['reportingTotalSum'];
            $input['grvTotalLocalCurrency'] = $grvTotalSupplierTransactionCurrency['localTotalSum'];
            $input['grvTotalSupplierDefaultCurrency'] = $grvTotalSupplierTransactionCurrency['defaultTotalSum'];

            //updating logistic details in grv details table
            $fetchAllGrvDetails = GRVDetails::where('grvAutoID', $input['grvAutoID'])
                ->get();

            if ($fetchAllGrvDetails) {
                $accountValidationArray = [];
                foreach ($fetchAllGrvDetails as $row) {
                    $updateGRVDetail_log_detail = GRVDetails::find($row['grvDetailsID']);

                    $logisticsCharges_TransCur = ($input['grvTotalSupplierTransactionCurrency'] == null || $input['grvTotalSupplierTransactionCurrency'] == 0) ? 0 : ((($row['noQty'] * $row['GRVcostPerUnitSupTransCur']) / ($input['grvTotalSupplierTransactionCurrency'])) * $grvTotalLogisticAmount['transactionTotalSum']) / $row['noQty'];

                    $logisticsCharges_LocalCur = ($input['grvTotalLocalCurrency'] == null || $input['grvTotalLocalCurrency'] == 0) ? 0 : ((($row['noQty'] * $row['GRVcostPerUnitLocalCur']) / ($input['grvTotalLocalCurrency'])) * $grvTotalLogisticAmount['localTotalSum']) / $row['noQty'];

                    $logisticsChargest_RptCur = ($input['grvTotalComRptCurrency'] == null || $input['grvTotalComRptCurrency'] == 0) ? 0 : ((($row['noQty'] * $row['GRVcostPerUnitComRptCur']) / ($input['grvTotalComRptCurrency'])) * $grvTotalLogisticAmount['reportingTotalSum']) / $row['noQty'];

                    $updateGRVDetail_log_detail->logisticsCharges_TransCur = \Helper::roundValue($logisticsCharges_TransCur);
                    $updateGRVDetail_log_detail->logisticsCharges_LocalCur = \Helper::roundValue($logisticsCharges_LocalCur);
                    $updateGRVDetail_log_detail->logisticsChargest_RptCur = \Helper::roundValue($logisticsChargest_RptCur);

                    $exemptExpenseDetails = TaxService::processGrvExpenseDetail($row['grvDetailsID']);
                    $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($row) {
                        $query->where('companySystemID', $row['companySystemID']);
                    })->where('isActive', 1)->first();


                    if(!empty($exemptExpenseDetails) && !empty($expenseCOA) && $expenseCOA->expenseGL != null){
                        $exemptVatTrans = $exemptExpenseDetails->VATAmount;
                        $exemptVATLocal = $exemptExpenseDetails->VATAmountLocal;
                        $exemptVatRpt = $exemptExpenseDetails->VATAmountRpt;
                    } else {
                        $exemptVatTrans = 0;
                        $exemptVATLocal = 0;
                        $exemptVatRpt = 0;
                    }


                    $updateGRVDetail_log_detail->landingCost_TransCur = \Helper::roundValue($logisticsCharges_TransCur) + $row['GRVcostPerUnitSupTransCur'] - $exemptVatTrans;
                    $updateGRVDetail_log_detail->landingCost_LocalCur = \Helper::roundValue($logisticsCharges_LocalCur) + $row['GRVcostPerUnitLocalCur'] - $exemptVATLocal;
                    $updateGRVDetail_log_detail->landingCost_RptCur = \Helper::roundValue($logisticsChargest_RptCur) + $row['GRVcostPerUnitComRptCur'] - $exemptVatRpt;

                    $updateGRVDetail_log_detail->save();


                    if ($row['includePLForGRVYN'] == -1 && !is_null($row['financeGLcodePLSystemID']) && $row['financeGLcodePLSystemID'] > 0) {
                        $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($row['financeGLcodePLSystemID'], $gRVMaster->companySystemID);
                        if (!$checkGLIsAssigned) {
                            return $this->sendError('PL account is not assigned to the company', 500);
                        }
                    }

                    if (!is_null($row['financeGLcodebBSSystemID']) && $row['financeGLcodebBSSystemID'] > 0) {
                        $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($row['financeGLcodebBSSystemID'], $gRVMaster->companySystemID);
                        if (!$checkGLIsAssigned) {
                            return $this->sendError('BS account is not assigned to the company', 500);
                        }
                    }


                    if (is_null($row->itemFinanceCategoryID)) {
                        $accountValidationArray[] = "Finance category of " . $row->itemPrimaryCode . " not found";
                    } else {
                        switch ($row->itemFinanceCategoryID) {
                            case 1:
                                if (is_null($row->financeGLcodebBSSystemID) || is_null($row->financeGLcodePLSystemID) || $row->financeGLcodebBSSystemID == 0 || $row->financeGLcodePLSystemID == 0) {

                                    $accountValidationArray[1][] = $row->itemPrimaryCode;
                                }
                                break;
                            case 2:
                            case 3:
                            case 4:
                                if ((is_null($row->financeGLcodebBSSystemID) || $row->financeGLcodebBSSystemID == 0) && (is_null($row->financeGLcodePLSystemID) || $row->financeGLcodePLSystemID == 0)) {
                                    $accountValidationArray[1][] = "Finance category accounts are not updated correctly. Please check the finance category configurations for the item " . $row->itemPrimaryCode;
                                }

                                if ((is_null($row->financeGLcodebBSSystemID) || $row->financeGLcodebBSSystemID == 0) && !is_null($row->financeGLcodePLSystemID) && $row->financeGLcodePLSystemID != 0 && $row->includePLForGRVYN != -1) {
                                    $accountValidationArray[2][] = $row->itemPrimaryCode;
                                }
                                break;

                            default:
                                # code...
                                break;
                        }
                    }

                }
                

                if (!empty($accountValidationArray)) {
                    $accountValidationErrrArray = [];
                    if (isset($accountValidationArray[1])) {
                        $itemsA = implode(", ", $accountValidationArray[1]);
                        $accountValidationErrrArray[] = "Finance category accounts are not updated correctly. Please check the finance category configurations for the item(s) " . $itemsA;
                    }

                    if (isset($accountValidationArray[2])) {
                        $itemsB = implode(", ", $accountValidationArray[2]);
                        $accountValidationErrrArray[] = "Expense account configuration is not done correctly. Activate includePLforGRVYN for the item(s) " . $itemsB;
                    }
                    return $this->sendError($accountValidationErrrArray, 420);
                }
            }

            unset($input['grvConfirmedYN']);
            unset($input['grvConfirmedByEmpSystemID']);
            unset($input['grvConfirmedByEmpID']);
            unset($input['grvConfirmedByName']);
            unset($input['grvConfirmedDate']);


            $rptCurrencyId = isset($gRVMaster->companyReportingCurrencyID) ? $gRVMaster->companyReportingCurrencyID : 2;
            $rptCurrencyDecimalPlaces = Helper::getCurrencyDecimalPlace($rptCurrencyId);

            // check the logistic charges from GRv table
            $grvLogisticAmount = GRVDetails::select(DB::raw('COALESCE(SUM(noQty*logisticsCharges_LocalCur),0) as grvLocal, COALESCE(SUM(noQty*logisticsChargest_RptCur),0) as grvReport'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->groupBy('grvAutoID')
                ->first();

            $grvReportAmount = round(isset($grvLogisticAmount->grvReport) ? $grvLogisticAmount->grvReport : 0, $rptCurrencyDecimalPlaces);


            // get the logistic charges from PO table which is linked to the grv
            $poLogisticAmount = PoAdvancePayment::select(DB::raw('COALESCE(SUM(reqAmountInPOLocalCur),0) as poLocal, COALESCE(SUM(reqAmountInPORptCur),0) as poReport, COALESCE(SUM(VATAmount),0) as logisticVAT'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->groupBy('grvAutoID')
                ->first();
            $grvPoAmount = round((isset($poLogisticAmount->poReport) ? $poLogisticAmount->poReport : 0), $rptCurrencyDecimalPlaces);

            // logistic charges from PO table should not be greater than data from grv table
            if ($grvPoAmount > $grvReportAmount) {
                return $this->sendError('PO logistic amount cannot be greater than GRV logistic amount.' . 'GRV Logistic Amount is' . $grvReportAmount . ' And PO Logistic Amount is ' . $grvPoAmount, 500);
            }

            $different = abs($input['grvTotalSupplierTransactionCurrency'] - $grvMasterSum['masterTotalSum']);
            

            if ($different < 0.01) {
                // same
            } else {
                return $this->sendError('Cannot confirm. GRV Master and Detail shows a difference in total.', 500);
            }

            //check Input Vat Transfer GL Account if vat exist
            $totalVAT = GRVDetails::where('grvAutoID',$id)->selectRaw('SUM(VATAmount*noQty) as totalVAT')->first();
            if((TaxService::checkGRVVATEligible($gRVMaster->companySystemID,$gRVMaster->supplierID) && !empty($totalVAT) && $totalVAT->totalVAT > 0) || (!empty($poLogisticAmount) && $poLogisticAmount->logisticVAT > 0)){
                if(empty(TaxService::getInputVATTransferGLAccount($gRVMaster->companySystemID))){
                    return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not configured.', 500);
                }

                $inputVATGL = TaxService::getInputVATTransferGLAccount($gRVMaster->companySystemID);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatTransferGLAccountAutoID, $gRVMaster->companySystemID);

                if (!$checkAssignedStatus) {
                    return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not assigned to company.', 500);
                }
            }



            if(TaxService::isGRVRCMActivation($id) && !empty($totalVAT) && $totalVAT->totalVAT > 0 ){
                if(empty(TaxService::getOutputVATTransferGLAccount($gRVMaster->companySystemID))){
                    return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                }

                $outputVATGL = TaxService::getOutputVATTransferGLAccount($gRVMaster->companySystemID);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($outputVATGL->outputVatTransferGLAccountAutoID, $gRVMaster->companySystemID);

                if (!$checkAssignedStatus) {
                    return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not assigned to company.', 500);
                }
            }

            $object = new ChartOfAccountValidationService();
            $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);

            if (isset($result) && !empty($result["accountCodes"])) {
                return $this->sendError($result["errorMsg"]);
            }



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


        if(isset($gRVMaster->deliveryAppoinmentID))
        {
           $selected_segment = $gRVMaster->serviceLineSystemID;
           $appoinmnet_po_ids =  AppointmentDetails::whereHas('po_master',function($q) use($selected_segment){
            $q->where('serviceLineSystemID',$selected_segment);
                })->where('appointment_id',$gRVMaster->deliveryAppoinmentID)->pluck('po_detail_id')->toArray();

           $grv_purchase =  GRVDetails::where('grvAutoID',$id)->pluck('purchaseOrderDetailsID')->toArray();


           $appoinmnet_details =  AppointmentDetails::whereHas('po_master',function($q) use($selected_segment){
            $q->where('serviceLineSystemID',$selected_segment);
             })->where('appointment_id',$gRVMaster->deliveryAppoinmentID)->get();


            $extra_po =  array_values(array_diff($grv_purchase,$appoinmnet_po_ids));

            $ignore_po =  array_values(array_diff($appoinmnet_po_ids,$grv_purchase));


           $total_msg = '';
           $extra_po_msg = [];

           if(count($extra_po) > 0)
           {
             foreach($extra_po as $extra)
             {

               $extra_po_msg_info =  GRVDetails::where('grvAutoID',$id)->where('purchaseOrderDetailsID',$extra)->with(['po_master'=>function($q){
                $q->select('purchaseOrderID','purchaseOrderCode');
               }])->select('grvDetailsID','itemPrimaryCode','itemDescription','noQty','purchaseOrderMastertID')->get();

               foreach($extra_po_msg_info as $info)
               {
                array_push($extra_po_msg,$info);

               }

             }
           
           }
           else
           {
            $extra_po_msg = [];
           }

           $ignore_po_msg = [];
           if(count($ignore_po) > 0)
           {
             foreach($ignore_po as $extra)
             {

                $ignore_po_msg_info =  AppointmentDetails::where('po_detail_id',$extra)->where('appointment_id',$gRVMaster->deliveryAppoinmentID)->with(['po_master'=>function($q){
                    $q->select('purchaseOrderID','purchaseOrderCode');
                   },'item'=>function($q){
                    $q->select('itemCodeSystem','primaryCode','itemDescription');
                   }])->select('id','qty','po_master_id','item_id')->get();

                   foreach($ignore_po_msg_info as $info)
                   {
                    array_push($ignore_po_msg,$info);
    
                   }

             }
           }
           else
           {
            $ignore_po_msg = [];
        
           }
           
         
           $appointment_info = Appointment::where('id',$gRVMaster->deliveryAppoinmentID)->select('id','primary_code')->first();

      

           $changes_item = [];
           foreach($appoinmnet_details as $po)
           {
                
              $planeed_qty =  $po->qty;
              $po_detail_id = $po->po_detail_id;
              $grv_Details = GRVDetails::where('grvAutoID',$id)->where('purchaseOrderMastertID',$po->po_master_id)->where('purchaseOrderDetailsID',$po_detail_id)
                            ->with(['po_master'=>function($q){
                                $q->select('purchaseOrderID','purchaseOrderCode');
                            }])->first();
              if(isset($grv_Details))
              {

                $grv_changes['po_code'] = $po->po_master->purchaseOrderCode;
                $grv_changes['item'] = $grv_Details->itemPrimaryCode;
                $grv_changes['description'] = $grv_Details->itemDescription;
                $grv_changes['appoinment_qty'] = $po->qty;
                $grv_changes['grv_qty'] = $grv_Details->noQty;
                $changes_item[]=$grv_changes;

           
              }

              
           }
         
       
            $body = "Dear Supplier, <br><br> Please be informed GRV <b>$gRVMaster->grvPrimaryCode</b> created for delivery appointment <b>$appointment_info->primary_code</b>  is confirmed. 
            <br><br>Please note below changes.<br><br> <b>Extra purchase order documents added to GRV</b><br><br>";
            $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: center;border: 1px solid black;">PO Code</th> 
                    <th style="text-align: center;border: 1px solid black;">Item Code</th>
                    <th style="text-align: center;border: 1px solid black;">Item Description </th> 
                    <th style="text-align: center;border: 1px solid black;">Qty </th> 
                </tr>
            </thead>';
            $body .= '<tbody>';
            foreach ($extra_po_msg as $val) {
                $body .= '<tr>
                    <td style="text-align:center;border: 1px solid black;">' . $val->po_master->purchaseOrderCode . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val->itemPrimaryCode . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val->itemDescription . '</td>   
                    <td style="text-align:center;border: 1px solid black;">' . $val->noQty . '</td>  
                </tr>';
            
            }
            $body .= '</tbody>
            </table>';
            $body .= "<br><br>";
            $body .= "<b>Purchase order documents removed from GRV</b> <br><br>";
            $body .= "<br><br>";
            $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-center: center;border: 1px solid black;">PO Code</th> 
                    <th style="text-center: center;border: 1px solid black;">Item Code</th>
                    <th style="text-center: center;border: 1px solid black;">Item Description </th> 
                    <th style="text-center: center;border: 1px solid black;">Qty </th> 
                </tr>
            </thead>';
            $body .= '<tbody>';
            foreach ($ignore_po_msg as $val) {
                $body .= '<tr>
                    <td style="text-align:center;border: 1px solid black;">' . $val->po_master->purchaseOrderCode . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val->item->primaryCode . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val->item->itemDescription . '</td>   
                    <td style="text-align:center;border: 1px solid black;">' . $val->qty . '</td>  
                </tr>';
            
                }
            $body .= '</tbody>
            </table>';
            $body .= "<br><br>";
            $body .= "<b>Quantity changes from delivery appointment</b> <br><br>";
            $body .= "<br><br>";
            $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: center;border: 1px solid black;">PO Code</th> 
                    <th style="text-align: center;border: 1px solid black;">Item Code</th>
                    <th style="text-align: center;border: 1px solid black;">Item Description </th> 
                    <th style="text-align: center;border: 1px solid black;">Appointment Qty </th> 
                    <th style="text-align: center;border: 1px solid black;">GRV Qty </th> 
                </tr>
            </thead>';
            $body .= '<tbody>';
            foreach ($changes_item as $val) {
                $body .= '<tr>
                    <td style="text-align:center;border: 1px solid black;">' . $val['po_code'] . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val['item'] . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val['description'] . '</td>   
                    <td style="text-align:center;border: 1px solid black;">' . $val['appoinment_qty'] . '</td>  
                    <td style="text-align:center;border: 1px solid black;">' . $val['grv_qty'] . '</td>  
                </tr>';
            
                }
            $body .= '</tbody>
            </table>';
            $body .= "<br><br>";
            $body .= "Thank You.";

            $supplier = $this->getSupplierDetails($input['supplierID']);
            if(isset($supplier) && !empty($supplier)){ 
                $dataEmail['empEmail'] = $supplier->supEmail;
                $dataEmail['companySystemID'] = $input['companySystemID'];
                $dataEmail['alertMessage'] = "GRV  Confirmed";
                $dataEmail['emailAlertMessage'] = $body;
                $sendEmail = \Email::sendEmailErp($dataEmail); 
            } 
        }


        return $this->sendReponseWithDetails($gRVMaster->toArray(), 'GRV updated successfully',1, $confirm['data'] ?? null);
    }

    /**
     * Remove the specified GRVMaster from storage.
     * DELETE /gRVMasters/{id}
     *
     * @param int $id
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
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'grvTypeID', 'projectID'));

        $grvLocation = $request['grvLocation'];
        $grvLocation = (array)$grvLocation;
        $grvLocation = collect($grvLocation)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $projectID = $request['projectID'];
        $projectID = (array)$projectID;
        $projectID = collect($projectID)->pluck('id');


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        
        $search = $request->input('search.value');

        $grvMaster = $this->gRVMasterRepository->grvListQuery($request,$input,$search,$grvLocation, $serviceLineSystemID, $projectID);

        $policySuplierEvaluation = CompanyPolicyMaster::where('companyPolicyCategoryID', 92)
            ->where('companySystemID', $input['companyId'])->first();
        $supplierEvaluationEnabled = 0;

        if(!empty($policySuplierEvaluation)) {
            $supplierEvaluationEnabled = $policySuplierEvaluation->isYesNO;
        }

        $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $input['companyId'])->first();

        $policy = 0;

        if (!empty($historyPolicy)) {
            $policy = $historyPolicy->isYesNO;
        }

        return \DataTables::eloquent($grvMaster)
            ->addColumn('Actions', $policy)
            ->addColumn('SupplierEvaluationPolicy', $supplierEvaluationEnabled)
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

        $grvAutoID = isset($request['grvAutoID']) ? $request['grvAutoID'] : 0;
        $wareHouseBinLocations = array();
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

        $locations = Location::where('is_deleted',0)->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $grvTypes = "";
        if (isset($request['type']) && $request['type'] != 'filter') {
            $allowDirectGrv = CompanyPolicyMaster::where('companyPolicyCategoryID', 30)
                ->where('companySystemID', $companyId)
                ->where('isYesNO', 1)
                ->first();

            if ($allowDirectGrv) {
                $grvTypes = GRVTypes::all();
            } else {
                $grvTypes = GRVTypes::where('grvTypeID', 2)->get();
            }
        } else {
            $grvTypes = GRVTypes::all();
        }


        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && ($request['type'] == 'add' || $request['type'] == 'edit')) {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
            $companyFinanceYear = $companyFinanceYear->where('isCurrent', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
            ->where('companySystemID', $companyId)
            ->first();

        $assetAllocatePolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 61)
        ->where('companySystemID', $companyId)
        ->where('isYesNO', 1)
        ->first();


        $warehouseBinLocationPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 40)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();

        if ($warehouseBinLocationPolicy) {
            $request['warehouseSystemCode'] = 0;
            if ($grvAutoID) {
                $grvMaster = GRVMaster::find($grvAutoID);
                if (!empty($grvMaster)) {
                    $request['warehouseSystemCode'] = $grvMaster->grvLocation;
                }
            }

            $wareHouseBinLocations = WarehouseBinLocation::where('companySystemID', $companyId)
                ->where('wareHouseSystemCode', $request['warehouseSystemCode'])
                ->where('isDeleted', 0)
                ->where('isActive', -1)
                ->get();
        }

        $hasEEOSSPolicy = false;
        if($grvAutoID){
            $grvMaster = GRVMaster::find($grvAutoID);
            $supplierAssigned= SupplierAssigned::where('supplierCodeSytem',$grvMaster->supplierID)
                ->where('companySystemID',$grvMaster->companySystemID)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();

            if(!empty($supplierAssigned) && $supplierAssigned->isMarkupPercentage){
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $supplierAssigned->companySystemID)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO',1)
                    ->exists();
            }

        }

        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $companyId)
        ->where('isYesNO', 1)
        ->exists();
        $projects = [];

        if (isset($request['type']) && $request['type'] == 'edit') {
            $projectGrvMaster = GRVMaster::find($grvAutoID);
            $serviceLineSystemID = $projectGrvMaster->serviceLineSystemID;
            $projects = ErpProjectMaster::where('serviceLineSystemID', $serviceLineSystemID)->get();
        } else {
            $projects = ErpProjectMaster::all();
        }
        

        $markupAmendRestrictionPolicy = Helper::checkRestrictionByPolicy($companyId,6);

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
            'companyFinanceYear' => $companyFinanceYear,
            'companyPolicy' => $allowPartialGRVPolicy,
            'assetAllocatePolicy' => $assetAllocatePolicy ? true : false,
            'warehouseBinLocationPolicy' => $warehouseBinLocationPolicy,
            'wareHouseBinLocations' => $wareHouseBinLocations,
            'isEEOSSPolicy' => $hasEEOSSPolicy,
            'markupAmendRestrictionPolicy' => $markupAmendRestrictionPolicy,
            'isProjectBase' => $isProject_base,
            'projects' => $projects,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getBinLocationsByWarehouse(Request $request)
    {

        $id = isset($request['id']) ? $request['id'] : 0;
        $wareHouseBinLocations = array();
        if ($id) {
            $wareHouseBinLocations = WarehouseBinLocation::where('wareHouseSystemCode', $id)
                ->get();
        }

        return $this->sendResponse($wareHouseBinLocations, 'Record retrieved successfully');
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
            }, 'details'=> function ($query) {
                $query->with('po_master');
            }, 'company_by', 'currency_by', 'companydocumentattachment_by' => function ($query) {
                $query->where('documentSystemID', 3);
            }, 'location_by', 'audit_trial.modified_by'])->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), trans('custom.record_retrieve', ['attribute' => trans('custom.grv')]));
    }

    public function getGRVMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 3)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_grvmaster.grvAutoID',
            'erp_grvmaster.grvPrimaryCode',
            'erp_grvmaster.documentSystemID',
            'erp_grvmaster.grvDoRefNo',
            'erp_grvmaster.grvDate',
            'erp_grvmaster.supplierPrimaryCode',
            'erp_grvmaster.supplierName',
            'erp_grvmaster.grvNarration',
            'erp_grvmaster.serviceLineCode',
            'erp_grvmaster.createdDateTime',
            'erp_grvmaster.grvConfirmedDate',
            'erp_grvmaster.grvTotalSupplierTransactionCurrency',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription',
            'warehousemaster.wareHouseDescription as wareHouseSet',
            'erp_grvtpes.des'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 3)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('erp_grvmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'grvAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_grvmaster.companySystemID', $companyID)
                ->where('erp_grvmaster.approved', 0)
                ->where('erp_grvmaster.grvConfirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('currencymaster', 'supplierTransactionCurrencyID', '=', 'currencyID')
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_grvmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->join('warehousemaster', 'erp_grvmaster.grvLocation', 'warehousemaster.wareHouseSystemCode')
            ->join('erp_grvtpes', 'erp_grvtpes.grvTypeID', 'erp_grvmaster.grvTypeID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 3)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('grvNarration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $grvMasters = [];
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function getApprovedGRVForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_grvmaster.grvAutoID',
            'erp_grvmaster.grvPrimaryCode',
            'erp_grvmaster.documentSystemID',
            'erp_grvmaster.grvDoRefNo',
            'erp_grvmaster.grvDate',
            'erp_grvmaster.supplierPrimaryCode',
            'erp_grvmaster.supplierName',
            'erp_grvmaster.grvNarration',
            'erp_grvmaster.serviceLineCode',
            'erp_grvmaster.createdDateTime',
            'erp_grvmaster.grvConfirmedDate',
            'erp_grvmaster.grvTotalSupplierTransactionCurrency',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription',
            'warehousemaster.wareHouseDescription as wareHouseSet',
            'erp_grvtpes.des'
        )->join('erp_grvmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'grvAutoID')
                ->where('erp_grvmaster.companySystemID', $companyID)
                ->where('erp_grvmaster.approved', -1)
                ->where('erp_grvmaster.grvConfirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('currencymaster', 'supplierTransactionCurrencyID', '=', 'currencyID')
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_grvmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->join('warehousemaster', 'erp_grvmaster.grvLocation', 'warehousemaster.wareHouseSystemCode')
            ->join('erp_grvtpes', 'erp_grvtpes.grvTypeID', 'erp_grvmaster.grvTypeID')
            ->where('erp_documentapproved.documentSystemID', 3)
            ->where('erp_documentapproved.companySystemID', $companyID)->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('grvNarration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function approveGoodReceiptVoucher(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }


    public function rejectGoodReceiptVoucher(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function goodReceiptVoucherPrintPDF(Request $request)
    {
        $id = $request->get('id');
        $grvMaster = $this->gRVMasterRepository->findWithoutFail($id);
        if (empty($grvMaster)) {
            return $this->sendError('GRV Master not found');
        }

        $outputRecord = $this->gRVMasterRepository->with(['created_by', 'confirmed_by',
            'cancelled_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 3);
            }, 'details'=> function ($query) {
                $query->with('po_master');
            }, 'company_by', 'currency_by', 'companydocumentattachment_by' => function ($query) {
                $query->where('documentSystemID', 3);
            }])->findWithoutFail($id);

        $grv = array(
            'grvData' => $outputRecord
        );

        $html = view('print.good_receipt_voucher_print_pdf', $grv);
        $time = strtotime("now");
        $fileName = 'good_receipt_voucher_' . $id . '_' . $time . '.pdf';

        $htmlFooter = view('print.good_receipt_voucher_footer', $grv);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function pullPOAttachment(Request $request)
    {
        $input = $request->all();

        $attachmentFound = 0;

        $grvAutoID = $input['grvAutoID'];
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];

        $poIDS = GRVDetails::where('grvAutoID', $grvAutoID)
            ->groupBy('purchaseOrderMastertID')
            ->pluck('purchaseOrderMastertID');

        $company = Company::where('companySystemID', $companySystemID)->first();
        if ($company) {
            $companyID = $company->CompanyID;
        }

        $document = DocumentMaster::where('documentSystemID', $documentSystemID)->first();
        if ($document) {
            $documentID = $document->documentID;
        }

        if (!empty($poIDS)) {
            $docAttachement = DocumentAttachments::whereIN('documentSystemCode', $poIDS)
                ->where('companySystemID', $companySystemID)
                ->whereIN('documentSystemID', [2, 5, 52])
                ->get();
            if (!empty($docAttachement->toArray())) {
                $attachmentFound = 1;
                foreach ($docAttachement as $doc) {
                    $documentAttachments = new DocumentAttachments();
                    $documentAttachments->companySystemID = $companySystemID;
                    $documentAttachments->companyID = $companyID;
                    $documentAttachments->documentID = $documentID;
                    $documentAttachments->documentSystemID = $documentSystemID;
                    $documentAttachments->documentSystemCode = $grvAutoID;
                    $documentAttachments->attachmentDescription = $doc['attachmentDescription'];
                    $documentAttachments->path = $doc['path'];
                    $documentAttachments->originalFileName = $doc['originalFileName'];
                    $documentAttachments->myFileName = $doc['myFileName'];
                    $documentAttachments->docExpirtyDate = $doc['docExpirtyDate'];
                    $documentAttachments->attachmentType = $doc['attachmentType'];
                    $documentAttachments->sizeInKbs = $doc['sizeInKbs'];
                    $documentAttachments->isUploaded = $doc['isUploaded'];
                    $documentAttachments->pullFromAnotherDocument = -1;
                    $documentAttachments->save();
                }
            }
        }
        if ($attachmentFound == 0) {
            return $this->sendError('No Attachments Found', 500);
        } else {
            return $this->sendResponse($grvAutoID, 'PO attachments pulled successfully');
        }


    }

    public function getGoodReceiptVoucherReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $grvAutoID = $input['grvAutoID'];

            $grvMasterData = GRVMaster::find($grvAutoID);
            $emails = array();
            if (empty($grvMasterData)) {
                return $this->sendError('Good Receipt Voucher not found');
            }

            if ($grvMasterData->RollLevForApp_curr > 1) {
                return $this->sendError('You cannot reopen this GRV it is already partially approved');
            }

            if ($grvMasterData->approved == -1) {
                return $this->sendError('You cannot reopen this GRV it is already fully approved');
            }

            if ($grvMasterData->grvConfirmedYN == 0) {
                return $this->sendError('You cannot reopen this GRV, it is not confirmed');
            }

            // updating fields
            $grvMasterData->grvConfirmedYN = 0;
            $grvMasterData->grvConfirmedByEmpSystemID = null;
            $grvMasterData->grvConfirmedByEmpID = null;
            $grvMasterData->grvConfirmedByName = null;
            $grvMasterData->grvConfirmedDate = null;
            $grvMasterData->RollLevForApp_curr = 1;
            $grvMasterData->isMarkupUpdated = 0;
            $grvMasterData->save();

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $grvMasterData->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $grvMasterData->grvPrimaryCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $grvMasterData->grvPrimaryCode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $grvMasterData->companySystemID)
                ->where('documentSystemCode', $grvMasterData->grvAutoID)
                ->where('documentSystemID', $grvMasterData->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $grvMasterData->companySystemID)
                        ->where('documentSystemID', $grvMasterData->documentSystemID)
                        ->first();

                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => 'Policy not found for this document'];
                    }

                    $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                        ->where('companySystemID', $documentApproval->companySystemID)
                        ->where('documentSystemID', $documentApproval->documentSystemID);

                    if ($companyDocument['isServiceLineApproval'] == -1) {
                        $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                    }

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

            DocumentApproved::where('documentSystemCode', $grvAutoID)
                ->where('companySystemID', $grvMasterData->companySystemID)
                ->where('documentSystemID', $grvMasterData->documentSystemID)
                ->delete();

            UnbilledGrvGroupBy::where('companySystemID', $grvMasterData->companySystemID)->where('grvAutoID', $grvAutoID)->delete();

            UnbilledGRV::where('companySystemID', $grvMasterData->companySystemID)->where('grvAutoID', $grvAutoID)->delete();

            /*Audit entry*/
            AuditTrial::createAuditTrial($grvMasterData->documentSystemID,$grvAutoID,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($grvMasterData->toArray(), 'Good Receipt Voucher reopened successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error Occurred', 500);
        }
    }

    public function getItemsOptionForGRV(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $items = ItemAssigned::where('companySystemID', $companyID)
                             ->where('isActive', 1)
                             ->where('isAssigned', -1)
                             ->whereHas('item_category_type', function ($query) {
                                $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                             })
                             ->when((isset($input['fixedAsset']) && $input['fixedAsset'] == 0), function($query) {
                                $query->whereIn('financeCategoryMaster', [1,2,4]);
                             });
        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function getFilteredGRV(Request $request)
    {
        $input = $request->all();
        $seachText = $input['seachText'];
        $seachText = str_replace("\\", "\\\\", $seachText);
        $companyID = $input['companyID'];
        $grv = GRVMaster::select('grvAutoID', 'grvPrimaryCode')
            ->where('approved', -1)
            //->where('companySystemID',$companyID)
            ->where('grvPrimaryCode', 'LIKE', "%{$seachText}%")
            ->orderBy('grvAutoID', 'desc')
            ->take(30)
            ->get()->toArray();
        return $this->sendResponse($grv, 'Data retrieved successfully');
    }

    public function getSupplierInvoiceStatusHistoryForGRV(Request $request)
    {

        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $grvAutoID = $input['grvAutoID'];

        $detail = DB::select('SELECT
	erp_bookinvsuppmaster.bookingDate,
	erp_bookinvsuppmaster.bookingInvCode,
	erp_bookinvsuppmaster.comments,
	erp_bookinvsuppmaster.supplierInvoiceNo,
	suppliermaster.supplierName,
	transCurrencymaster.CurrencyCode AS SupTransCur,
	transCurrencymaster.DecimalPlaces AS SupTransDec,
	locCurrencymaster.CurrencyCode AS LocCur,
	locCurrencymaster.DecimalPlaces AS LocDec,
	rptCurrencymaster.CurrencyCode AS RptCur,
	rptCurrencymaster.DecimalPlaces AS RptDec,
	erp_bookinvsuppdet.totTransactionAmount,
	erp_bookinvsuppdet.totLocalAmount,
	erp_bookinvsuppdet.totRptAmount,
erp_bookinvsuppmaster.confirmedYN,
erp_bookinvsuppmaster.approved,
erp_bookinvsuppmaster.bookingSuppMasInvAutoID
FROM
	erp_bookinvsuppdet
LEFT JOIN erp_bookinvsuppmaster ON erp_bookinvsuppdet.bookingSuppMasInvAutoID = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
LEFT JOIN suppliermaster ON erp_bookinvsuppdet.supplierID = suppliermaster.supplierCodeSystem
LEFT JOIN currencymaster AS transCurrencymaster ON erp_bookinvsuppdet.supplierTransactionCurrencyID = transCurrencymaster.currencyID
LEFT JOIN currencymaster AS locCurrencymaster ON erp_bookinvsuppdet.localCurrencyID = locCurrencymaster.currencyID
LEFT JOIN currencymaster AS rptCurrencymaster ON erp_bookinvsuppdet.companyReportingCurrencyID = rptCurrencymaster.currencyID
WHERE
	erp_bookinvsuppdet.grvAutoID = ' . $grvAutoID . '
AND erp_bookinvsuppdet.companySystemID = ' . $companySystemID . '');

        return $this->sendResponse($detail, 'Details retrieved successfully');
    }


    public function getGoodReceiptVoucherAmend(Request $request)
    {
        $input = $request->all();

        $grvAutoID = $input['grvAutoID'];

        $grvMasterData = GRVMaster::find($grvAutoID);
        if (empty($grvMasterData)) {
            return $this->sendError('Good receipt voucher not found');
        }

        if ($grvMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this good receipt voucher');
        }

        $grvMasterDataArray = $grvMasterData->toArray();


        $storeGoodReceiptHistory = GrvMasterRefferedback::insert($grvMasterDataArray);

        $fetchGoodReceiptDetails = GRVDetails::where('grvAutoID', $grvAutoID)
            ->get();

        if (!empty($fetchGoodReceiptDetails)) {
            foreach ($fetchGoodReceiptDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $grvMasterData->timesReferred;
            }
        }

        $GoodReceiptVoucherDetailArray = $fetchGoodReceiptDetails->toArray();

        $storeGoodReceiptVoucherDetailHistory = GrvDetailsRefferedback::insert($GoodReceiptVoucherDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $grvAutoID)
            ->where('companySystemID', $grvMasterData->companySystemID)
            ->where('documentSystemID', $grvMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $grvMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();


        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $grvAutoID)
            ->where('companySystemID', $grvMasterData->companySystemID)
            ->where('documentSystemID', $grvMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $grvMasterData->refferedBackYN = 0;
            $grvMasterData->isMarkupUpdated = 0;
            $grvMasterData->grvConfirmedYN = 0;
            $grvMasterData->grvConfirmedByEmpSystemID = null;
            $grvMasterData->grvConfirmedByEmpID = null;
            $grvMasterData->grvConfirmedByName = null;
            $grvMasterData->grvConfirmedDate = null;
            $grvMasterData->RollLevForApp_curr = 1;
            $grvMasterData->save();
        }

        return $this->sendResponse($grvMasterData->toArray(), 'Good receipt voucher amend successfully');
    }

    public function cancelGRVPreCheck(Request $request)
    {
        $input = $request->all();
        $isEligible = $this->gRVMasterRepository->isGrvEligibleForCancellation($input);
        if ($isEligible['status'] == 1) {
            return $this->sendResponse([], 'GRV Eligible for cancellation');
        }
        $errorMsg = (isset($isEligible['msg']) && $isEligible['msg'] != '') ? $isEligible['msg'] : 'GRV Not Eligible for cancellation';
        return $this->sendError($errorMsg, 500);
    }

    public function reverseGRVPreCheck(Request $request)
    {
        $input = $request->all();

        $isEligible = $this->gRVMasterRepository->isGrvEligibleForCancellation($input, 'reversal');

        if ($isEligible['status'] == 0) {
            return $this->sendError(
                $isEligible['msg'],
                $isEligible['code'] ?? 500,
                $isEligible['data'] ?? array('type' => '')
            );
        }

        return $this->sendResponse([], 'GRV Eligible for reversal');
    }

    public function cancelGRV(Request $request)
    {


        $input = $request->all();

        $employee = Helper::getEmployeeInfo();

        // precheck
        $isEligible = $this->gRVMasterRepository->isGrvEligibleForCancellation($input);
        if ($isEligible['status'] == 0) {
            $errorMsg = (isset($isEligible['msg']) && $isEligible['msg'] != '') ? $isEligible['msg'] : 'GRV Not Eligible for cancellation';
            return $this->sendError($errorMsg, 500);
        }
        DB::beginTransaction();
        try {
            // update grv master
            $grv = GRVMaster::find($input['grvAutoID']);
            $grv->grvCancelledYN = -1;
            $grv->grvCancelledBySystemID = $employee->employeeSystemID;
            $grv->grvCancelledBy = $employee->empID;
            $grv->grvCancelledByName = $employee->empName;
            $grv->grvCancelledDate = now();
            $grv->grvCancelledComment = $input['grvCancelledComment'];
            $grv->save();

            $this->openGrvRelatedDetailsInPo($input['grvAutoID'], $grv->companySystemID);
            if ($input['cancelMethod'] != 1) {
                $cancelRes = $this->cancelGrvRelatedPo($input['grvAutoID'], $grv->companySystemID, $input['grvCancelledComment']);
                if (!$cancelRes['status']) {
                    DB::rollback();
                    return $this->sendError($cancelRes['message'], 500);
                }
            }

            // update erp_unbilledgrvgroupby
            UnbilledGrvGroupBy::where('grvAutoID', $input['grvAutoID'])->update(['selectedForBooking' => -1, 'fullyBooked' => 2]);

            $generalLedger = GeneralLedger::where(['companySystemID' => $grv->companySystemID, 'documentSystemID' => 3, 'documentSystemCode' => $input['grvAutoID']])->get();
            if (!empty($generalLedger)) {
                foreach ($generalLedger as $gl) {
                    unset($gl['GeneralLedgerID']);
                    $temp = $gl;
                    $temp['documentDate'] = now();
                    $temp['documentYear'] = date("Y");
                    $temp['documentMonth'] = date("n");
                    $temp['documentNarration'] = 'Reversal Entry';
                    $temp['documentTransAmount'] = ($gl['documentTransAmount']) * -1;
                    $temp['documentLocalAmount'] = ($gl['documentLocalAmount']) * -1;
                    $temp['documentRptAmount'] = ($gl['documentRptAmount']) * -1;
                    GeneralLedger::create($temp->toArray());
                }
            }

            AuditTrial::createAuditTrial($grv->documentSystemID,$input['grvAutoID'],$input['grvCancelledComment'],'cancelled');

            // cancelation email
            CancelDocument::sendEmail($input);

            DB::commit();
            return $this->sendResponse($grv, 'GRV successfully canceled');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }

    }

    public function reverseGRV(Request $request)
    {
        $input = $request->all();
        $employee = Helper::getEmployeeInfo();
        $emails = array();

        // precheck
        $isEligible = $this->gRVMasterRepository->isGrvEligibleForCancellation($input, 'reversal');
        if ($isEligible['status'] == 0) {
            $errorMsg = (isset($isEligible['msg']) && $isEligible['msg'] != '') ? $isEligible['msg'] : 'GRV Not Eligible for cancellation';
            return $this->sendError($errorMsg, 500);
        }

        $isExistBSI = PurchaseReturnDetails::where('grvAutoID', $input['grvAutoID'])->exists();
        $masterData = $this->gRVMasterRepository->findWithoutFail($input['grvAutoID']);

        if ($isExistBSI) {
            return $this->sendError("You cannot reverse the GRV. The GRV is already added to Purchase Return", 500);
        }

        $MasterData = GRVMaster::find($input['grvAutoID']);
        $documentAutoId = $input['grvAutoID'];
        $documentSystemID = $MasterData->documentSystemID;

        $checkBalance = GeneralLedgerService::validateDebitCredit($documentSystemID, $documentAutoId);
        if (!$checkBalance['status']) {
            $allowValidateDocumentAmend = false;
        } else {
            $allowValidateDocumentAmend = true;
        }

        if($MasterData->approved == -1 && $allowValidateDocumentAmend){
            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId,$documentSystemID);
            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                    return $this->sendError($validatePendingGlPost['message']);
                }
            }
        }

        ReversalDocument::sendEmail($input);


        DB::beginTransaction();
        try {
            // update grv master
            $grv = GRVMaster::find($input['grvAutoID']);
            $grv->grvConfirmedYN = 0;
            $grv->grvConfirmedByName = null;
            $grv->grvConfirmedByEmpID = null;
            $grv->grvConfirmedByEmpSystemID = null;
            $grv->grvConfirmedDate = null;
            $grv->RollLevForApp_curr = 1;
            $grv->approved = 0;
            $grv->approvedByUserID = null;
            $grv->approvedByUserSystemID = null;
            $grv->approvedDate = null;
            $grv->save();

            // update erp_unbilledgrvgroupby
            UnbilledGrvGroupBy::where('grvAutoID', $input['grvAutoID'])->delete();
            $generalLedger = GeneralLedger::where(['companySystemID' => $grv->companySystemID, 'documentSystemID' => 3, 'documentSystemCode' => $input['grvAutoID']])->delete();
            $itemLedger = ErpItemLedger::where(['companySystemID' => $grv->companySystemID, 'documentSystemID' => 3, 'documentSystemCode' => $input['grvAutoID']])->delete();
            $approvers = DocumentApproved::where(['companySystemID' => $grv->companySystemID, 'documentSystemID' => 3, 'documentSystemCode' => $input['grvAutoID']])->delete();
            $taxLedger = TaxLedgerDetail::where(['companySystemID' => $grv->companySystemID, 'documentSystemID' => 3, 'documentMasterAutoID' => $input['grvAutoID']])->delete();

            BudgetConsumedData::where('documentSystemCode',  $input['grvAutoID'])
                ->where('companySystemID', $grv->companySystemID)
                ->where('documentSystemID', 3)
                ->delete();
            //deleting records from tax ledger
            $deleteTaxLedgerData = TaxLedger::where('documentMasterAutoID', $input['grvAutoID'])
                ->where('companySystemID', $grv->companySystemID)
                ->where('documentSystemID', 3)
                ->delete();


            AuditTrial::createAuditTrial($grv->documentSystemID,$input['grvAutoID'],$input['grvReversalComment'],'reversed');

            $grvAutoID = $input['grvAutoID'];

            $grvMasterData = GRVMaster::find($grvAutoID);
            if (empty($grvMasterData)) {
                return $this->sendError('Good receipt voucher not found');
            }

            $grvMasterDataArray = $grvMasterData->toArray();


            $storeGoodReceiptHistory = GrvMasterRefferedback::create($grvMasterDataArray);


            $fetchGoodReceiptDetails = GRVDetails::where('grvAutoID', $grvAutoID)
                ->get();

            if (!empty($fetchGoodReceiptDetails)) {
                foreach ($fetchGoodReceiptDetails as $bookDetail) {
                    $bookDetail['timesReferred'] = $grvMasterData->timesReferred;
                }
            }
            foreach ($fetchGoodReceiptDetails as $bookDetail) {
                $bookDetail['grvRefferedBackID'] = $storeGoodReceiptHistory->grvRefferedBackID;
            }


            $GoodReceiptVoucherDetailArray = $fetchGoodReceiptDetails->toArray();

            $storeGoodReceiptVoucherDetailHistory = GrvDetailsRefferedback::insert($GoodReceiptVoucherDetailArray);

            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $grvAutoID)
                ->where('companySystemID', $grvMasterData->companySystemID)
                ->where('documentSystemID', $grvMasterData->documentSystemID)
                ->get();

            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $grvMasterData->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();


            $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

            // cancelation email
            // CancelDocument::sendEmail($input);

            DB::commit();
            return $this->sendResponse($grv, 'GRV successfully reversed');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }

    }

    public function openGrvRelatedDetailsInPo($grvAutoID, $companySystemID)
    {
        $grvDetailsData = GRVDetails::where('grvAutoID', $grvAutoID)->get();

        foreach ($grvDetailsData as $key => $value) {
            $this->updatePoDetailForGrvCancel($value, $companySystemID);
        }

        return true;
    }

     public function cancelGrvRelatedPo($grvAutoID, $companySystemID, $grvCancelledComment)
    {
        $grvDetailsData = GRVDetails::where('grvAutoID', $grvAutoID)->get();
        $poIds = [];
        foreach ($grvDetailsData as $key => $value) {
            $purchaseOrderDetailsID = $value->purchaseOrderDetailsID;
            $purchaseOrderMastertID = $value->purchaseOrderMastertID;

            if (!empty($purchaseOrderMastertID)) {
                $checkAnyGrvExists = GRVDetails::where('purchaseOrderMastertID', $purchaseOrderMastertID)
                                               ->where('grvAutoID', '!=', $grvAutoID)
                                               ->whereHas('grv_master', function($query) {
                                                    $query->where('grvCancelledYN', '!=', -1);
                                                 })
                                               ->first();

                if ($checkAnyGrvExists) {
                    return ['status' => false, 'message' => "Order cannot be cancelled as there is another GRV created."];
                }

                // $poData = ProcumentOrder::find($purchaseOrderMastertID);

                // if ($poData->grvRecieved != 2) {
                //     return ['status' => false, 'message' => "Order cannot be cancelled as the order partially pulled."];
                // }

                $poIds[] = $purchaseOrderMastertID;
            }
        }

        foreach ($poIds as $key => $value) {
            $res = $this->procumentOrderCancel($value, $grvCancelledComment);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']];
            }
        }

        return ['status' => true];

    }

     public function procumentOrderCancel($purchaseOrderID, $grvCancelledComment)
    {
        $employee = \Helper::getEmployeeInfo();

        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);

        if (empty($purchaseOrder)) {
            return ['status' => false, 'message' => "Purchase Order not found."];
        }

        $update = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->update([
                'poCancelledYN' => -1,
                'poCancelledBySystemID' => $employee->employeeSystemID,
                'poCancelledBy' => $employee->empID,
                'poCancelledByName' => $employee->empName,
                'poCancelledDate' => now(),
                'cancelledComments' => $grvCancelledComment
            ]);

        $idsDeleted = array();
        if ($purchaseOrder->approved == -1) {

            $budgetDetail = BudgetConsumedData::where('companySystemID', $purchaseOrder->companySystemID)
                ->where('documentSystemCode', $purchaseOrderID)
                ->where('documentSystemID', $purchaseOrder->documentSystemID)
                ->get();

            if (!empty($budgetDetail)) {
                foreach ($budgetDetail as $bd) {
                    array_push($idsDeleted, $bd->budgetConsumedDataAutoID);
                }
                BudgetConsumedData::destroy($idsDeleted);
            }

        }

        AuditTrial::createAuditTrial($purchaseOrder->documentSystemID, $purchaseOrderID, $grvCancelledComment, 'cancelled');

        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $purchaseOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseOrder->purchaseOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseOrder->purchaseOrderCode;

        $body = '<p>' . $cancelDocNameBody . ' is cancelled due to below reason.</p><p>Comment : ' . $grvCancelledComment . '</p>';
        $subject = $cancelDocNameSubject . ' is cancelled';

        if ($purchaseOrder->poConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {
            $emails[] = array('empSystemID' => $da->employeeSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return ['status' => false, 'message' => $sendEmail["message"]];
        }

        return ['status' => true];
    }

    public function updatePoDetailForGrvCancel($gRVDetails, $companySystemID)
    {
        $purchaseOrderDetailsID = $gRVDetails->purchaseOrderDetailsID;
        $purchaseOrderMastertID = $gRVDetails->purchaseOrderMastertID;

         if (!empty($purchaseOrderDetailsID) && !empty($purchaseOrderMastertID)) {
            $detailExistPODetail = PurchaseOrderDetails::find($purchaseOrderDetailsID);
            // get the total received qty for a specific item
            $detailPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                     ->whereHas('grv_master', function($query) {
                                        $query->where('grvCancelledYN', '!=', -1);
                                     })
                                      ->WHERE('purchaseOrderMastertID', $purchaseOrderMastertID)
                                      ->WHERE('companySystemID', $companySystemID)
                                      ->WHERE('purchaseOrderDetailsID', $purchaseOrderDetailsID)
                                      ->first();
            // get the total received qty
            $masterPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                    ->whereHas('grv_master', function($query) {
                                        $query->where('grvCancelledYN', '!=', -1);
                                     })
                                     ->WHERE('purchaseOrderMastertID', $purchaseOrderMastertID)
                                     ->WHERE('companySystemID', $companySystemID)
                                     ->first();

            $receivedQty = 0;
            $goodsRecievedYN = 0;
            $GRVSelectedYN = 0;
            if ($detailPOSUM->newNoQty > 0) {
                $receivedQty = $detailPOSUM->newNoQty;
            }

            $checkQuantity = $detailExistPODetail->noQty - $receivedQty;
            if ($receivedQty == 0) {
                $goodsRecievedYN = 0;
                $GRVSelectedYN = 0;
            } else {
                if ($checkQuantity == 0) {
                    $goodsRecievedYN = 2;
                    $GRVSelectedYN = 1;
                } else {
                    $goodsRecievedYN = 1;
                    $GRVSelectedYN = 0;
                }
            }

            $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

            if ($masterPOSUM->newNoQty > 0) {
                $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                    ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
            } else {
                $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                    ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
            }
        } 
    }

    public function grvMarkupfinalyze(Request $request){
        $input = $request->all();
        $grvMaster = GRVMaster::find($input['grvAutoID']);

        if (empty($grvMaster)) {
            return $this->sendError('GRV not found');
        }
        if ($grvMaster->isMarkupUpdated==1) {
            return $this->sendError('GRV markup update process restricted',500);
        }
        $grv = $this->gRVMasterRepository->update(['isMarkupUpdated'=>1], $input['grvAutoID']);

        return $this->sendResponse($grv, 'GRV markup updated successfully');
    }

    public function getSupplierDetails($supplierId){
        return SupplierMaster::select('supEmail')
            ->where('supplierCodeSystem', $supplierId)
            ->first();
    }

    public function getDeliveryEvaluationTemplates(Request $request)
    {
        $companyId = $request['companyId'];
        $deliveryEvaluations = SupplierEvaluationTemplate::where('companySystemID',$companyId)
                            ->WHERE('template_type', 1)
                            ->WHERE('is_active', 1)
                            ->WHERE('is_confirmed', 1)
                            ->get();
        return $this->sendResponse($deliveryEvaluations, 'Record retrieved successfully');

    }

}
