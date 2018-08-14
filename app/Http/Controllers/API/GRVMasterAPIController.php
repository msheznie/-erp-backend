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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVMasterAPIRequest;
use App\Http\Requests\API\UpdateGRVMasterAPIRequest;
use App\Models\DocumentAttachments;
use App\Models\GRVMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
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

        $documentDate = $input['grvDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('GRV Date not between Financial period !');
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['documentSystemID'] = '3';
        $input['documentID'] = 'GRV';

        $lastSerial = GRVMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('grvAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
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

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
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
        $gRVMaster = $this->gRVMasterRepository->with(['created_by', 'confirmed_by', 'segment_by', 'location_by','financeperiod_by' => function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
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

        $input = array_except($input, ['created_by', 'confirmed_by', 'location_by', 'segment_by','financeperiod_by']);
        $input = $this->convertArrayToValue($input);

        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        if ($gRVMaster->grvCancelledYN == -1) {
            return $this->sendError('Good Receipt Voucher closed. You cannot edit.', 500);
        }

        if (isset($input['grvDate'])) {
            if ($input['grvDate']) {
                $input['grvDate'] = new Carbon($input['grvDate']);
            }
        }

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            if($companyFinancePeriod->isActive != -1 && $companyFinancePeriod->isCurrent != -1){
                return $this->sendError('GRV Date not between Financial period !', 500);
            }
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        } else{
            return $this->sendError('Please select a financial period', 500);
        }

        if (isset($input['stampDate'])) {
            if ($input['stampDate']) {
                $input['stampDate'] = new Carbon($input['stampDate']);
            }
        }

        $documentDate = $input['grvDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('GRV Date not between Financial period !');
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

            //getting total sum of PO detail Amount
            $grvMasterSum = GRVDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
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

            // checking logistic details  exist and updating grv id in erp_purchaseorderadvpayment  table
            $fetchingGRVDetails = GRVDetails::select(DB::raw('purchaseOrderMastertID'))
                ->where('grvAutoID', $input['grvAutoID'])
                ->groupBy('purchaseOrderMastertID')
                ->get();

            if ($fetchingGRVDetails) {
                foreach ($fetchingGRVDetails as $der) {
                    $poMaster = ProcumentOrder::find($der['purchaseOrderMastertID']);
                    if ($poMaster->logisticsAvailable == -1) {
                        $poAdvancePaymentdetail = PoAdvancePayment::where('poID', $der['purchaseOrderMastertID'])->where('isAdvancePaymentYN',1)
                            ->where('grvAutoID',0)->get();
                        if (count($poAdvancePaymentdetail) > 0) {
                            foreach ($poAdvancePaymentdetail as $advance) {
                                if ($advance['grvAutoID'] == 0) {
                                    $updatePoAdvancePaymentdetail = PoAdvancePayment::find($advance->poAdvPaymentID);
                                    $updatePoAdvancePaymentdetail->grvAutoID = $input['grvAutoID'];
                                    $updatePoAdvancePaymentdetail->save();
                                }
                            }
                        } else {
                            $grvCheck = PoAdvancePayment::where('poID', $der['purchaseOrderMastertID'])->where('isAdvancePaymentYN',1)
                                ->where('grvAutoID',$id)->get();
                            if (count($grvCheck) == 0) {
                                return $this->sendError('Added PO ' . $poMaster->purchaseOrderCode . ' has logistics. You can confirm the GRV only after logistics details are updated.');
                            }
                        }
                    }
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

            //updating logistic details in grv details table

            $fetchAllGrvDetails = GRVDetails::where('grvAutoID', $input['grvAutoID'])
                ->get();

            if ($fetchAllGrvDetails) {
                foreach ($fetchAllGrvDetails as $row) {
                    $updateGRVDetail_log_detail = GRVDetails::find($row['grvDetailsID']);

                    $logisticsCharges_TransCur = ((($row['noQty'] * $row['GRVcostPerUnitSupTransCur']) / ($input['grvTotalSupplierTransactionCurrency'])) * $grvTotalLogisticAmount['transactionTotalSum']) / $row['noQty'];

                    $logisticsCharges_LocalCur = ((($row['noQty'] * $row['GRVcostPerUnitLocalCur']) / ($input['grvTotalLocalCurrency'])) * $grvTotalLogisticAmount['localTotalSum']) / $row['noQty'];

                    $logisticsChargest_RptCur = ((($row['noQty'] * $row['GRVcostPerUnitComRptCur']) / ($input['grvTotalComRptCurrency'])) * $grvTotalLogisticAmount['reportingTotalSum']) / $row['noQty'];

                    $updateGRVDetail_log_detail->logisticsCharges_TransCur = \Helper::roundValue($logisticsCharges_TransCur);
                    $updateGRVDetail_log_detail->logisticsCharges_LocalCur = \Helper::roundValue($logisticsCharges_LocalCur);
                    $updateGRVDetail_log_detail->logisticsChargest_RptCur = \Helper::roundValue($logisticsChargest_RptCur);

                    $updateGRVDetail_log_detail->landingCost_TransCur = \Helper::roundValue($logisticsCharges_TransCur) + $row['GRVcostPerUnitSupTransCur'];
                    $updateGRVDetail_log_detail->landingCost_LocalCur = \Helper::roundValue($logisticsCharges_LocalCur) + $row['GRVcostPerUnitLocalCur'];
                    $updateGRVDetail_log_detail->landingCost_RptCur = \Helper::roundValue($logisticsChargest_RptCur) + $row['GRVcostPerUnitComRptCur'];

                    $updateGRVDetail_log_detail->save();
                }
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
                $grvMaster->whereMonth('grvDate', '=', $input['month']);
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

        $grvTypes = GRVTypes::where('grvTypeID',2)->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
            ->where('companySystemID', $companyId)
            ->first();


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
            'companyPolicy' => $allowPartialGRVPolicy
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
            },'details','company_by','currency_by', 'companydocumentattachment_by' => function ($query) {
                $query->where('documentSystemID', 3);
            },'location_by'])->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'GRV retrieved successfully');
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
            'warehousemaster.wareHouseDescription as wareHouseSet'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 3)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
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
            'warehousemaster.wareHouseDescription as wareHouseSet'
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
            },'details','company_by','currency_by', 'companydocumentattachment_by' => function ($query) {
                $query->where('documentSystemID', 3);
            }])->findWithoutFail($id);

        $grv = array(
            'grvData' => $outputRecord
        );

        $html = view('print.good_receipt_voucher_print_pdf', $grv);

        // echo $html;
        //exit();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream();
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
                    ->whereIN('documentSystemID',[2,5,52] )
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

}
