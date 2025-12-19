<?php
/**
 * =============================================
 * -- File Name : MobileMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  MobileMaster
 * -- Author : Mohamed Rilwan
 * -- Create date : 09 - July 2020
 * -- Description : This file contains the all CRUD for MobileMaster
 * -- REVISION HISTORY
 * -- Date: 09 - July 2020 By: Rilwan Description: Added new functions named as getAllMobileNo()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMobileMasterAPIRequest;
use App\Http\Requests\API\UpdateMobileMasterAPIRequest;
use App\Models\Employee;
use App\Models\MobileMaster;
use App\Models\MobileNoPool;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\MobileMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Mpdf\Tag\P;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MobileMasterController
 * @package App\Http\Controllers\API
 */

class MobileMasterAPIController extends AppBaseController
{
    /** @var  MobileMasterRepository */
    private $mobileMasterRepository;

    public function __construct(MobileMasterRepository $mobileMasterRepo)
    {
        $this->mobileMasterRepository = $mobileMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileMasters",
     *      summary="Get a listing of the MobileMasters.",
     *      tags={"MobileMaster"},
     *      description="Get all MobileMasters",
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
     *                  @SWG\Items(ref="#/definitions/MobileMaster")
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
        $this->mobileMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileMasters = $this->mobileMasterRepository->all();

        return $this->sendResponse($mobileMasters->toArray(), trans('custom.mobile_masters_retrieved_successfully'));
    }

    /**
     * @param CreateMobileMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileMasters",
     *      summary="Store a newly created MobileMaster in storage",
     *      tags={"MobileMaster"},
     *      description="Store MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileMaster")
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
     *                  ref="#/definitions/MobileMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input,array('employeeSystemID','mobileNoPoolID','isActive'));
        $input = array_except($input,['employee','mobile_no']);
        $validator = \Validator::make($input, [
            'employeeSystemID' => 'required|min:1',
            'mobileNoPoolID' => 'required|min:1',
            'creditlimit' => 'required',
            'isActive' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if($input['mobileNoPoolID'] == 0){
            return $this->sendError(trans('custom.mobile_no_not_found'),500);
        }

        if($input['employeeSystemID'] == 0){
            return $this->sendError(trans('custom.employee_not_found'),500);
        }

        $employee = Employee::find($input['employeeSystemID']);
        $input['empID'] = $employee->empID;
        $input['assignDate'] = date('Y-m-d');

        $isEmployeeExist = MobileMaster::where('isActive',-1)->where('employeeSystemID',$input['employeeSystemID'])
            ->when((isset($input['mobilemasterID']) && $input['mobilemasterID']), function ($q) use($input){
                $q->where('mobilemasterID','!=',$input['mobilemasterID']);
            })
            ->exists();
        if($isEmployeeExist){
            return $this->sendError("Employee has already assigned for a mobile no", 500);
        }
        $isMobileExist = MobileMaster::where('isActive',-1)->where('mobileNoPoolID',$input['mobileNoPoolID'])
            ->when((isset($input['mobilemasterID']) && $input['mobilemasterID']), function ($q) use($input){
                $q->where('mobilemasterID','!=',$input['mobilemasterID']);
            })
            ->exists();
        if($isMobileExist){
            return $this->sendError("Mobile No has already assigned for a employee", 500);
        }

        $mobile = MobileNoPool::find($input['mobileNoPoolID']);
        $input['mobileNo'] = $mobile->mobileNo;

        if(isset($input['mobilemasterID']) && $input['mobilemasterID']){

            $mobileMaster = $this->mobileMasterRepository->findWithoutFail($input['mobilemasterID']);
            if (empty($mobileMaster)) {
                return $this->sendError(trans('custom.mobile_master_not_found'));
            }
            $mobileMaster = $this->mobileMasterRepository->update($input, $input['mobilemasterID']);

        }else{

            $mobileMaster = $this->mobileMasterRepository->create($input);
        }



        return $this->sendResponse($mobileMaster->toArray(), trans('custom.mobile_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileMasters/{id}",
     *      summary="Display the specified MobileMaster",
     *      tags={"MobileMaster"},
     *      description="Get MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileMaster",
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
     *                  ref="#/definitions/MobileMaster"
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
        /** @var MobileMaster $mobileMaster */
        $mobileMaster = $this->mobileMasterRepository->findWithoutFail($id);

        if (empty($mobileMaster)) {
            return $this->sendError(trans('custom.mobile_master_not_found'));
        }

        return $this->sendResponse($mobileMaster->toArray(), trans('custom.mobile_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMobileMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileMasters/{id}",
     *      summary="Update the specified MobileMaster in storage",
     *      tags={"MobileMaster"},
     *      description="Update MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileMaster")
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
     *                  ref="#/definitions/MobileMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileMaster $mobileMaster */
        $mobileMaster = $this->mobileMasterRepository->findWithoutFail($id);

        if (empty($mobileMaster)) {
            return $this->sendError(trans('custom.mobile_master_not_found'));
        }

        $mobileMaster = $this->mobileMasterRepository->update($input, $id);

        return $this->sendResponse($mobileMaster->toArray(), trans('custom.mobilemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileMasters/{id}",
     *      summary="Remove the specified MobileMaster from storage",
     *      tags={"MobileMaster"},
     *      description="Delete MobileMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileMaster",
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
        /** @var MobileMaster $mobileMaster */
        $mobileMaster = $this->mobileMasterRepository->findWithoutFail($id);

        if (empty($mobileMaster)) {
            return $this->sendError(trans('custom.mobile_master_not_found'));
        }

        $mobileMaster->delete();

        return $this->sendResponse($mobileMaster->toArray(), trans('custom.mobile_master_deleted_successfully'));
    }

    public function getAllMobileMaster(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $mobileMaster = MobileMaster::with(['employee','mobile_no']);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $mobileMaster = $mobileMaster->where(function ($query) use ($search) {
                $query->whereHas('mobile_no', function ($query) use($search){
                    $query->where('mobileNo','LIKE', "%{$search}%");
                })
                ->orWhereHas('employee', function ($query) use($search){
                    $query->where('empID','LIKE', "%{$search}%")
                        ->orWhere('empName','LIKE', "%{$search}%");
                });
            });
        }

        return \DataTables::eloquent($mobileMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('mobilemasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getMobileMasterFormData(){
        $employees = Employee::where('discharegedYN','!=',-1)
                    ->get();

        $mobiles = MobileNoPool::select(DB::raw('mobilenopoolID,CAST(mobileNo AS CHAR) AS mobileNumber'))
                    ->get();

        $yesNoSelection = YesNoSelectionForMinus::all();

        $output = array(
            'employees' => $employees,
            'yesNoSelection' => $yesNoSelection,
            'mobiles' => $mobiles
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));

    }
}
