<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCurrencyConversionMasterAPIRequest;
use App\Http\Requests\API\UpdateCurrencyConversionMasterAPIRequest;
use App\Models\CurrencyConversionMaster;
use App\Models\CurrencyConversionDetail;
use App\Models\CurrencyConversion;
use App\Models\CurrencyMaster;
use App\Repositories\CurrencyConversionMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use App\helper\ReopenDocument;

/**
 * Class CurrencyConversionMasterController
 * @package App\Http\Controllers\API
 */

class CurrencyConversionMasterAPIController extends AppBaseController
{
    /** @var  CurrencyConversionMasterRepository */
    private $currencyConversionMasterRepository;

    public function __construct(CurrencyConversionMasterRepository $currencyConversionMasterRepo)
    {
        $this->currencyConversionMasterRepository = $currencyConversionMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyConversionMasters",
     *      summary="Get a listing of the CurrencyConversionMasters.",
     *      tags={"CurrencyConversionMaster"},
     *      description="Get all CurrencyConversionMasters",
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
     *                  @SWG\Items(ref="#/definitions/CurrencyConversionMaster")
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
        $this->currencyConversionMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyConversionMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyConversionMasters = $this->currencyConversionMasterRepository->all();

        return $this->sendResponse($currencyConversionMasters->toArray(), 'Currency Conversion Masters retrieved successfully');
    }

    /**
     * @param CreateCurrencyConversionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/currencyConversionMasters",
     *      summary="Store a newly created CurrencyConversionMaster in storage",
     *      tags={"CurrencyConversionMaster"},
     *      description="Store CurrencyConversionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyConversionMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyConversionMaster")
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
     *                  ref="#/definitions/CurrencyConversionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $checkNotApproved = CurrencyConversionMaster::where('approvedYN', 0)
                                                    ->where('refferedBackYN', 0)
                                                    ->where(function($query) {
                                                        $query->where('confirmedYN', 0)
                                                              ->orWhere('confirmedYN', 1);
                                                    })
                                                    ->first();

        if ($checkNotApproved) {
            return $this->sendError("There is conversion created and still not approved, therefore, you cannot create", 500);
        }


        $lastSerial = CurrencyConversionMaster::orderBy('serialNumber', 'desc')
                                             ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }


        $input['conversionCode'] = $bookingInvCode = 'CURCONV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);

        $input['createdBy'] = \Helper::getEmployeeSystemID();
        $input['serialNumber'] = $lastSerialNumber;


        DB::beginTransaction();
        try {
            $currencyConversionMaster = $this->currencyConversionMasterRepository->create($input);
            if ($currencyConversionMaster) {
                $currencyConversions = CurrencyConversion::all();

                foreach ($currencyConversions as $key => $value) {
                    $insertData = $value->toArray();
                    $insertData['currencyConversioMasterID'] = $currencyConversionMaster->id;

                    CurrencyConversionDetail::create($insertData);
                }
            } else {
                DB::rollback();
                return $this->sendError("Error occured while creating currency conversion", 500);
            }

            DB::commit();
            return $this->sendResponse($currencyConversionMaster->toArray(), 'Currency Conversion Master saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyConversionMasters/{id}",
     *      summary="Display the specified CurrencyConversionMaster",
     *      tags={"CurrencyConversionMaster"},
     *      description="Get CurrencyConversionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionMaster",
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
     *                  ref="#/definitions/CurrencyConversionMaster"
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
        /** @var CurrencyConversionMaster $currencyConversionMaster */
        $currencyConversionMaster = $this->currencyConversionMasterRepository->findWithoutFail($id);

        if (empty($currencyConversionMaster)) {
            return $this->sendError('Currency Conversion Master not found');
        }

        return $this->sendResponse($currencyConversionMaster->toArray(), 'Currency Conversion Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCurrencyConversionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/currencyConversionMasters/{id}",
     *      summary="Update the specified CurrencyConversionMaster in storage",
     *      tags={"CurrencyConversionMaster"},
     *      description="Update CurrencyConversionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyConversionMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyConversionMaster")
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
     *                  ref="#/definitions/CurrencyConversionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        /** @var CurrencyConversionMaster $currencyConversionMaster */
        $currencyConversionMaster = $this->currencyConversionMasterRepository->findWithoutFail($id);

        if (empty($currencyConversionMaster)) {
            return $this->sendError('Currency Conversion Master not found');
        }


