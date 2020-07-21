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
use App\Models\EmployeeMobileBillMaster;
use App\Models\MobileBillMaster;
use App\Models\MobileBillSummary;
use App\Models\MobileDetail;
use App\Models\PeriodMaster;
use App\Models\YesNoSelection;
use App\Repositories\MobileBillMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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

        return $this->sendResponse($mobileBillMasters->toArray(), 'Mobile Bill Masters retrieved successfully');
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

        return $this->sendResponse($mobileBillMaster->toArray(), 'Mobile Bill Master saved successfully');
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
            return $this->sendError('Mobile Bill Master not found');
        }

        return $this->sendResponse($mobileBillMaster->toArray(), 'Mobile Bill Master retrieved successfully');
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
            return $this->sendError('Mobile Bill Master not found');
        }

        if(isset($input['confirmedYN']) && $input['confirmedYN'] == 1){

            // check mobile summary exists
            $isSummaryExists = MobileBillSummary::where('mobileMasterID',$id)->exists();
            if(!$isSummaryExists){
                return $this->sendError('You cannot confirm this Mobile bill. Mobile bill summary not found');
            }

            // check mobile detail exists
            $isDetailExists = MobileDetail::where('mobilebillMasterID',$id)->exists();
            if(!$isDetailExists){
                return $this->sendError('You cannot confirm this Mobile bill. Mobile bill details not found');
            }

            // check employee mobile bill exists
            $isEmpBillExists = EmployeeMobileBillMaster::where('mobilebillMasterID',$id)->exists();
            if(!$isEmpBillExists){
                return $this->sendError('You cannot confirm this Mobile bill. Employee mobile bill is not generated');
            }

            $input['confirmedDate'] = Carbon::now();
            $input['confirmedByEmployeeSystemID'] = $employee->employeeSystemID;
            $input['confirmedby'] = $employee->empID;
        }

        $input['modifiedpc'] = gethostname();
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUser'] = $employee->empID;

        $mobileBillMaster = $this->mobileBillMasterRepository->update($input, $id);

        return $this->sendResponse($mobileBillMaster->toArray(), 'MobileBillMaster updated successfully');
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
            return $this->sendError('Mobile Bill Master not found');
        }

        $mobileBillMaster->delete();

        return $this->sendResponse($mobileBillMaster,'Mobile Bill Master deleted successfully');
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

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    public function mobileSummaryDetailDelete(Request $request){
        $input = $request->all();

        if(!(isset($input['mobilebillMasterID']) && $input['mobilebillMasterID']>0)){
            return $this->sendError('Mobile Bill Master ID not found');
        }

        $isExists = EmployeeMobileBillMaster::where('mobilebillMasterID',$input['mobilebillMasterID'])->exists();

        if($input['type'] == 'summary'){

            if($isExists){
                return $this->sendError('You cannot delete. Employee mobile bill is already generated');
            }

            $isDelete = MobileBillSummary::where('mobileMasterID',$input['mobilebillMasterID'])->delete();
        }elseif ($input['type'] == 'detail'){
            if($isExists){
                return $this->sendError('You cannot delete. Employee mobile bill is already generated');
            }
            $isDelete = MobileDetail::where('mobilebillMasterID',$input['mobilebillMasterID'])->delete();
        }else{
            $isDelete = EmployeeMobileBillMaster::where('mobilebillMasterID',$input['mobilebillMasterID'])->delete();
        }

        if($isDelete) {
            return $this->sendResponse([],'Successfully Deleted');
        }else{
            return $this->sendError('Error Occur',500);
        }
    }



}
