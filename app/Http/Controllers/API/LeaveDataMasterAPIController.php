<?php
/**
 * =============================================
 * -- File Name : LeaveDataMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 29 - August 2019
 * -- Description : This file contains the all related functions for leave appliation
 * -- REVISION HISTORY
 * -- Date: 29- August 2019 By: Rilwan Description: Added new function getLeaveHistory()
 * -- Date: 01- September 2019 By: Rilwan Description: Added new function getLeaveDetailsForEmployee(),getLeaveAvailableForEmployee
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateLeaveDataMasterAPIRequest;
use App\Http\Requests\API\UpdateLeaveDataMasterAPIRequest;
use App\Models\CalenderMaster;
use App\Models\LeaveDataMaster;
use App\Models\QryLeavePosted;
use App\Models\QryLeavesAccrued;
use App\Models\QryLeavesApplied;
use App\Repositories\LeaveDataMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveDataMasterController
 * @package App\Http\Controllers\API
 */

class LeaveDataMasterAPIController extends AppBaseController
{
    /** @var  LeaveDataMasterRepository */
    private $leaveDataMasterRepository;

    public function __construct(LeaveDataMasterRepository $leaveDataMasterRepo)
    {
        $this->leaveDataMasterRepository = $leaveDataMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataMasters",
     *      summary="Get a listing of the LeaveDataMasters.",
     *      tags={"LeaveDataMaster"},
     *      description="Get all LeaveDataMasters",
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
     *                  @SWG\Items(ref="#/definitions/LeaveDataMaster")
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
        $this->leaveDataMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveDataMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveDataMasters = $this->leaveDataMasterRepository->all();

        return $this->sendResponse($leaveDataMasters->toArray(), 'Leave Data Masters retrieved successfully');
    }

    /**
     * @param CreateLeaveDataMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveDataMasters",
     *      summary="Store a newly created LeaveDataMaster in storage",
     *      tags={"LeaveDataMaster"},
     *      description="Store LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataMaster")
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
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveDataMasterAPIRequest $request)
    {
        $employee = Helper::getEmployeeInfo();
        $empID = $employee->empID;
        $company_id = $employee->empCompanyID;
        $sn = LeaveDataMaster::where('CompanyID',$company_id)->orderBy('leavedatamasterID','Desc')->max('serialNo');
        $serial_no = $sn+1;
        $code = str_pad($serial_no, 5, '0', STR_PAD_LEFT);
        $leaveDataMasterCode = $company_id.'/HR/'.'LA'.$code;
        $input = array(
            'empID'=>$empID,
            'EntryType'=>1,
            'managerAttached'=>$employee->empManagerAttached,
            'SeniorManager'=>isset($employee->manager->empManagerAttached)?$employee->manager->empManagerAttached:0,
            'designatiomID'=>isset($employee->details->designationID)?$employee->details->designationID:0,
            'CompanyID'=>$company_id,
            'location'=>isset($employee->details->categoryID)?$employee->details->categoryID:0,
            'scheduleMasterID'=>isset($employee->details->schedulemasterID)?$employee->details->schedulemasterID:0,
            'leaveDataMasterCode'=>$leaveDataMasterCode,
            'documentID'=>'LA',
            'serialNo'=>$serial_no,
            'createDate'=>date('Y-m-d H:i:s'),
            'createduserGroup'=>$empID,
            'createdpc'=>strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))
        );

        $leaveDataMaster = $this->leaveDataMasterRepository->create($input);

        return $this->sendResponse($leaveDataMaster->toArray(), 'Leave Data Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataMasters/{id}",
     *      summary="Display the specified LeaveDataMaster",
     *      tags={"LeaveDataMaster"},
     *      description="Get LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
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
     *                  ref="#/definitions/LeaveDataMaster"
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
        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        return $this->sendResponse($leaveDataMaster->toArray(), 'Leave Data Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLeaveDataMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveDataMasters/{id}",
     *      summary="Update the specified LeaveDataMaster in storage",
     *      tags={"LeaveDataMaster"},
     *      description="Update LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataMaster")
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
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveDataMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        $leaveDataMaster = $this->leaveDataMasterRepository->update($input, $id);

        return $this->sendResponse($leaveDataMaster->toArray(), 'LeaveDataMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveDataMasters/{id}",
     *      summary="Remove the specified LeaveDataMaster from storage",
     *      tags={"LeaveDataMaster"},
     *      description="Delete LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
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
        /** @var LeaveDataMaster $leaveDataMaster */

        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        // check is claim data
        if($leaveDataMaster->claimedLeavedatamasterID != null && $leaveDataMaster->EntryType==2){
            $date_time = date('Y-m-d H:i:s');
            $this->leaveDataMasterRepository->update(['claimedYN' => 0, 'timestamp' => $date_time], $leaveDataMaster->claimedLeavedatamasterID);
        }

