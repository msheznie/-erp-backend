<?php
/**
 * =============================================
 * -- File Name : ConsoleJVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General Ledger
 * -- Author : Mohamed Mubashir
 * -- Create date : 06 - March 2019
 * -- Description : This file contains the all CRUD for Console JV
 * -- REVISION HISTORY
 * -- Date: 06 - March 2019 By: Mubashir Description: Added new functions named as getAllConsoleJV()
 * -- Date: 07 - March 2019 By: Mubashir Description: Added new functions named as getConsoleJVMasterFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateConsoleJVMasterAPIRequest;
use App\Http\Requests\API\UpdateConsoleJVMasterAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ConsoleJVDetail;
use App\Models\ConsoleJVMaster;
use App\Models\DocumentApproved;
use App\Models\EmployeesDepartment;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\JvMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ConsoleJVMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\AuditTrial;

/**
 * Class ConsoleJVMasterController
 * @package App\Http\Controllers\API
 */

class ConsoleJVMasterAPIController extends AppBaseController
{
    /** @var  ConsoleJVMasterRepository */
    private $consoleJVMasterRepository;

    public function __construct(ConsoleJVMasterRepository $consoleJVMasterRepo)
    {
        $this->consoleJVMasterRepository = $consoleJVMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/consoleJVMasters",
     *      summary="Get a listing of the ConsoleJVMasters.",
     *      tags={"ConsoleJVMaster"},
     *      description="Get all ConsoleJVMasters",
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
     *                  @SWG\Items(ref="#/definitions/ConsoleJVMaster")
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
        $this->consoleJVMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->consoleJVMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $consoleJVMasters = $this->consoleJVMasterRepository->all();

        return $this->sendResponse($consoleJVMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param CreateConsoleJVMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/consoleJVMasters",
     *      summary="Store a newly created ConsoleJVMaster in storage",
     *      tags={"ConsoleJVMaster"},
     *      description="Store ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ConsoleJVMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ConsoleJVMaster")
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
     *                  ref="#/definitions/ConsoleJVMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateConsoleJVMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'consoleJVdate' => 'required|date',
            'currencyID' => 'required',
            'consoleJVNarration' => 'required',
            'jvType' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['consoleJVdate'] = new Carbon($input['consoleJVdate']);
        $input['currencyER'] = 1;

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $lastSerial = ConsoleJVMaster::orderBy('serialNo', 'desc')->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($documentMaster) {
            $documentCode = ($company->CompanyID . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['consoleJVcode'] = $documentCode;
        }
        $input['serialNo'] = $lastSerialNumber;

        $companyCurrency = \Helper::companyCurrency($input['companySystemID']);
        if ($companyCurrency) {
            $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $input['rptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['currencyID'], $input['currencyID'], 0);
            if ($companyCurrencyConversion) {
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            }
        }

        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdPcID'] = gethostname();

        $consoleJVMasters = $this->consoleJVMasterRepository->create($input);

        return $this->sendResponse($consoleJVMasters->toArray(), trans('custom.save', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/consoleJVMasters/{id}",
     *      summary="Display the specified ConsoleJVMaster",
     *      tags={"ConsoleJVMaster"},
     *      description="Get ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVMaster",
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
     *                  ref="#/definitions/ConsoleJVMaster"
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
        /** @var ConsoleJVMaster $consoleJVMaster */
        $consoleJVMaster = $this->consoleJVMasterRepository->with(['confirmed_by'])->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_masters')]));
        }

        return $this->sendResponse($consoleJVMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.console_j_v_masters')]));
    }

    /**
     * @param int $id
     * @param UpdateConsoleJVMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/consoleJVMasters/{id}",
     *      summary="Update the specified ConsoleJVMaster in storage",
     *      tags={"ConsoleJVMaster"},
     *      description="Update ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ConsoleJVMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ConsoleJVMaster")
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
     *                  ref="#/definitions/ConsoleJVMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateConsoleJVMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'consoleJVdate' => 'required|date',
            'currencyID' => 'required',
            'consoleJVNarration' => 'required',
            'jvType' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var ConsoleJVMaster $consoleJVMaster */
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_masters')]));
        }

        $input['consoleJVdate'] = new Carbon($input['consoleJVdate']);

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $companyCurrency = \Helper::companyCurrency($input['companySystemID']);
        if ($companyCurrency) {
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['currencyID'], $input['currencyID'], 0);
            if ($companyCurrencyConversion) {
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            }
        }

        if ($consoleJVMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'consoleJVdate' => 'required',
                'consoleJVNarration' => 'required',
                'jvType' => 'required|not_in:0',
                'currencyID' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $finalError = array(
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
                'required_glCode' => array(),
                'active_glCode' => array(),
            );

            $error_count = 0;

            $consoleJVDetail = ConsoleJVDetail::ofMaster($id)->get();
            foreach ($consoleJVDetail as $item) {
                if ($item->serviceLineSystemID && !is_null($item->serviceLineSystemID)) {
                    $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $item->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActive)) {
                        $item->serviceLineSystemID = null;
                        $item->serviceLineCode = null;
                        array_push($finalError['active_serviceLine'], $item->companyID);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $item->companyID);
                    $error_count++;
                }

                if ($item->glAccountSystemID && !is_null($item->glAccountSystemID)) {
                    $checkChartOfAccountActive = ChartOfAccount::where('chartOfAccountSystemID', $item->glAccountSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkChartOfAccountActive)) {
                        $item->glAccountSystemID = null;
                        $item->glAccount = null;
                        $item->glAccountDescription = null;
                        array_push($finalError['active_glCode'], $item->companyID);
                        $error_count++;
                    }
                }
                else if (is_null($item->glAccountSystemID) || $item->glAccountSystemID == 0) {
                    array_push($finalError['required_glCode'], $item->companyID);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError(trans('custom.you_cannot_confirm_this_document'), 500, $confirm_error);
            }

            $jvDetail = ConsoleJVDetail::selectRAW('SUM(debitAmount) as debitAmount,SUM(creditAmount) as creditAmount,SUM(debitAmount) - SUM(creditAmount) as balance')->ofMaster($id)->first();
            if($jvDetail){
                if($jvDetail->balance != 0){
                    return $this->sendError(trans('custom.debit_and_credit_amount_not_matching'),500,['type' => 'confirm']);
                }

                if($jvDetail->debitAmount == 0 && $jvDetail->creditAmount == 0){
                    return $this->sendError(trans('custom.total_debit_and_credit_amount_cannot_be_zero'),500,['type' => 'confirm']);
                }
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemID"],
                'segment' => 0,
                'category' => 0,
                'amount' => $jvDetail->debitAmount
            );

            $confirm = \Helper::confirmDocument($params);

            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedPc'] = gethostname();

        $consoleJVMaster = $this->consoleJVMasterRepository->update($input, $id);

        return $this->sendReponseWithDetails($consoleJVMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.console_j_v_masters')]),1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/consoleJVMasters/{id}",
     *      summary="Remove the specified ConsoleJVMaster from storage",
     *      tags={"ConsoleJVMaster"},
     *      description="Delete ConsoleJVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ConsoleJVMaster",
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
        /** @var ConsoleJVMaster $consoleJVMaster */
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.console_j_v_masters')]));
        }

        $consoleJVMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.console_j_v_masters')]));
    }


