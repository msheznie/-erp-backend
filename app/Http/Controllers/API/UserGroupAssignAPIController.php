<?php
/**
=============================================
-- File Name : UserGroupAssignAPIController.php
-- Project Name : ERP
-- Module Name :  User group assign
-- Author : Mohamed Mubashir
-- Create date : 14 - March 2018
-- Description : This file contains assigning navigation from company to user group
-- REVISION HISTORY
-- Date: 05-September 2018 By: Fayas Description: Added new function getDetailsByDebitNote()
 **/
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserGroupAssignAPIRequest;
use App\Http\Requests\API\UpdateUserGroupAssignAPIRequest;
use App\Models\Company;
use App\Models\DocumentRestrictionAssign;
use App\Models\DocumentRestrictionPolicy;
use App\Models\Employee;
use App\Models\EmployeeNavigation;
use App\Models\UserGroupAssign;
use App\Repositories\UserGroupAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Response;

/**
 * Class UserGroupAssignController
 * @package App\Http\Controllers\API
 */
class UserGroupAssignAPIController extends AppBaseController
{
    /** @var  UserGroupAssignRepository */
    private $userGroupAssignRepository;
    private $userRepository;

    public function __construct(UserGroupAssignRepository $userGroupAssignRepo,UserRepository $userRepo)
    {
        $this->userGroupAssignRepository = $userGroupAssignRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the UserGroupAssign.
     * GET|HEAD /userGroupAssigns
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userGroupAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->userGroupAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userGroupAssigns = $this->userGroupAssignRepository->all();

        return $this->sendResponse($userGroupAssigns->toArray(), 'User Group Assigns retrieved successfully');
    }

