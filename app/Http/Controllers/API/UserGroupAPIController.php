<?php
/**
=============================================
-- File Name : UserGroupAPIController.php
-- Project Name : ERP
-- Module Name :  User Group Setup
-- Author : Mohamed Mubashir
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for User Group Setup
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserGroupAPIRequest;
use App\Http\Requests\API\UpdateUserGroupAPIRequest;
use App\Models\EmployeeNavigation;
use App\Models\UserGroup;
use App\Repositories\UserGroupRepository;
use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class UserGroupController
 * @package App\Http\Controllers\API
 */
class UserGroupAPIController extends AppBaseController
{
    use AuditLogsTrait;

    /** @var  UserGroupRepository */
    private $userGroupRepository;

    public function __construct(UserGroupRepository $userGroupRepo)
    {
        $this->userGroupRepository = $userGroupRepo;
    }

    /**
     * Display a listing of the UserGroup.
     * GET|HEAD /userGroups
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userGroupRepository->pushCriteria(new RequestCriteria($request));
        $this->userGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userGroups = $this->userGroupRepository->all();

        return $this->sendResponse($userGroups->toArray(), trans('custom.user_groups_retrieved_successfully'));
    }

    /**
     * Store a newly created UserGroup in storage.
     * POST /userGroups
     *
     * @param CreateUserGroupAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUserGroupAPIRequest $request)
    {   
     
        $input = $request->all();
        $userGroups = "";

        $uuid = $input['tenant_uuid'] ?? 'local';
        $db = $input['db'] ?? '';

        if(isset($input['tenant_uuid'])) {
            unset($input['tenant_uuid']);
        }

        if(isset($input['db'])) {
            unset($input['db']);
        }

        $previousValue = [];
        $crudType = "C";
        $transactionID = 0;
       
    
        if (isset($request->userGroupID))
        {
            $id = $request->userGroupID;
            $crudType = "U";
             $userGroups = UserGroup::where("userGroupID", $id)->first();

            if (empty($userGroups)) {
                return $this->sendError(trans('custom.user_group_not_found'));
            }

            $requestedDefaultYN = filter_var($input['defaultYN'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $requestedCompanyID = $input['companyID'] ?? $userGroups->companyID;
            $requestedDescription = $input['description'] ?? $userGroups->description;

            if (is_array($requestedCompanyID)) {
                $requestedCompanyID = count($requestedCompanyID) > 0 ? $requestedCompanyID[0] : $userGroups->companyID;
            }
            if (is_array($requestedDescription)) {
                $requestedDescription = count($requestedDescription) > 0 ? $requestedDescription[0] : $userGroups->description;
            }

            if ($requestedDefaultYN == false)
            {
                $isExistsDefaultUserGroups = UserGroup::where('userGroupID', '!=', $id)
                    ->where('companyID', $userGroups->companyID)
                    ->where('defaultYN', true)
                    ->count();

                if ($isExistsDefaultUserGroups == 0) {
                    $availableUserGroups = UserGroup::where('companyID', $userGroups->companyID)
                        ->where('userGroupID', '!=', $id)
                        ->select('userGroupID', 'description')
                        ->orderBy('description')
                        ->get();

                    return $this->sendError(
                        trans('custom.company_has_default_user_group'),
                        422,
                        [
                            'type' => 'default_untick_confirmation',
                            'companyID' => $userGroups->companyID,
                            'currentUserGroupID' => $id,
                            'availableUserGroups' => $availableUserGroups,
                        ]
                    );
                }
            }

            if($requestedDefaultYN)
            {
                $userGroupsCheck = UserGroup::where("userGroupID", $id)->where("defaultYN", true)->count();
                if($userGroupsCheck == 0)
                {
                    $userGroupsDefault = UserGroup::where("companyID", $input["companyID"])->where("defaultYN", true)->first();

                
                    if (isset($userGroupsDefault)) {
                        //return $this->sendError(trans('custom.the_company_have_already_a_default_user_group'));
                        return $this->sendError(trans('custom.company_has_default_user_group'), 500);

                    }
                }
            }

            $employeeExists = EmployeeNavigation::where('userGroupID',$id)->count();
            $isCompanyChanged = (int)$requestedCompanyID !== (int)$userGroups->companyID;
            $isDescriptionChanged = trim((string)$requestedDescription) !== trim((string)$userGroups->description);

            if ($employeeExists > 0 && ($isCompanyChanged || $isDescriptionChanged)) {
                return $this->sendError(trans('custom.user_group_already_assigned_to_employees_cannot_ch'));
            }

            $previousValue = $userGroups->toArray();
            $transactionID = $userGroups->userGroupID;
        

            unset($input['company']);
            foreach ($input as $key => $value) {
                if (is_array($input[$key])){
                    if(count($input[$key]) > 0){
                        $input[$key] = $input[$key][0];
                    }else{
                        $input[$key] = 0;
                    }
                }
            }
            $userGroups->companyID = $input["companyID"];
            $userGroups->description = $input["description"];
            $userGroups->isActive = 1;
            $userGroups->defaultYN = $requestedDefaultYN;

           $userGroups->save();
        }else{
            $input['isActive'] = 1;

            if($input["defaultYN"])
            {
                $userGroups = UserGroup::where("companyID", $input["companyID"])->where("defaultYN", true)->first();
                if(isset($userGroups))
                {
                    return $this->sendError(trans('custom.company_has_default_user_group'), 500);
                }
            }
        
            $userGroups = $this->userGroupRepository->create($input);
            $transactionID = $userGroups->userGroupID;
        }

        $narrationVariables = $userGroups->description;
        $newValue = $userGroups->toArray();
        $this->auditLog($db, $transactionID, $uuid, "srp_erp_usergroups", $narrationVariables, $crudType, $newValue, $previousValue);

        return $this->sendResponse($userGroups, trans('custom.user_group_saved_successfully'));
    }

    /**
     * Display the specified UserGroup.
     * GET|HEAD /userGroups/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->findWithoutFail($id);

        if (empty($userGroup)) {
            return $this->sendError(trans('custom.user_group_not_found'));
        }

        return $this->sendResponse($userGroup->toArray(), trans('custom.user_group_retrieved_successfully'));
    }

    /**
     * Update the specified UserGroup in storage.
     * PUT/PATCH /userGroups/{id}
     *
     * @param  int $id
     * @param UpdateUserGroupAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserGroupAPIRequest $request)
    {
        $input = $request->all();

        $uuid = $input['tenant_uuid'] ?? 'local';
        $db = $input['db'] ?? '';

        if(isset($input['tenant_uuid'])) {
            unset($input['tenant_uuid']);
        }

        if(isset($input['db'])) {
            unset($input['db']);
        }

        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->findWithoutFail($id);

        if (empty($userGroup)) {
            return $this->sendError(trans('custom.user_group_not_found'));
        }

        $previousValue = $userGroup->toArray();
        $userGroup = $this->userGroupRepository->update($input, $id);
        $newValue = $userGroup->toArray();

        $narrationVariables = $userGroup->description;
        $this->auditLog($db, $userGroup->userGroupID, $uuid, "srp_erp_usergroups", $narrationVariables, "U", $newValue, $previousValue);

        return $this->sendResponse($userGroup->toArray(), trans('custom.usergroup_updated_successfully'));
    }

    /**
     * Remove the specified UserGroup from storage.
     * DELETE /userGroups/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $input = request()->all();
        $uuid = $input['tenant_uuid'] ?? 'local';
        $db = $input['db'] ?? '';

        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->findWithoutFail($id);

        if (empty($userGroup)) {
            return $this->sendError(trans('custom.user_group_not_found'));
        }

        $countUsers = EmployeeNavigation::where('userGroupID', $id)->get();
        if ($countUsers && count($countUsers) > 0){
            return $this->sendError(trans('custom.user_already_assigned', ['attribute' => count($countUsers)]));
        }

        $previousValue = $userGroup->toArray();
        $userGroup->navigationusergroup()->delete();
        $userGroup->usergroupemployee()->delete();
        $userGroup->update(['isActive' => 0, 'isDeleted' => 1, 'defaultYN' => 0]);

        $narrationVariables = $userGroup->description;
        $this->auditLog($db, $userGroup->userGroupID, $uuid, "srp_erp_usergroups", $narrationVariables, "D", [], $previousValue);

        return $this->sendResponse($id, trans('custom.user_group_deleted_successfully'));
    }

    public function getUserGroupByCompanyDatatable(Request $request)
    {
        $input = $request->all();
        $userGroup = $this->userGroupRepository->getUserGroupByCompanyDatatable($input);
        return $userGroup;
    }

    public function getUserGroup(Request $request)
    {
        $input = $request->all();
        $userGroup = $this->userGroupRepository->getUserGroup($input);
        return $this->sendResponse($userGroup, trans('custom.user_group_retrieved_successfully'));
    }

    public function assignDefaultAndUntick(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'] ?? null;
        $currentUserGroupID = $input['currentUserGroupID'] ?? null;
        $selectedUserGroupID = $input['selectedUserGroupID'] ?? null;

        if (!$companyID || !$currentUserGroupID || !$selectedUserGroupID) {
            return $this->sendError(trans('custom.unable_to_update'), 422);
        }

        DB::beginTransaction();
        try {
            $selectedGroup = UserGroup::where('companyID', $companyID)
                ->where('userGroupID', $selectedUserGroupID)
                ->first();
            $currentGroup = UserGroup::where('companyID', $companyID)
                ->where('userGroupID', $currentUserGroupID)
                ->first();

            if (!$selectedGroup || !$currentGroup) {
                DB::rollBack();
                return $this->sendError(trans('custom.user_group_not_found'), 404);
            }

            $selectedGroup->defaultYN = true;
            $selectedGroup->save();

            $currentGroup->defaultYN = false;
            $currentGroup->save();

            DB::commit();
            return $this->sendResponse([], trans('custom.user_group_updated_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError(trans('custom.unable_to_update'), 500);
        }
    }

    public function createDefaultAndUntick(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'] ?? null;
        $currentUserGroupID = $input['currentUserGroupID'] ?? null;
        $description = trim((string)($input['description'] ?? ''));

        if (!$companyID || !$currentUserGroupID || $description === '') {
            return $this->sendError(trans('custom.unable_to_add'), 422);
        }

        DB::beginTransaction();
        try {
            $currentGroup = UserGroup::where('companyID', $companyID)
                ->where('userGroupID', $currentUserGroupID)
                ->first();

            if (!$currentGroup) {
                DB::rollBack();
                return $this->sendError(trans('custom.user_group_not_found'), 404);
            }

            $newGroup = new UserGroup();
            $newGroup->companyID = $companyID;
            $newGroup->description = $description;
            $newGroup->defaultYN = true;
            $newGroup->isActive = 1;
            if (isset($input['isDelegation'])) {
                $newGroup->isDelegation = $input['isDelegation'];
            }
            $newGroup->save();

            $currentGroup->defaultYN = false;
            $currentGroup->save();

            DB::commit();
            return $this->sendResponse($newGroup->toArray(), trans('custom.user_group_saved_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError(trans('custom.unable_to_add'), 500);
        }
    }

}
