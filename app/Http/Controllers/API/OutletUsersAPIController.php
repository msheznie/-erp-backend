<?php
/**
 * =============================================
 * -- File Name : OutletUsersAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Outlet Users
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - January 2019
 * -- Description : This file contains the all CRUD for Outlet Users
 * -- REVISION HISTORY
 * -- Date: 03-January 2019 By: Fayas Description: Added new functions named as getAssignedUsersOutlet(),getUnAssignUsersByOutlet()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOutletUsersAPIRequest;
use App\Http\Requests\API\UpdateOutletUsersAPIRequest;
use App\Models\Employee;
use App\Models\OutletUsers;
use App\Models\ShiftDetails;
use App\Repositories\OutletUsersRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class OutletUsersController
 * @package App\Http\Controllers\API
 */
class OutletUsersAPIController extends AppBaseController
{
    /** @var  OutletUsersRepository */
    private $outletUsersRepository;

    public function __construct(OutletUsersRepository $outletUsersRepo)
    {
        $this->outletUsersRepository = $outletUsersRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/outletUsers",
     *      summary="Get a listing of the OutletUsers.",
     *      tags={"OutletUsers"},
     *      description="Get all OutletUsers",
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
     *                  @SWG\Items(ref="#/definitions/OutletUsers")
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
        $this->outletUsersRepository->pushCriteria(new RequestCriteria($request));
        $this->outletUsersRepository->pushCriteria(new LimitOffsetCriteria($request));
        $outletUsers = $this->outletUsersRepository->all();

        return $this->sendResponse($outletUsers->toArray(), trans('custom.outlet_users_retrieved_successfully'));
    }

