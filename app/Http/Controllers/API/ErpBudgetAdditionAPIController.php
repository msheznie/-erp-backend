<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateErpBudgetAdditionAPIRequest;
use App\Http\Requests\API\UpdateErpBudgetAdditionAPIRequest;
use App\Models\BudgetMaster;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\ErpBudgetAddition;
use App\Models\Months;
use App\Models\ReportTemplate;
use App\Models\SegmentMaster;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ErpBudgetAdditionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class ErpBudgetAdditionController
 *
 * @package App\Http\Controllers\API
 */
class ErpBudgetAdditionAPIController extends AppBaseController
{
    /** @var  ErpBudgetAdditionRepository */
    private $erpBudgetAdditionRepository;

    public function __construct(ErpBudgetAdditionRepository $erpBudgetAdditionRepo)
    {
        $this->erpBudgetAdditionRepository = $erpBudgetAdditionRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditions",
     *      summary="Get a listing of the ErpBudgetAdditions.",
     *      tags={"ErpBudgetAddition"},
     *      description="Get all ErpBudgetAdditions",
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
     *                  @SWG\Items(ref="#/definitions/ErpBudgetAddition")
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
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approvedYN', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $budgetTransfer = $this->erpBudgetAdditionRepository->budgetAdditionFormListQuery($request, $input, $search);

        return \DataTables::of($budgetTransfer)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * @param CreateErpBudgetAdditionAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpBudgetAdditions",
     *      summary="Store a newly created ErpBudgetAddition in storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Store ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAddition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAddition")
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
     *                  ref="#/definitions/ErpBudgetAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpBudgetAdditionAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdDate'] = now();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        $validator = \Validator::make($input, [
            'year' => 'required|numeric|min:1',
            'comments' => 'required',
            'templatesMasterAutoID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentSystemID'] = 102;
        $input['documentID'] = 'BDA';

        $lastSerial = ErpBudgetAddition::where('companySystemID', $input['companySystemID'])
            ->orderBy('id', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }

        $input['companyID'] = $company->CompanyID;
        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['additionVoucherNo'] = $code;
        }

        $budgetTransferForms = $this->erpBudgetAdditionRepository->create($input);

        return $this->sendResponse($budgetTransferForms->toArray(), 'Budget Addition Form saved successfully');
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Display the specified ErpBudgetAddition",
     *      tags={"ErpBudgetAddition"},
     *      description="Get ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
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
     *                  ref="#/definitions/ErpBudgetAddition"
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
        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->fetchBudgetData($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        return $this->sendResponse($erpBudgetAddition, 'Erp Budget Addition retrieved successfully');
    }

    /**
     * @param int                               $id
     * @param UpdateErpBudgetAdditionAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Update the specified ErpBudgetAddition in storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Update ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAddition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAddition")
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
     *                  ref="#/definitions/ErpBudgetAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpBudgetAdditionAPIRequest $request)
    {

        $input = $request->only(['confirmedYN']);
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN'));
        $input['comments'] = $request->get('comments');

        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        if ($erpBudgetAddition->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $params = array('autoID' => $id,
                'company' => $erpBudgetAddition->companySystemID,
                'document' => $erpBudgetAddition->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }


        $erpBudgetAddition = $this->erpBudgetAdditionRepository->update($input, $id);

        return $this->sendResponse($erpBudgetAddition->toArray(), 'Budget Addition updated successfully');
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Remove the specified ErpBudgetAddition from storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Delete ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
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
        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        $erpBudgetAddition->delete();

        return $this->sendSuccess('Erp Budget Addition deleted successfully');
    }

    public function getBudgetAdditionFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = Year::orderBy('year', 'desc')->get();

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->where('isActive', 1)->get();

        if (count($companyFinanceYear) > 0) {
            $startYear = $companyFinanceYear[0]['financeYear'];
            $finYearExp = explode('/', (explode('|', $startYear))[0]);
            $financeYear = (int)$finYearExp[2];
        } else {
            $financeYear = date("Y");
        }


        $budgetMasters = BudgetMaster::where([
                                            'companySystemID' => $companyId,
                                            'Year' => $financeYear
                                        ])->groupBy('templateMasterID')
                                        ->get();

        $templateIds = collect($budgetMasters)->pluck('templateMasterID')->toArray();

        $masterTemplates = ReportTemplate::whereIn('companyReportTemplateID', $templateIds)->get();

        $output = [
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments,
            'masterTemplates' => $masterTemplates,
            'financeYear' => $financeYear
        ];

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getTemplatesDetailsByBudgetAddition(Request $request)
    {

        $id = $request->get('id');

        $budgetAdditionMaster = ErpBudgetAddition::find($id);

        if (empty($budgetAdditionMaster)) {
            return $this->sendError('Budget Addition not found');
        }

        $template_details = ReportTemplate::find($budgetAdditionMaster->templatesMasterAutoID)
            ->details()->where([
                'isFinalLevel' => 1,
                'companySystemID' => $budgetAdditionMaster->companySystemID
            ])->get();
        if (empty($template_details)) {
            return $this->sendError('Templates not found');
        }

        return $this->sendResponse($template_details, 'Templates Details retrieved successfully');
    }

    public function getBudgetAdditionApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $debitNotes = DB::table('erp_documentapproved')
            ->select(
                'erp_budgetaddition.*',
                'employees.empName As confirmed_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [102])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_budgetaddition', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_budgetaddition.companySystemID', $companyId)
                    ->where('erp_budgetaddition.approvedYN', 0)
                    ->where('erp_budgetaddition.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'confirmedByEmpSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [102])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $debitNotes = $debitNotes->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('additionVoucherNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $debitNotes = [];
        }

        return \DataTables::of($debitNotes)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBudgetAdditionApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $debitNotes = DB::table('erp_documentapproved')
            ->select(
                'erp_budgetaddition.*',
                'employees.empName As confirmed_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_budgetaddition', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->where('erp_budgetaddition.companySystemID', $companyId)
                    ->where('erp_budgetaddition.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'confirmedByEmpSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [102])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $debitNotes = $debitNotes->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('additionVoucherNo', 'LIKE', "%$search%")
                    ->orWhere('comments', 'like', "%$search%");
            });
        }

        return \DataTables::of($debitNotes)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