        if ($input['confirmedYN'] == 1 && $currencyConversionMaster->confirmedYN == 0) {

            $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => 96);
            $confirm = \Helper::confirmDocument($params);
            if(!$confirm["success"]){
                return $this->sendError($confirm["message"]);
            }
        }

        $updateData = [
            'description' => $input['description']
        ];

        $currencyConversionMaster = $this->currencyConversionMasterRepository->update($updateData, $id);

        return $this->sendReponseWithDetails($currencyConversionMaster->toArray(), 'CurrencyConversionMaster updated successfully',1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/currencyConversionMasters/{id}",
     *      summary="Remove the specified CurrencyConversionMaster from storage",
     *      tags={"CurrencyConversionMaster"},
     *      description="Delete CurrencyConversionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionMaster",
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
        /** @var CurrencyConversionMaster $currencyConversionMaster */
        $currencyConversionMaster = $this->currencyConversionMasterRepository->findWithoutFail($id);

        if (empty($currencyConversionMaster)) {
            return $this->sendError('Currency Conversion Master not found');
        }

        $currencyConversionMaster->delete();

        return $this->sendSuccess('Currency Conversion Master deleted successfully');
    }

    public function getAllCurrencyConversions(Request $request)
    {
        $input = $request->all();


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $conversions = CurrencyConversionMaster::with(['created_by']);


        $search = $request->input('search.value');
        if ($search) {
            $conversions = $conversions->where(function ($query) use ($search) {
                $query->where('conversionCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('created_by', function ($query) use ($search) {
                        $query->where('empName', 'LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($conversions)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }


    public function getConversionMaster(Request $request)
    {
        $input = $request->all();

        $conversions = CurrencyConversionMaster::with(['confirmed_by'])
                                               ->where('id', $input['id'])
                                               ->first();

        return $this->sendResponse($conversions, 'Currency Conversion Master retrieved successfully');
    }


    public function getAllTempConversionByCurrency(Request $request)
    {

        $id = isset($request['id']) ? $request['id'] : 0;
        $currencyConversionMasterID = isset($request['currencyConversionMasterID']) ? $request['currencyConversionMasterID'] : 0;
        $currencyMaster = CurrencyMaster::find($id);
        $reportingId = 0;
        if (empty($currencyMaster)) {
            return $this->sendError('Currency Master not found');
        }

        $currencyConversionMaster = CurrencyConversionMaster::find($currencyConversionMasterID);
        if (empty($currencyConversionMaster)) {
            return $this->sendError('Currency Conversion Master not found');
        }

        $employee = Helper::getEmployeeInfo();
        if(!empty($employee)){
            $company = Helper::companyCurrency($employee->empCompanySystemID);
            if(!empty($company)){
                $reportingId =  $company->reportingCurrency;
            }
        }

        $conversions = CurrencyConversionDetail::with(['sub_currency'])->where('masterCurrencyID', $id)->where('currencyConversioMasterID', $currencyConversionMasterID)->get();
        $array = array(
            'reportingCurrency' => $reportingId,
            'conversions' => $conversions->toArray()
        );
        return $this->sendResponse($array, 'Currency conversions retrieved successfully');
    }

    public function currencyConversionReopen(Request $request)
    {
        $reopen = ReopenDocument::reopenDocument($request);
        if (!$reopen["success"]) {
            return $this->sendError($reopen["message"]);
        } else {
            return $this->sendResponse(array(), $reopen["message"]);
        }
    }

    public function getAllCurrencyConversionApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        
        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $companyID = \Helper::getGroupCompany($companyId);
        }else{
            $companyID = [$companyId];
        }
        
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $conversions = DB::table('erp_documentapproved')->select('employeesdepartments.approvalDeligated','currency_conversion_master.*','erp_documentapproved.documentApprovedID','rollLevelOrder','approvalLevelID','documentSystemCode', 'employees.empName as createdByEmp', 'employees.empID as createdByEmpID')->join('employeesdepartments',function ($query) use ($companyID,$empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID',96)
                ->whereIn('employeesdepartments.companySystemID',$companyID)
                ->where('employeesdepartments.employeeSystemID',$empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('currency_conversion_master',function ($query) use ($companyID,$empID,$search) {
            $query->on('currency_conversion_master.id','=','erp_documentapproved.documentSystemCode')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('currency_conversion_master.approvedYN', 0)
                ->where('currency_conversion_master.confirmedYN', 1)
                ->when($search != "", function ($q) use($search){
                    $q->where(function ($query) use($search) {
                        $query->where('conversionCode','LIKE',"%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%");
                    });
                });
        })
            ->leftJoin('employees','employees.employeeSystemID','=','createdBy')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN',0)
            ->where('erp_documentapproved.documentSystemID',96)
            ->whereIn('erp_documentapproved.companySystemID',$companyID);

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $conversions = [];
        }

        return \DataTables::of($conversions)
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

    public function approveCurrencyConversion(Request $request){
        $approve = \Helper::approveDocument($request);
        if(!$approve["success"]){
            return $this->sendError($approve["message"]);
        }else{
            return $this->sendResponse(array(),$approve["message"]);
        }

    }

    public function rejectCurrencyConversion(Request $request){
        $reject = \Helper::rejectDocument($request);
        if(!$reject["success"]){
            return $this->sendError($reject["message"]);
        }else{
            return $this->sendResponse(array(),$reject["message"]);
        }

    }
}
