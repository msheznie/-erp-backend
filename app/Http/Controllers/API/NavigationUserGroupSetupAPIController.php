<?php
/**
=============================================
-- File Name : NavigationUserGroupSetupAPIController.php
-- Project Name : ERP
-- Module Name :  Navigation User Group Setup
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for  Navigation User Group Setup
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNavigationUserGroupSetupAPIRequest;
use App\Http\Requests\API\UpdateNavigationUserGroupSetupAPIRequest;
use App\Models\NavigationUserGroupSetup;
use App\Repositories\NavigationUserGroupSetupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Criteria\FilterParentMenuCriteria;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Models\UserGroup;
/**
 * Class NavigationUserGroupSetupController
 * @package App\Http\Controllers\API
 */

class NavigationUserGroupSetupAPIController extends AppBaseController
{
    /** @var  NavigationUserGroupSetupRepository */
    private $navigationUserGroupSetupRepository;

    private $userRepository;

    public function __construct(NavigationUserGroupSetupRepository $navigationUserGroupSetupRepo,UserRepository $userRepo)
    {
        $this->navigationUserGroupSetupRepository = $navigationUserGroupSetupRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the NavigationUserGroupSetup.
     * GET|HEAD /navigationUserGroupSetups
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->navigationUserGroupSetupRepository->pushCriteria(new RequestCriteria($request));
        $this->navigationUserGroupSetupRepository->pushCriteria(new LimitOffsetCriteria($request));
        //$navigationUserGroupSetups = $this->navigationUserGroupSetupRepository->all();

        $navigationUserGroupSetups = $this->navigationUserGroupSetupRepository
                                                ->paginate(20);

        return $this->sendResponse($navigationUserGroupSetups->toArray(), 'Navigation User Group Setups retrieved successfully');
    }


    /**
     * Display a listing of Navigation for company.
     * GET|HEAD /userMenu
     *
     * @param Request $request
     * @return Response
     */
    public function userMenu(Request $request){

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['employeeSystemID'];

        $userGroup = DB::table('srp_erp_employeenavigation')
                         ->where('employeeSystemID',$empId)
                         ->where('companyID',$request['companyId'])
                         ->pluck('userGroupID');

               

        if($userGroup){
            $request['userGroupId'] = $userGroup;
            //$this->navigationUserGroupSetupRepository->pushCriteria(new RequestCriteria($request));
            $this->navigationUserGroupSetupRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->navigationUserGroupSetupRepository->pushCriteria(new FilterParentMenuCriteria($request));
            return $navigationUserGroupSetups = $this->navigationUserGroupSetupRepository->paginate(50);
        }else{

            $userGroupExist = UserGroup::where('companyID',$request['companyId'])
                                         ->where('defaultYN',true)       
                                         ->first();
            if($userGroupExist)
            {
            $request['userGroupId'] = [$userGroupExist->userGroupID];
            //$this->navigationUserGroupSetupRepository->pushCriteria(new RequestCriteria($request));
            $this->navigationUserGroupSetupRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->navigationUserGroupSetupRepository->pushCriteria(new FilterParentMenuCriteria($request));
            return $navigationUserGroupSetups = $this->navigationUserGroupSetupRepository->paginate(50);
            }  
             else
             {
                return $this->sendResponse([],'not found any menu');
             }                          
           
        }
    }

    public function getUserMenu(Request $request){

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['employeeSystemID'];

        $userGroup = DB::table('srp_erp_employeenavigation')
            ->where('employeeSystemID',$empId)
            ->where('companyID',$request['companyId'])
            ->first();

        if($userGroup){
            $companyId = $request['companyId'];
            $userGroupId = $userGroup->userGroupID;
            $menus = NavigationUserGroupSetup::whereNotNull('masterID')
                                                ->whereNotNull('url')
                                                ->whereIn('isPortalYN',array(0))
                                                ->where('userGroupID',$userGroupId)
                                                ->where('companyID',$companyId)
                                                ->where('isActive',1)
                                                ->with(['child' => function ($query) use($companyId,$userGroupId) {
                                                    $query->where('userGroupID',$userGroupId)
                                                        ->where('companyID',$companyId)
                                                        ->with(['child' => function ($query) use($companyId,$userGroupId) {
                                                            $query->where('userGroupID',$userGroupId)
                                                                ->where('companyID',$companyId)
                                                                ->orderBy("sortOrder","asc");
                                                        }])
                                                        ->orderBy("sortOrder","asc");
                                                }])
                                                ->orderBy("sortOrder","asc")
                                                ->select('navigationMenuID','pageTitle','url')
                                                ->get();

            return $this->sendResponse($menus,'successfully retrieved menus');

        }else{
            return $this->sendResponse([],'not found any menu');
        }
    }


    /**
     * Store a newly created NavigationUserGroupSetup in storage.
     * POST /navigationUserGroupSetups
     *
     * @param CreateNavigationUserGroupSetupAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateNavigationUserGroupSetupAPIRequest $request)
    {
        $input = $request->all();

        $navigationUserGroupSetups = $this->navigationUserGroupSetupRepository->create($input);

        return $this->sendResponse($navigationUserGroupSetups->toArray(), 'Navigation User Group Setup saved successfully');
    }

    /**
     * Display the specified NavigationUserGroupSetup.
     * GET|HEAD /navigationUserGroupSetups/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var NavigationUserGroupSetup $navigationUserGroupSetup */
        $navigationUserGroupSetup = $this->navigationUserGroupSetupRepository->findWithoutFail($id);

        if (empty($navigationUserGroupSetup)) {
            return $this->sendError('Navigation User Group Setup not found');
        }

        return $this->sendResponse($navigationUserGroupSetup->toArray(), 'Navigation User Group Setup retrieved successfully');
    }

    /**
     * Update the specified NavigationUserGroupSetup in storage.
     * PUT/PATCH /navigationUserGroupSetups/{id}
     *
     * @param  int $id
     * @param UpdateNavigationUserGroupSetupAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNavigationUserGroupSetupAPIRequest $request)
    {
        $input = $request->all();

        /** @var NavigationUserGroupSetup $navigationUserGroupSetup */
        $navigationUserGroupSetup = $this->navigationUserGroupSetupRepository->findWithoutFail($id);

        if (empty($navigationUserGroupSetup)) {
            return $this->sendError('Navigation User Group Setup not found');
        }

        $navigationUserGroupSetup = $this->navigationUserGroupSetupRepository->update($input, $id);

        return $this->sendResponse($navigationUserGroupSetup->toArray(), 'NavigationUserGroupSetup updated successfully');
    }

    /**
     * Remove the specified NavigationUserGroupSetup from storage.
     * DELETE /navigationUserGroupSetups/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var NavigationUserGroupSetup $navigationUserGroupSetup */
        $navigationUserGroupSetup = $this->navigationUserGroupSetupRepository->findWithoutFail($id);

        if (empty($navigationUserGroupSetup)) {
            return $this->sendError('Navigation User Group Setup not found');
        }

        $navigationUserGroupSetup->delete();

        return $this->sendResponse($id, 'Navigation User Group Setup deleted successfully');
    }

}