    /**
     * Store a newly created UserGroupAssign in storage.
     * POST /userGroupAssigns
     *
     * @param CreateUserGroupAssignAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUserGroupAssignAPIRequest $request)
    {
        $input = $request->all();
        DB::table('srp_erp_navigationusergroupsetup')->where('companyID', '=', $input["companyID"])->where('userGroupID', '=', $input["userGroupID"])->delete();
        $navigation = array();
        if ($input["navigation"]) {
            foreach ($input["navigation"] as $val) {
                if ($val["isChecked"]) {
                    $navigation[] = array("navigationMenuID" => $val["navigationMenuID"], "description" => $val["description"], "userGroupID" => $input["userGroupID"], "companyID" => $input["companyID"], "masterID" => $val["masterID"], "url" => $val["url"], "pageID" => $val["pageID"], "pageTitle" => $val["pageTitle"], "pageIcon" => $val["pageIcon"], "levelNo" => $val["levelNo"], "sortOrder" => $val["sortOrder"], "isSubExist" => $val["isSubExist"], "readonly" => $val["readonly"], "create" => $val["create"], "update" => $val["update"], "delete" => $val["delete"], "print" => $val["print"], "export" => $val["export"],"isPortalYN" => $val["isPortalYN"],"externalLink" => $val["externalLink"]);
                }
                if (isset($val["children"])) {
                    $children1 = $val["children"];
                    foreach ($children1 as $val2) {
                        if ($val2["isChecked"]) {
                            $navigation[] = array("navigationMenuID" => $val2["navigationMenuID"], "description" => $val2["description"], "userGroupID" => $input["userGroupID"], "companyID" => $input["companyID"], "masterID" => $val2["masterID"], "url" => $val2["url"], "pageID" => $val2["pageID"], "pageTitle" => $val2["pageTitle"], "pageIcon" => $val2["pageIcon"], "levelNo" => $val2["levelNo"], "sortOrder" => $val2["sortOrder"], "isSubExist" => $val2["isSubExist"], "readonly" => $val2["readonly"], "create" => $val2["create"], "update" => $val2["update"], "delete" => $val2["delete"], "print" => $val2["print"], "export" => $val2["export"],"isPortalYN" => $val2["isPortalYN"],"externalLink" => $val2["externalLink"]);
                        }
                        if (isset($val2["children"])) {
                            $children2 = $val2["children"];
                            foreach ($children2 as $val3) {
                                if ($val3["isChecked"]) {
                                    $navigation[] = array("navigationMenuID" => $val3["navigationMenuID"], "description" => $val3["description"], "userGroupID" => $input["userGroupID"], "companyID" => $input["companyID"], "masterID" => $val3["masterID"], "url" => $val3["url"], "pageID" => $val3["pageID"], "pageTitle" => $val3["pageTitle"], "pageIcon" => $val3["pageIcon"], "levelNo" => $val3["levelNo"], "sortOrder" => $val3["sortOrder"], "isSubExist" => $val3["isSubExist"], "readonly" => $val3["readonly"], "create" => $val3["create"], "update" => $val3["update"], "delete" => $val3["delete"], "print" => $val3["print"], "export" => $val3["export"],"isPortalYN" => $val3["isPortalYN"],"externalLink" => $val3["externalLink"]);
                                }
                            }
                        }
                    }
                }
            }
        }
        $userGroupAssigns = UserGroupAssign::insert($navigation);


        DocumentRestrictionAssign::where('companySystemID', '=', $input["companyID"])
                                  ->where('userGroupID', '=', $input["userGroupID"])
                                  ->delete();

        $documentRestriction = array();

        $company = Company::find($input["companyID"]);
        $input["companyCode"] = null;
        if(!empty($company)){
            $input["companyCode"] = $company->CompanyID;
        }

        if ($input["restrictionPolicy"]) {
            foreach ($input["restrictionPolicy"] as $val) {
                if ($val["isChecked"]) {
                    $documentRestriction[] = array("documentRestrictionPolicyID" => $val["id"],
                                          "documentSystemID" => $val["documentSystemID"],
                                          "documentID" => $val["documentID"],
                                          "companySystemID" => $input["companyID"],
                                          "companyID" => $input["companyCode"],
                                          "userGroupID" => $input["userGroupID"]
                                         );
                }
            }

            DocumentRestrictionAssign::insert($documentRestriction);
        }
        return $this->sendResponse(array(), 'User Group Assign saved successfully');
    }

    /**
     * Display the specified UserGroupAssign.
     * GET|HEAD /userGroupAssigns/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UserGroupAssign $userGroupAssign */
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            return $this->sendError('User Group Assign not found');
        }

        return $this->sendResponse($userGroupAssign->toArray(), 'User Group Assign retrieved successfully');
    }

    /**
     * Update the specified UserGroupAssign in storage.
     * PUT/PATCH /userGroupAssigns/{id}
     *
     * @param  int $id
     * @param UpdateUserGroupAssignAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserGroupAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserGroupAssign $userGroupAssign */
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            return $this->sendError('User Group Assign not found');
        }

        $userGroupAssign = $this->userGroupAssignRepository->update($input, $id);

        return $this->sendResponse($userGroupAssign->toArray(), 'UserGroupAssign updated successfully');
    }

    /**
     * Remove the specified UserGroupAssign from storage.
     * DELETE /userGroupAssigns/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UserGroupAssign $userGroupAssign */
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            return $this->sendError('User Group Assign not found');
        }

        $userGroupAssign->delete();

        return $this->sendResponse($id, 'User Group Assign deleted successfully');
    }

    public function getUserGroupNavigation(Request $request)
    {
        $companyID = $request['companyID'];
        $userGroupID = $request['userGroupID'];
        //DB::enableQueryLog();
        $navigationMenu = DB::table('srp_erp_companynavigationmenus')
            ->select(DB::raw('srp_erp_companynavigationmenus.*,IFNULL(srp_erp_navigationusergroupsetup.readonly,0) as readonly,IFNULL(srp_erp_navigationusergroupsetup.`create`,0) as `create`,IFNULL(srp_erp_navigationusergroupsetup.`update`,0) as `update`,IFNULL(srp_erp_navigationusergroupsetup.`delete`,0) as `delete`,IFNULL(srp_erp_navigationusergroupsetup.`print`,0) as `print`,IFNULL(srp_erp_navigationusergroupsetup.`export`,0) as `export`,if(srp_erp_companynavigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID,1,0) as isChecked,if(srp_erp_navigationusergroupsetup.readonly = 1 && srp_erp_navigationusergroupsetup.`create` = 1 && srp_erp_navigationusergroupsetup.`update` = 1 && srp_erp_navigationusergroupsetup.`delete` = 1 && srp_erp_navigationusergroupsetup.`print` = 1&& srp_erp_navigationusergroupsetup.`export` = 1,1,0) as accessRightAll'))
            ->leftJoin('srp_erp_navigationusergroupsetup', function ($join) use ($companyID, $userGroupID) {
                $join->on('srp_erp_companynavigationmenus.navigationMenuID', '=', 'srp_erp_navigationusergroupsetup.navigationMenuID')
                    ->where('srp_erp_navigationusergroupsetup.companyID', '=', $companyID)->where('srp_erp_navigationusergroupsetup.userGroupID', '=', $userGroupID);
            })
            ->where('srp_erp_companynavigationmenus.companyID',$companyID)->get();
        //dd(DB::getQueryLog());
        $tree = buildTree($navigationMenu);

        $subMenus = DocumentRestrictionPolicy::with(['assign' => function($q)  use ($companyID, $userGroupID){
              $q->where('companySystemID',$companyID)
                 ->where('userGroupID',$userGroupID);
        }])->get();

        foreach ($subMenus as $subMenu){
            $subMenu->isChecked = false;
            if(count($subMenu['assign']) > 0){
                $subMenu->isChecked = true;
            }
            //$subMenu->count = count($subMenu['assign']);
        }

        $array = array('mainMenus' => $tree,'subMenus' => $subMenus);
        //$navigationMenu = DB::table("srp_erp_companynavigationmenus")->where("companyID",$companyID)->get();
        return $this->sendResponse($array, 'Record retrieved successfully');
    }


    public function checkUserGroupAccessRights(Request $request)
    {
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['employeeSystemID'];

        $userGroup = '';
        if($request['companyID'] != 'null') {
            $userGroup = EmployeeNavigation::where('employeeSystemID', $empId)
                ->where('companyID', $request['companyID'])
                ->first();
        }else{
            return $this->sendError('Company ID not found');
        }


        $userGroupID = $userGroup->userGroupID;
        $companyID = $request['companyID'];
        $navigationMenuID = $request['navigationMenuID'];

        $accessRights = [];
        //DB::enableQueryLog();
        $userGroupAssign = UserGroupAssign::where('companyID',$companyID)->where('navigationMenuID',$navigationMenuID)->where('userGroupID',$userGroupID)->first();
        //dd(DB::getQueryLog());
        if (empty($userGroupAssign)) {
            return $this->sendError('No access right found',401);
           // $accessRights = array('R' => false,'c' => false, 'E' => false, 'D' => false, 'P' => false);
        } else {
            $accessRights = array('R' => $userGroupAssign->readonly, 'C' => $userGroupAssign->create ,'E' => $userGroupAssign->update, 'D' => $userGroupAssign->delete, 'P' => $userGroupAssign->print,'Ex' => $userGroupAssign->export);
        }

        return $this->sendResponse($accessRights, 'Record retrieved successfully');
    }

}

function buildTree($elements, $parentId = null)
{
    $branch = array();
    foreach ($elements as $element) {
        if ($element->masterID == $parentId) {
            $children = buildTree($elements, $element->navigationMenuID);
            if ($children) {
                $element->children = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}