        if(!empty($leaveDataMaster->detail())){
            $leaveDataMaster->detail()->delete();
        }
        $leaveDataMaster->delete();

        return $this->sendResponse($id, 'Leave Data Master deleted successfully');
    }

    public function getLeaveHistory()
    {
        $emp_id = Helper::getEmployeeID();
        $leaveHistory =QryLeavePosted::select('createDate','leaveDataMasterCode','leavetype','Manager','Type','confirmedYN',
            'approvedYN','confirmedYN','leavedatamasterID','LeaveApplicationTypeID')
            ->where('empID',$emp_id)
            ->get();
        return $this->sendResponse($leaveHistory->toArray(), 'Leave history details retrieved successfully');
    }

    public function getLeaveDetailsForEmployee(Request $request)
    {
        $fromDate = isset($request['fromDate'])?$request['fromDate']:null;
        $toDate = isset($request['toDate'])?$request['toDate']:null;
        $leaveMasterID = isset($request['leaveMasterID'])?$request['leaveMasterID']:null;

        if($leaveMasterID==null){
            return $this->sendResponse([],'Leave details not found');
        }

        $employee = Helper::getEmployeeInfo();
        $empID = $employee->empID;
        $workingDays = 0;
        $nonWorkingDays = 0;
        $total_days_applied = 0;
        $balance = 0;
        $day_type = "Working Days";

        if($leaveMasterID==16||$leaveMasterID==2||$leaveMasterID==3||$leaveMasterID==4||$leaveMasterID==21)
        {
            $date = date('Y');
            $available = $this->getLeaveAvailableForEmployee($empID,$leaveMasterID,$date);
        }else{
            $available = $this->getLeaveAvailableForEmployee($empID,$leaveMasterID);
        }

        if($fromDate!=null && $toDate!=null){

            $workingDays=CalenderMaster::where('isWorkingDay',-1)->whereBetween('calDate',[$fromDate, $toDate])->get()->count();
            $nonWorkingDays=CalenderMaster::where('isWorkingDay',0)->whereBetween('calDate',[$fromDate, $toDate])->get()->count();
            $total_days_applied = $workingDays+$nonWorkingDays;

            if($leaveMasterID==16||$leaveMasterID==18||$leaveMasterID==3||$leaveMasterID==13||$leaveMasterID==2||$leaveMasterID==3||$leaveMasterID==4||$leaveMasterID==21||$leaveMasterID==5||$leaveMasterID==11){
                $balance = $available - ($workingDays+$nonWorkingDays);
                $day_type = "Total days applied";
            }else{
                $calculateCalendarDays = isset($employee->details->schedule->calculateCalendarDays)?$employee->details->schedule->calculateCalendarDays:0;
                if($leaveMasterID == 1){
                    if($calculateCalendarDays==-1){
                        $balance = $available - ($workingDays+$nonWorkingDays);
                    }else{
                        $balance = $available - $workingDays;
                    }
                }else{
                    $balance = $available - $workingDays;
                }

            }
        }

        $output = array(
            'leave_available'=>$available,
            'working_days'=>$workingDays,
            'non_working_days'=>$nonWorkingDays,
            'total_days_applied'=>$total_days_applied,
            'balance'=>$balance,
            'day_type'=>$day_type
        );
        return $this->sendResponse($output, 'Leave details retrieved successfully');
    }

    private function getLeaveAvailableForEmployee($empID,$leaveMasterID=null,$date=null)
    {
        if($empID!=null && $leaveMasterID!=null){

            $leave_accured = QryLeavesAccrued::selectRaw('SUM(SumOfDaysEntitled) as leaveBalanceaccrued')
                ->where('empID',$empID)
                ->where('leaveType',$leaveMasterID)
                ->where(function ($query) use ($date) {
                    if($date!=null){
                        $query->where('periodYear',$date);
                    }
                })->groupBy('leaveType')
                ->first();
            $leave_accured = isset($leave_accured->leaveBalanceaccrued)?$leave_accured->leaveBalanceaccrued:0;

            $leave_applied = QryLeavesApplied::selectRaw('SUM(calculatedDays) as leaveBalanceapplied')
                ->where('empID',$empID)
                ->where('leavemasterID',$leaveMasterID)
                ->where(function ($query) use ($date) {
                    if($date!=null){
                        $query->where('CYear',$date);
                    }
                })->groupBy('leavemasterID')
                ->first();

            $leave_applied = isset($leave_applied->leaveBalanceapplied)?$leave_applied->leaveBalanceapplied:0;

            $balance = $leave_accured-$leave_applied;
            return $balance;
        }
        return 0;
    }

}
