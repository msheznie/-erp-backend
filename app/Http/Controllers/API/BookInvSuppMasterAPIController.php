<?php
/**
 * =============================================
 * -- File Name : BookInvSuppMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getInvoiceMasterRecord(),
 * -- Date: 24-August 2018 By: Nazir Description: Added new function getInvoiceMasterView(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBookInvSuppMasterAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppMasterAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\Months;
use App\Models\SupplierAssigned;
use App\Models\Company;
use App\Models\SupplierCurrency;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BookInvSuppMasterRepository;
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
 * Class BookInvSuppMasterController
 * @package App\Http\Controllers\API
 */
class BookInvSuppMasterAPIController extends AppBaseController
{
    /** @var  BookInvSuppMasterRepository */
    private $bookInvSuppMasterRepository;
    private $userRepository;

    public function __construct(BookInvSuppMasterRepository $bookInvSuppMasterRepo, UserRepository $userRepo)
    {
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasters",
     *      summary="Get a listing of the BookInvSuppMasters.",
     *      tags={"BookInvSuppMaster"},
     *      description="Get all BookInvSuppMasters",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppMaster")
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
        $this->bookInvSuppMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->all();

        return $this->sendResponse($bookInvSuppMasters->toArray(), 'Book Inv Supp Masters retrieved successfully');
    }

    /**
     * @param CreateBookInvSuppMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppMasters",
     *      summary="Store a newly created BookInvSuppMaster in storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Store BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMaster")
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
     *                  ref="#/definitions/BookInvSuppMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBookInvSuppMasterAPIRequest $request)
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
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);

        if (isset($input['bookingDate'])) {
            if ($input['bookingDate']) {
                $input['bookingDate'] = new Carbon($input['bookingDate']);
            }
        }

        if (isset($input['supplierInvoiceDate'])) {
            if ($input['supplierInvoiceDate']) {
                $input['supplierInvoiceDate'] = new Carbon($input['supplierInvoiceDate']);
            }
        }

        $documentDate = $input['bookingDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the financial period!');
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['documentSystemID'] = '11';
        $input['documentID'] = 'SI';

        $lastSerial = BookInvSuppMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('bookingSuppMasInvAutoID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
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

        $input['serialNo'] = $lastSerialNumber;
        $input['supplierTransactionCurrencyER'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];

            $input['FYBiggin'] = $companyfinanceyear->bigginingDate;
            $input['FYEnd'] = $companyfinanceyear->endingDate;
        } else {
            $finYear = date("Y");
        }

        if ($documentMaster) {
            $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['bookingInvCode'] = $bookingInvCode;
        }

        // adding supplier grv details
        $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount')
            ->where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($supplierAssignedDetail) {
            $input['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
            $input['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
            $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
            $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
        }

        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->create($input);

        return $this->sendResponse($bookInvSuppMasters->toArray(), 'Supplier Invoice created successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Display the specified BookInvSuppMaster",
     *      tags={"BookInvSuppMaster"},
     *      description="Get BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
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
     *                  ref="#/definitions/BookInvSuppMaster"
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
        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
        }

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Book Inv Supp Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBookInvSuppMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Update the specified BookInvSuppMaster in storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Update BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMaster")
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
     *                  ref="#/definitions/BookInvSuppMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
        }

        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->update($input, $id);

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'BookInvSuppMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Remove the specified BookInvSuppMaster from storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Delete BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
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
        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
        }

        $bookInvSuppMaster->delete();

        return $this->sendResponse($id, 'Book Inv Supp Master deleted successfully');
    }

    public function getInvoiceMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $input['bookingSuppMasInvAutoID'])->with(['grvdetail' => function ($query) {
            $query->with('grv');
        }, 'directdetail' => function ($query) {
            $query->with('segment');
        }, 'detail' => function ($query) {
            $query->with('grv');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 11);
        }, 'company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier', 'directdetail', 'suppliergrv', 'confirmed_by'])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function getInvoiceMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = BookInvSuppMaster::select(DB::raw("YEAR(createdDateTime) as year"))
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

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && ($request['type'] == 'add' || $request['type'] == 'edit')) {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
            $companyFinanceYear = $companyFinanceYear->where('isCurrent', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'financialYears' => $financialYears,
            'suppliers' => $supplier,
            'companyFinanceYear' => $companyFinanceYear
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getInvoiceMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $invMaster = BookInvSuppMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->where('documentSystemID', $input['documentSystemID']);
        $invMaster->with(['created_by' => function ($query) {
        }, 'supplier' => function ($query) {
        }, 'transactioncurrency' => function ($query) {
        }]);

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $invMaster->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('bookingDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('bookingDate', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $invMaster->where('supplierID', $input['supplierID']);
            }
        }

        $invMaster = $invMaster->select(
            ['erp_bookinvsuppmaster.bookingSuppMasInvAutoID',
                'erp_bookinvsuppmaster.bookingInvCode',
                'erp_bookinvsuppmaster.documentSystemID',
                'erp_bookinvsuppmaster.supplierInvoiceNo',
                'erp_bookinvsuppmaster.secondaryRefNo',
                'erp_bookinvsuppmaster.createdDateTime',
                'erp_bookinvsuppmaster.createdUserSystemID',
                'erp_bookinvsuppmaster.comments',
                'erp_bookinvsuppmaster.bookingDate',
                'erp_bookinvsuppmaster.supplierID',
                'erp_bookinvsuppmaster.confirmedDate',
                'erp_bookinvsuppmaster.approvedDate',
                'erp_bookinvsuppmaster.supplierTransactionCurrencyID',
                'erp_bookinvsuppmaster.bookingAmountTrans',
                'erp_bookinvsuppmaster.cancelYN',
                'erp_bookinvsuppmaster.timesReferred',
                'erp_bookinvsuppmaster.confirmedYN',
                'erp_bookinvsuppmaster.approved'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bookingSuppMasInvAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
