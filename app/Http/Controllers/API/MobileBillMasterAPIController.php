<?php
/**
 * MobileBillMasterAPIController
 * -- File Name : MobileNoPoolAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Mobile Bill
 * -- Author : Mohamed Rilwan
 * -- Create date : 12 - July 2020
 * -- Description : This file contains the all CRUD for Mobile No Pool
 * -- REVISION HISTORY
 * -- Date: 12 - July 2020 By: Rilwan Description: Added new functions named as getAllMobileMaster()
 * -- Date: 12 - July 2020 By: Rilwan Description: Added new functions named as getMobileMasterFormData()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateMobileBillMasterAPIRequest;
use App\Http\Requests\API\UpdateMobileBillMasterAPIRequest;
use App\Models\Company;
use App\Models\EmployeeMobileBillMaster;
use App\Models\MobileBillMaster;
use App\Models\MobileBillSummary;
use App\Models\MobileDetail;
use App\Models\PeriodMaster;
use App\Models\SegmentRights;
use App\Models\YesNoSelection;
use App\Repositories\MobileBillMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MobileBillMasterController
 * @package App\Http\Controllers\API
 */

class MobileBillMasterAPIController extends AppBaseController
{
    /** @var  MobileBillMasterRepository */
    private $mobileBillMasterRepository;

    public function __construct(MobileBillMasterRepository $mobileBillMasterRepo)
    {
        $this->mobileBillMasterRepository = $mobileBillMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileBillMasters",
     *      summary="Get a listing of the MobileBillMasters.",
     *      tags={"MobileBillMaster"},
     *      description="Get all MobileBillMasters",
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
     *                  @SWG\Items(ref="#/definitions/MobileBillMaster")
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
        $this->mobileBillMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileBillMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileBillMasters = $this->mobileBillMasterRepository->all();

        return $this->sendResponse($mobileBillMasters->toArray(), trans('custom.mobile_bill_masters_retrieved_successfully'));
    }

    /**
     * @param CreateMobileBillMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileBillMasters",
     *      summary="Store a newly created MobileBillMaster in storage",
     *      tags={"MobileBillMaster"},
     *      description="Store MobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileBillMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileBillMaster")
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
     *                  ref="#/definitions/MobileBillMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileBillMasterAPIRequest $request)
    {
        $input = $request->all();

        $messages = [
            'billPeriod.unique' => 'The Bill period is already taken.'
        ];

        $validator = \Validator::make($input, [
            'billPeriod' => 'required|unique:hrms_mobilebillmaster',
            'Description' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentID'] = 'EMB';

        //Order Code
        $lastSerial = MobileBillMaster::orderBy('serialNo', 'desc') ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }
        $input['mobilebillmasterCode'] = ('HR/' .$input['documentID']. str_pad($lastSerialNumber, 5, '0', STR_PAD_LEFT));

        $input['serialNo'] = $lastSerialNumber;

        $employee = Helper::getEmployeeInfo();
        $input['createUserID'] = $employee->empID;
        $input['createPCID'] = gethostname();

        $mobileBillMaster = $this->mobileBillMasterRepository->create($input);

        return $this->sendResponse($mobileBillMaster->toArray(), trans('custom.mobile_bill_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileBillMasters/{id}",
     *      summary="Display the specified MobileBillMaster",
     *      tags={"MobileBillMaster"},
     *      description="Get MobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileBillMaster",
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
     *                  ref="#/definitions/MobileBillMaster"
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
        /** @var MobileBillMaster $mobileBillMaster */

        $mobileBillMaster = $this->mobileBillMasterRepository->with(['confirmed_by','approved_by','summary' => function($query){
            $query->with(['mobile_pool.mobile_master.employee']);
        },'detail' => function($query){
            $query->with(['mobile_pool.mobile_master.employee']);
        },'employee_mobile' => function($query){
            $query->with(['mobile_pool.mobile_master.employee']);
        }])->findWithoutFail($id);

        if (empty($mobileBillMaster)) {
            return $this->sendError(trans('custom.mobile_bill_master_not_found'));
        }