    /**
     * @param CreateOutletUsersAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/outletUsers",
     *      summary="Store a newly created OutletUsers in storage",
     *      tags={"OutletUsers"},
     *      description="Store OutletUsers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="OutletUsers that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/OutletUsers")
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
     *                  ref="#/definitions/OutletUsers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateOutletUsersAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = array(
            'wareHouseCode.required' => 'The Outlet field is required.',
            'userID.required' => 'The user field is required.',
        );

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'wareHouseID' => 'required',
            'userID' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $checkAlreadyExist = OutletUsers::where('companySystemID', $input['companySystemID'])
            ->where('wareHouseID', $input['wareHouseID'])
            ->where('userID', $input['userID'])
            ->count();
        if ($checkAlreadyExist > 0) {
            return $this->sendError(trans('custom.selected_user_is_already_added_to_this_outlet'), 500);
        }

        $checkEmployee = Employee::where('employeeSystemID', $input['userID'])
            ->where('empCompanySystemID', $input['companySystemID'])
            ->where('discharegedYN', 0)
            ->first();

        if (empty($checkEmployee)) {
            return $this->sendError(trans('custom.user_not_found_1'), 500);
        }

        $checkUserActive = OutletUsers::where('userID',$input['userID'])
                                        ->where('companySystemID',$input['companySystemID'])
                                        ->where('isActive',1)
                                        ->first();

        if(!empty($checkUserActive)){
            return $this->sendError(trans('custom.selected_user_is_already_active_in_outlet').$checkUserActive->outlet->wareHouseCode,500);
        }


        $input['companyID'] = \Helper::getCompanyById($input['companySystemID']);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdUserName'] = $employee->empName;
        $data['timestamp'] = now();

        $outletUsers = $this->outletUsersRepository->create($input);

        return $this->sendResponse($outletUsers->toArray(), trans('custom.outlet_user_created_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/outletUsers/{id}",
     *      summary="Display the specified OutletUsers",
     *      tags={"OutletUsers"},
     *      description="Get OutletUsers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OutletUsers",
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
     *                  ref="#/definitions/OutletUsers"
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
        /** @var OutletUsers $outletUsers */
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            return $this->sendError(trans('custom.outlet_users_not_found'));
        }

        return $this->sendResponse($outletUsers->toArray(), trans('custom.outlet_users_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateOutletUsersAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/outletUsers/{id}",
     *      summary="Update the specified OutletUsers in storage",
     *      tags={"OutletUsers"},
     *      description="Update OutletUsers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OutletUsers",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="OutletUsers that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/OutletUsers")
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
     *                  ref="#/definitions/OutletUsers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateOutletUsersAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['employee']);
        $input = $this->convertArrayToValue($input);
        $messages = array(
            'wareHouseCode.required' => 'The Outlet field is required.',
            'userID.required' => 'The user field is required.',
        );

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'wareHouseID' => 'required',
            'userID' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var OutletUsers $outletUsers */
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            return $this->sendError(trans('custom.outlet_user_not_found'));
        }

        if (isset($input['isActive']) && $input['isActive']) {
            $input['isActive'] = 1;

            $checkUserActive = OutletUsers::where('userID',$input['userID'])
                                           ->where('companySystemID',$input['companySystemID'])
                                           ->where('isActive',1)
                                           ->with(['outlet'])
                                           ->first();

            if(!empty($checkUserActive)){
                return $this->sendError(trans('custom.user_is_already_active_in_outlet').$checkUserActive->outlet->wareHouseCode,500);
            }

        } else {
            $input['isActive'] = 0;

            $shift = ShiftDetails::where('isClosed',0)
                                    ->where('empID',$input['userID'])
                                    ->with(['user','outlet','counter'])
                                    ->first();

            if(!empty($shift)){
                return $this->sendError(trans('custom.cannot_deactivate_selected_user_has_on_going_shift'));
            }
        }

        $input['companyID'] = \Helper::getCompanyById($input['companySystemID']);
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUserName'] = $employee->empName;
        $input['timestamp'] = now();

        $outletUsers = $this->outletUsersRepository->update($input, $id);

        return $this->sendResponse($outletUsers->toArray(), trans('custom.outlet_user_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/outletUsers/{id}",
     *      summary="Remove the specified OutletUsers from storage",
     *      tags={"OutletUsers"},
     *      description="Delete OutletUsers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OutletUsers",
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
        /** @var OutletUsers $outletUsers */
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            return $this->sendError(trans('custom.outlet_user_not_found'));
        }

        $shift = ShiftDetails::where('isClosed',0)
            ->where('wareHouseID',$outletUsers->wareHouseID)
            ->where('empID',$outletUsers->userID)
            ->first();

        if(!empty($shift)){
            return $this->sendError(trans('custom.cannot_delete_selected_user_has_on_going_shift'));
        }

        $outletUsers->delete();

        return $this->sendResponse($id, trans('custom.outlet_users_deleted_successfully'));
    }

    public function getAssignedUsersOutlet(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $outlets = OutletUsers::whereIn('companySystemID', $subCompanies)
            ->where('wareHouseID', $input['id'])
            ->with(['employee']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $outlets = $outlets->where(function ($query) use ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('empID', 'LIKE', "%{$search}%")
                        ->orWhere('empName', 'LIKE', "%{$search}%");
                });
            });
        }

        return \DataTables::eloquent($outlets)
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

    public function getUnAssignUsersByOutlet(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'wareHouseID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $users = Employee::where('empCompanySystemID', $input['companySystemID'])
            ->where('discharegedYN', 0)
            ->whereDoesntHave('outlet', function ($q) use ($input) {
                $q->where('companySystemID', $input['companySystemID'])
                    ->where('wareHouseID', $input['wareHouseID']);
            })
            ->select(['employeeSystemID', 'empID', 'empName']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $users = $users->where(function ($query) use ($search) {
                $query->where('empID', 'LIKE', "%{$search}%")
                    ->orWhere('empName', 'LIKE', "%{$search}%");
            });
        }

        $users = $users->take(20)->get();
        return $this->sendResponse($users->toArray(), trans('custom.data_retrieved_successfully'));
    }


}
