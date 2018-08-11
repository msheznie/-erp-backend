<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Return
 * -- Author : Mohamed Fayas
 * -- Create date : 31 - July 2018
 * -- Description : This file contains the all CRUD for Purchase Return
 * -- REVISION HISTORY
 * -- Date: 10 - August 2018 By: Fayas Description: Added new functions named as getPurchaseReturnByCompany(),getPurchaseReturnFormData()
 * -- Date: 10 - August 2018 By: Fayas Description: Added new functions named as purchaseReturnSegmentChkActive(),grvForPurchaseReturn()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnAPIRequest;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\GRVTypes;
use App\Models\Location;
use App\Models\Months;
use App\Models\PurchaseReturn;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\WarehouseMaster;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PurchaseReturnRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseReturnController
 * @package App\Http\Controllers\API
 */

class PurchaseReturnAPIController extends AppBaseController
{
    /** @var  PurchaseReturnRepository */
    private $purchaseReturnRepository;

    public function __construct(PurchaseReturnRepository $purchaseReturnRepo)
    {
        $this->purchaseReturnRepository = $purchaseReturnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturns",
     *      summary="Get a listing of the PurchaseReturns.",
     *      tags={"PurchaseReturn"},
     *      description="Get all PurchaseReturns",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturn")
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
        $this->purchaseReturnRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturns = $this->purchaseReturnRepository->all();

        return $this->sendResponse($purchaseReturns->toArray(), 'Purchase Returns retrieved successfully');
    }

    /**
     * @param CreatePurchaseReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturns",
     *      summary="Store a newly created PurchaseReturn in storage",
     *      tags={"PurchaseReturn"},
     *      description="Store PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturn")
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
     *                  ref="#/definitions/PurchaseReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnAPIRequest $request)
    {
        /*
        'totalSupplierDefaultAmount',
        'totalSupplierTransactionAmount',
        'totalLocalAmount',
        'totalComRptAmount',
         */

        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['documentSystemID'] = 24;
        $input['documentID'] = 'PRN';

        $companyFinancePeriod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        if ($companyFinancePeriod) {
            $input['FYBiggin'] = $companyFinancePeriod->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod->dateTo;
        }

        if (isset($input['purchaseReturnDate'])) {
            if ($input['purchaseReturnDate']) {
                $input['purchaseReturnDate'] = new Carbon($input['purchaseReturnDate']);
            }
        }

        $documentDate = $input['purchaseReturnDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Purchase Return Date not between Financial period !',500);
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
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $lastSerial = PurchaseReturn::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('purhaseReturnAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }


        $input['serialNo'] = $lastSerialNumber;
        $input['supplierTransactionER'] = 1;

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
        if ($supplier) {
            $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
            $input['supplierName'] = $supplier->supplierName;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyFinanceYear) {
            $startYear = $companyFinanceYear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }
        if ($documentMaster) {
            $grvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['purchaseReturnCode'] = $grvCode;
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

        $purchaseReturns = $this->purchaseReturnRepository->create($input);

        return $this->sendResponse($purchaseReturns->toArray(), 'Purchase Return saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturns/{id}",
     *      summary="Display the specified PurchaseReturn",
     *      tags={"PurchaseReturn"},
     *      description="Get PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturn",
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
     *                  ref="#/definitions/PurchaseReturn"
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
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->with(['segment_by','location_by','financeperiod_by'])->findWithoutFail($id);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }

        return $this->sendResponse($purchaseReturn->toArray(), 'Purchase Return retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturns/{id}",
     *      summary="Update the specified PurchaseReturn in storage",
     *      tags={"PurchaseReturn"},
     *      description="Update PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturn")
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
     *                  ref="#/definitions/PurchaseReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($id);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }

        $purchaseReturn = $this->purchaseReturnRepository->update($input, $id);

        return $this->sendResponse($purchaseReturn->toArray(), 'PurchaseReturn updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturns/{id}",
     *      summary="Remove the specified PurchaseReturn from storage",
     *      tags={"PurchaseReturn"},
     *      description="Delete PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturn",
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
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($id);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }

        $purchaseReturn->delete();

        return $this->sendResponse($id, 'Purchase Return deleted successfully');
    }

    public function getPurchaseReturnByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID',
                     'purchaseReturnLocation', 'confirmedYN', 'approved', 'month', 'year'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $purchaseReturn = PurchaseReturn::where('companySystemID', $input['companyId'])
                                          ->where('documentSystemID', $input['documentId'])
                                          ->with(['created_by', 'segment_by', 'location_by','supplier_by','currency_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturn->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('purchaseReturnLocation', $input)) {
            if ($input['purchaseReturnLocation'] && !is_null($input['purchaseReturnLocation'])) {
                $purchaseReturn->where('purchaseReturnLocation', $input['purchaseReturnLocation']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $purchaseReturn->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $purchaseReturn->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseReturn->whereMonth('purchaseReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseReturn->whereYear('purchaseReturnDate', '=', $input['year']);
            }
        }

        $purchaseReturn = $purchaseReturn->select(
            ['purhaseReturnAutoID',
                'purchaseReturnCode',
                'documentSystemID',
                'purchaseReturnRefNo',
                'createdDateTime',
                'createdUserSystemID',
                'narration',
                'purchaseReturnLocation',
                'purchaseReturnDate',
                'supplierID',
                'serviceLineSystemID',
                'confirmedDate',
                'approvedDate',
                'supplierTransactionCurrencyID',
                'timesReferred',
                'confirmedYN',
                'approved'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturn = $purchaseReturn->where(function ($query) use ($search) {
                $query->where('purchaseReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }


        $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $input['companyId'])->first();

        $policy = 0;

        if (!empty($historyPolicy)) {
            $policy = $historyPolicy->isYesNO;
        }

        return \DataTables::eloquent($purchaseReturn)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purhaseReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPurchaseReturnFormData(Request $request)
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

        $years = Year::orderBy('year','desc')->get();

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

    public function purchaseReturnSegmentChkActive(Request $request)
    {

        $input = $request->all();

        $purchaseReturnAutoID = $input['purchaseReturnAutoID'];

        $purchaseReturn = PurchaseReturn::find($purchaseReturnAutoID);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $purchaseReturn->serviceLineSystemID)
                                  ->where('companySystemID', $purchaseReturn->companySystemID)
                                  ->where('isActive', 1)
                                  ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        return $this->sendResponse($purchaseReturn, 'sucess');
    }

    public function grvForPurchaseReturn(Request $request)
    {

        $input = $request->all();

        $purchaseReturnAutoID = $input['purchaseReturnAutoID'];

        $purchaseReturn = PurchaseReturn::find($purchaseReturnAutoID);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }

        $grv = GRVMaster::where('companySystemID',$purchaseReturn->companySystemID)
                        ->where('serviceLineSystemID',$purchaseReturn->serviceLineSystemID)
                        ->where('grvLocation',$purchaseReturn->purchaseReturnLocation)
                        ->where('approved',-1)
                        ->where('supplierID',$purchaseReturn->supplierID)
                        ->get();

        return $this->sendResponse($grv, 'success');
    }

    public function grvDetailByMasterForPurchaseReturn(Request $request)
    {

        $input = $request->all();

        $grvAutoID = $input['grvAutoID'];

        $grvMaster = GRVMaster::find($grvAutoID);

        if (empty($grvMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        $grvDetails = GRVDetails::where('grvAutoID',$grvMaster->grvAutoID)->with(['unit'])->get();

        return $this->sendResponse($grvDetails, 'success');
    }

}