        return $this->sendResponse($mobileBillMaster->toArray(), trans('custom.mobile_bill_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMobileBillMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileBillMasters/{id}",
     *      summary="Update the specified MobileBillMaster in storage",
     *      tags={"MobileBillMaster"},
     *      description="Update MobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileBillMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileBillMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileBillMaster")
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
     *                  ref="#/definitions/MobileBillMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileBillMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,['detail','employee_mobile','summary','confirmed_by']);
        $input = $this->convertArrayToValue($input);
        $messages = [
            'billPeriod.unique' => 'The Bill period is already taken.'
        ];

        $validator = \Validator::make($input, [
            'billPeriod' => ['required', Rule::unique('hrms_mobilebillmaster')->ignore($id, 'mobilebillMasterID')],
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $employee = Helper::getEmployeeInfo();
        /** @var MobileBillMaster $mobileBillMaster */
        $mobileBillMaster = $this->mobileBillMasterRepository->findWithoutFail($id);

        if (empty($mobileBillMaster)) {
            return $this->sendError(trans('custom.mobile_bill_master_not_found'));
        }

        if(isset($input['confirmedYN']) && $input['confirmedYN'] == 1){

            // check mobile summary exists
            $isSummaryExists = MobileBillSummary::where('mobileMasterID',$id)->exists();
            if(!$isSummaryExists){
                return $this->sendError(trans('custom.you_cannot_confirm_this_mobile_bill_mobile_bill_su'));
            }

            // check mobile detail exists
            $isDetailExists = MobileDetail::where('mobilebillMasterID',$id)->exists();
            if(!$isDetailExists){
                return $this->sendError(trans('custom.you_cannot_confirm_this_mobile_bill_mobile_bill_de'));
            }

            // check employee mobile bill exists
            $isEmpBillExists = EmployeeMobileBillMaster::where('mobilebillMasterID',$id)->exists();
            if(!$isEmpBillExists){
                return $this->sendError(trans('custom.you_cannot_confirm_this_mobile_bill_employee_mobil'));
            }

            $input['confirmedDate'] = Carbon::now();
            $input['confirmedByEmployeeSystemID'] = $employee->employeeSystemID;
            $input['confirmedby'] = $employee->empID;
        }

        $input['modifiedpc'] = gethostname();
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUser'] = $employee->empID;

        $mobileBillMaster = $this->mobileBillMasterRepository->update($input, $id);

        return $this->sendResponse($mobileBillMaster->toArray(), trans('custom.mobilebillmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileBillMasters/{id}",
     *      summary="Remove the specified MobileBillMaster from storage",
     *      tags={"MobileBillMaster"},
     *      description="Delete MobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileBillMaster",
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
        /** @var MobileBillMaster $mobileBillMaster */
        $mobileBillMaster = $this->mobileBillMasterRepository->findWithoutFail($id);

        if (empty($mobileBillMaster)) {
            return $this->sendError(trans('custom.mobile_bill_master_not_found'));
        }

        $mobileBillMaster->delete();

        return $this->sendResponse($mobileBillMaster,trans('custom.mobile_bill_master_deleted_successfully'));
    }

    public function getAllMobileBill(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $mobileMaster = MobileBillMaster::with(['period']);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $mobileMaster = $mobileMaster->where(function ($query) use ($search) {
                $query->where('mobilebillmasterCode', 'LIKE', "%{$search}%")
                    ->orWhere('Description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($mobileMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('mobilebillMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getMobileBillFormData(){

        $yesNoSelection = YesNoSelection::all();
        $period = PeriodMaster::orderBy('periodMasterID','DESC')->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'period' => $period
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));

    }

    public function mobileSummaryDetailDelete(Request $request){
        $input = $request->all();

        if(!(isset($input['mobilebillMasterID']) && $input['mobilebillMasterID']>0)){
            return $this->sendError(trans('custom.mobile_bill_master_id_not_found'));
        }

        $isExists = EmployeeMobileBillMaster::where('mobilebillMasterID',$input['mobilebillMasterID'])->exists();

        if($input['type'] == 'summary'){

            if($isExists){
                return $this->sendError(trans('custom.you_cannot_delete_employee_mobile_bill_is_already_'));
            }

            $isDelete = MobileBillSummary::where('mobileMasterID',$input['mobilebillMasterID'])->delete();
        }elseif ($input['type'] == 'detail'){
            if($isExists){
                return $this->sendError(trans('custom.you_cannot_delete_employee_mobile_bill_is_already_'));
            }
            $isDelete = MobileDetail::where('mobilebillMasterID',$input['mobilebillMasterID'])->delete();
        }else{
            $isDelete = EmployeeMobileBillMaster::where('mobilebillMasterID',$input['mobilebillMasterID'])->delete();
        }

        if($isDelete) {
            return $this->sendResponse([],trans('custom.successfully_deleted'));
        }else{
            return $this->sendError(trans('custom.error_occur'),500);
        }
    }

    public function validateMobileReport(Request $request){
        $input = $request->all();
        $masterIDs = [];
        $periodIDs = [];

        if (array_key_exists('billMaster', $input)) {
            $masterID = (array)$input['billMaster'];
            $masterIDs = collect($masterID)->pluck('mobilebillMasterID');

            $periodIDs = MobileBillMaster::select('billPeriod')->whereIn('mobilebillMasterID',$masterIDs)->get()->pluck('billPeriod');

        }else{
            return $this->sendError('Please select at least one bill',500);
        }


        if(count($periodIDs)){

            $periods = PeriodMaster::whereIn('periodMasterID',$periodIDs)->orderBy('periodMasterID')->get();
            $years = array_unique(collect($periods)->pluck('periodYear')->toArray());
            if(count($years)>1){
                return $this->sendError('Different years of bills found',500);
            }

        }

        return $this->sendResponse([],'success');

    }

    public function getMobileBillReport(Request $request){
        $input = $request->all();

        $result = $this->getMobileBillReportQuery($input);

        return \DataTables::of($result['output'])
            ->addIndexColumn()
            ->with('period',$result['period'])
            ->make(true);
    }

    public function getMobileReportFormData(Request $request) {

        $employee_id = Helper::getEmployeeSystemID();
        $segment = SegmentRights::select('companySystemID')
            ->where('employeeSystemID', $employee_id)
            ->groupby('companySystemID')
            ->get();
        $output['company'] = [];
        if (count($segment) > 0) {
            $companiesByGroup = array_pluck($segment, 'companySystemID');
            $company = Company::select('masterCompanySystemIDReorting')
                ->whereIn('companySystemID', $companiesByGroup)
                ->get();

            $masterCompany = array_pluck($company, 'masterCompanySystemIDReorting');
            $output['company'] = Company::select(DB::raw("companySystemID,CONCAT(CompanyID,' - ',CompanyName) as label"))
                ->whereIn('companySystemID', $masterCompany)
                ->get();
        }

        return $this->sendResponse($output,trans('custom.successfully_retrieved'));
    }

    public function getBillMastersByCompany(Request $request){
        $input = $request->all();

        $companyID = 0;
        if (array_key_exists('company', $input)) {
            $companies = (array)$input['company'];
            $companyID = collect($companies)->pluck('companySystemID');
        }

        $billMaster = [];
        if ($companyID) {
            $billMaster =  MobileBillMaster::where('confirmedYN',1)
                ->join('hrms_periodmaster','hrms_mobilebillmaster.billPeriod','=','hrms_periodmaster.periodMasterID')
                ->whereHas('employee_mobile',function ($query) use($companyID){
                    $query->whereIn('companySysID',$companyID);
                })
                ->select(DB::raw("mobilebillMasterID,CONCAT(mobilebillmasterCode,' - ',periodMonth,' - ',periodYear) as label,billPeriod"))
                ->orderBy('mobilebillMasterID','DESC')
                ->get();
        }
        return $this->sendResponse($billMaster, trans('custom.bill_master_retrieved_successfully'));
    }

    public function exportMobileReport(Request $request){
        $input = $request->all();
        $type = isset($input['type'])?$input['type']:'csv';
        $result = $this->getMobileBillReportQuery($input);

        if (!empty($result['output'])) {
            $x = 0;
            $data = [];
            foreach ($result['output'] as $val) {
                $x++;

                $data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Employee'] = $val->empID.' - '.isset($val->employee->empName)?$val->employee->empName:'';
                $data[$x]['Employee Email'] = isset($val->employee->empEmail)?$val->employee->empEmail:'';
                $data[$x]['Department Description'] = isset($val->employee->details->hrmsDepartmentMaster->DepartmentDescription)?$val->employee->details->hrmsDepartmentMaster->DepartmentDescription:'';
                $data[$x]['Segment Code'] = isset($val->employee->details->hrmsDepartmentMaster->ServiceLineCode)?$val->employee->details->hrmsDepartmentMaster->ServiceLineCode:'';
                $data[$x]['Mobile No'] = $val->mobileNo;
                $data[$x]['Credit Limit'] = round($val->climit,3);
                $data[$x]['Total Amount'] = round($val->totalAmount,3);
                $data[$x]['Exceeded Amount'] = round($val->exceededAmount,3);
                $data[$x]['Deduction Amount'] = round($val->deductionAmount,3);
                $data[$x]['Official Amount'] = round($val->officialAmount,3);
                $data[$x]['GPRS PayG'] = round($val->GPRSPayG,3);
                $data[$x]['GPRS PKG'] = round($val->GPRSPKG,3);
                $data[$x]['Final Deduction Amount'] = round($val->FinalDeductionAmount,3);

            }

            \Excel::create('mobile_report', function ($excel) use ($data) {
                $excel->sheet('sheet name', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    
                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }
                });
                $lastrow = $excel->getActiveSheet()->getHighestRow();
                $excel->getActiveSheet()->getStyle('A1:N' . $lastrow)->getAlignment()->setWrapText(true);
            })->download($type);

            return $this->sendResponse(array(), trans('custom.success_export'));
        }
        return $this->sendError( 'No Records Found');
    }

    private function getMobileBillReportQuery($input){

        $masterIDs = [];
        $periodIDs = [];
        $companyIDs = [];
        $str = '';
        $billCount = 0;
        if (array_key_exists('billMasters', $input)) {
            $masterID = (array)$input['billMasters'];
            $masterIDs = collect($masterID)->pluck('mobilebillMasterID');
            $billCount = collect($masterID)->count();
            $periodIDs = MobileBillMaster::select('billPeriod')->whereIn('mobilebillMasterID',$masterIDs)->get()->pluck('billPeriod');

        }

        if (array_key_exists('companyID', $input)) {
            $companyID = (array)$input['companyID'];
            $companyIDs = collect($companyID)->pluck('companySystemID');
        }


        if(count($periodIDs)){

            $periods = PeriodMaster::whereIn('periodMasterID',$periodIDs)->orderBy('periodMasterID')->get();
            $years = array_unique(collect($periods)->pluck('periodYear')->toArray());

            $periodArray = [];
            if(!empty($periods)){
                foreach ($periods as $row){
                    $periodArray[$row->periodYear][] = $row->periodMonth;
                }
            }

            $stringArray = [];
            foreach ($years as $year){
                $stringArray[]= $year. ' ['. implode(', ', $periodArray[$year]).' ]';

            }

            if(count($stringArray)){
                $str .= implode(', ', $stringArray);
            }

        }


        $result['period'] = $str;
        $result['output'] = EmployeeMobileBillMaster::whereIn('mobilebillMasterID',$masterIDs)
            ->whereIn('companySysID',$companyIDs)
            ->join('hrms_mobilebillsummary', function ($join){
                $join->on('hrms_employeemobilebillmaster.mobileNo','=','hrms_mobilebillsummary.mobileNumber')
                    ->on('hrms_employeemobilebillmaster.mobilebillMasterID', '=', 'hrms_mobilebillsummary.mobileMasterID');
            })
//            ->select(DB::raw('mobilebillMasterID,employeeSystemID,mobileNo,companyID,empID, sum(totalAmount) as totalAmount, sum(deductionAmount) as deductionAmount,sum(exceededAmount) as exceededAmount,sum(officialAmount) as officialAmount,creditLimit * 3 AS crLimit, sum(creditLimit) as sumCreditLimit, sum(GPRSPayG) as GPRSPayG, sum(GPRSPKG) as GPRSPKG, sum(totalCurrentCharges) as totalCurrentCharges, IF(totalAmount < creditLimit,0, IF(officialAmount = 0 AND creditLimit > totalAmount,0,IF(officialAmount = 0 AND creditLimit < totalAmount,totalAmount - creditLimit,IF(officialAmount > deductionAmount,0,IF(deductionAmount > officialAmount,deductionAmount - officialAmount, 0))))) AS FinalDeductionAmount'))
            ->select(DB::raw('mobilebillMasterID,employeeSystemID,mobileNo,companyID,empID, sum(totalAmount) as totalAmount, sum(deductionAmount) as deductionAmount,sum(exceededAmount) as exceededAmount,sum(officialAmount) as officialAmount, creditLimit * '.$billCount.' as climit, sum(creditLimit) as sumCreditLimit, sum(GPRSPayG) as GPRSPayG, sum(GPRSPKG) as GPRSPKG, sum(totalCurrentCharges) as totalCurrentCharges, IF(sum(totalAmount) < (creditLimit * '.$billCount.'),0, IF(sum(officialAmount) = 0 AND (creditLimit * '.$billCount.') > sum(totalAmount),0,IF(sum(officialAmount) = 0 AND (creditLimit * '.$billCount.') < sum(totalAmount),sum(totalAmount) - (creditLimit * '.$billCount.'),IF(sum(officialAmount) > sum(deductionAmount),0,IF(sum(deductionAmount) > sum(officialAmount),sum(deductionAmount) - sum(officialAmount), 0))))) AS FinalDeductionAmount'))
            ->whereHas('mobile_pool', function ($query){
                $query->whereHas('mobile_master', function ($q){
                    $q->where('isInternetSim',0);
                });
            })
            ->whereHas('employee', function ($query){
                $query->whereHas('details', function ($q1){
                    $q1->where('employeestatus','!=',2);
                });
            })
            ->with(['employee'=> function($query){
                $query->select('employeeSystemID','empName','empEmail')
                    ->with(['details'=> function($q1){
                        $q1->select('employeeSystemID','departmentID')
                            ->with(['hrmsDepartmentMaster'=> function($q2){
                                $q2->select('DepartmentID','DepartmentDescription','ServiceLineCode');
                            }]);
                    }]);
            }])
            ->groupBy('companyID','employeeSystemID','mobileNo')
            ->orderBy('FinalDeductionAmount','DESC')
            ->get();

        return $result;
    }




}