    public function getAllConsoleJV(Request $request){
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'confirmedYN'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $consoleJV = $this->consoleJVMasterRepository->consoleJVMasterListQuery($request, $input, $search);

        return \DataTables::eloquent($consoleJV)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('consoleJvMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getConsoleJVGL(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = ChartOfAccount::where('controllAccountYN', 0)
            ->where('isActive', 1)
            ->where('isBank', 0)
            ->where('isApproved', 1);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.data')]));

    }

    public function getConsoleJVMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();
        $month = Months::all();

        $years = ConsoleJVMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $company = Company::where('masterCompanySystemIDReorting',$companyId)
                          ->where('isGroup', 0)
                          ->get();

        $segment = SegmentMaster::where('isActive',1)->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'company' => $company,
            'segments' => $segment,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

     public function getConsoleJvApproval(Request $request)
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
            ->where('documentSystemID', 17)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_consolejvmaster.consoleJvMasterAutoId',
            'erp_consolejvmaster.consoleJVcode',
            'erp_consolejvmaster.documentSystemID',
            'erp_consolejvmaster.consoleJVdate',
            'erp_consolejvmaster.consoleJVNarration',
            'erp_consolejvmaster.createdDateTime',
            'erp_consolejvmaster.confirmedDate',
            'erp_consolejvmaster.jvType',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 69)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('erp_consolejvmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'consoleJvMasterAutoId')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_consolejvmaster.companySystemID', $companyID)
                ->where('erp_consolejvmaster.approved', 0)
                ->where('erp_consolejvmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'erp_consolejvmaster.currencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 69)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('consoleJVcode', 'LIKE', "%{$search}%")
                    ->orWhere('consoleJVNarration', 'LIKE', "%{$search}%");
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
            ->make(true);
    }

    public function getApprovedConsoleJvForCurrentUser(Request $request)
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
            'erp_consolejvmaster.consoleJvMasterAutoId',
            'erp_consolejvmaster.consoleJVcode',
            'erp_consolejvmaster.documentSystemID',
            'erp_consolejvmaster.consoleJVdate',
            'erp_consolejvmaster.consoleJVNarration',
            'erp_consolejvmaster.createdDateTime',
            'erp_consolejvmaster.confirmedDate',
            'erp_consolejvmaster.jvType',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_consolejvmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'consoleJvMasterAutoId')
                ->where('erp_consolejvmaster.companySystemID', $companyID)
                ->where('erp_consolejvmaster.approved', -1)
                ->where('erp_consolejvmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'erp_consolejvmaster.currencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.documentSystemID', 69)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('consoleJVcode', 'LIKE', "%{$search}%")
                    ->orWhere('consoleJVNarration', 'LIKE', "%{$search}%");
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
            ->make(true);
    }

    public function approveConsoleJV(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectConsoleJV(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function consoleJVReopen(Request $request)
    {
        $input = $request->all();

        $consoleJvAutoID = $input['consoleJvAutoID'];

        $jvMasterData = ConsoleJVMaster::find($consoleJvAutoID);
        $emails = array();
        if (empty($jvMasterData)) {
            return $this->sendError(trans('custom.console_jv_not_found'));
        }

        if ($jvMasterData->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_console_journal_voucher_it__2'));
        }

        if ($jvMasterData->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_console_journal_voucher_it_'));
        }

        if ($jvMasterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_console_journal_voucher_it__1'));
        }

        // updating fields

        $jvMasterData->confirmedYN = 0;
        $jvMasterData->confirmedByEmpSystemID = null;
        $jvMasterData->confirmedByEmpID = null;
        $jvMasterData->confirmedByName = null;
        $jvMasterData->confirmedDate = null;
        $jvMasterData->RollLevForApp_curr = 1;
        $jvMasterData->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $jvMasterData->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $jvMasterData->bookingInvCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $jvMasterData->bookingInvCode;

        $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

        $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemCode', $jvMasterData->consoleJvMasterAutoId)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $jvMasterData->companySystemID)
                    ->where('documentSystemID', $jvMasterData->documentSystemID)
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

        DocumentApproved::where('documentSystemCode', $consoleJvAutoID)
            ->where('companySystemID', $jvMasterData->companySystemID)
            ->where('documentSystemID', $jvMasterData->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($jvMasterData->documentSystemID,$consoleJvAutoID,$input['reopenComments'],'Reopened');

        return $this->sendResponse($jvMasterData->toArray(), trans('custom.console_jv_reopened_successfully'));
    }


}
