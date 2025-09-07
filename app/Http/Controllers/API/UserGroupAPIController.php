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
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserGroupController
 * @package App\Http\Controllers\API
 */
class UserGroupAPIController extends AppBaseController
{
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

       
    
        if (isset($request->userGroupID))
        {
            $id = $request->userGroupID;
             $userGroups = UserGroup::where("userGroupID", $id)->first();

            if (empty($userGroups)) {
                return $this->sendError(trans('custom.user_group_not_found'));
            }

            $employeeExists = EmployeeNavigation::where('userGroupID',$id)->count();
            
            if ($employeeExists > 0) {
                return $this->sendError(trans('custom.user_group_already_assigned_to_employees_cannot_ch'));
            }
            
            if($input["defaultYN"])
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
            $userGroups->defaultYN = $input["defaultYN"];

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
        
        }
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

        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->findWithoutFail($id);

        if (empty($userGroup)) {
            return $this->sendError(trans('custom.user_group_not_found'));
        }

        $userGroup = $this->userGroupRepository->update($input, $id);

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
        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->findWithoutFail($id);

        if (empty($userGroup)) {
            return $this->sendError(trans('custom.user_group_not_found'));
        }

        $countUsers = EmployeeNavigation::where('userGroupID', $id)->get();
        if ($countUsers && count($countUsers) > 0){
            return $this->sendError('There are ' .count($countUsers). ' users already assigned to this group. Remove users and try again');
        }

        $userGroup->navigationusergroup()->delete();
        $userGroup->usergroupemployee()->delete();
        $userGroup->update(['isActive' => 0, 'isDeleted' => 1, 'defaultYN' => 0]);

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

}
