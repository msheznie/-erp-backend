<?php
/**
=============================================
-- File Name : UserAPIController.php
-- Project Name : ERP
-- Module Name :  User
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for User
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as checkUser(),userCompanies()
-- Date: 10-December-2018 By: Shahmy loginwithToken() function created to validate and login from portal token
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Services\WebPushNotificationService;
use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use App\Models\EmployeeNavigation;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\EmployeeRepository;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;
    private $employeeRepository;
    private $companyRepository;

    public function __construct(UserRepository $userRepo, EmployeeRepository $empRepo, CompanyRepository $comRepo)
    {
        $this->userRepository = $userRepo;
        $this->employeeRepository = $empRepo;
        $this->companyRepository = $comRepo;
    }

    /**
     * Display a listing of the User.
     * GET|HEAD /users
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));
        $users = $this->userRepository->all();

        return $this->sendResponse($users->toArray(), trans('custom.users_retrieved_successfully'));
    }

    /**
     * check user authentications
     * GET /checkUser
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkUser(Request $request)
    {

        try {
            $id = Auth::id();
            $user = Auth::user();
            if (empty($user)) {
                return $this->sendError(trans('custom.user_not_found'));
            }
            return $this->sendResponse([], trans('custom.user_retrieved_successfully'));

        } catch (\Exception $exception) {
            return $this->sendError(trans('custom.user_not_found'));
        }
    }

    /**
     * get user details
     * POST /userCompanies
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userCompanies(Request $request)
    {

        try {
            $id = Auth::id();

            $user = $this->userRepository->with(['employee' => function ($q) {
                //$q->with('companies');
            }
            ])->findWithoutFail($id);

            $empId = $user->employee['empID'];

            $employeeSystemID = $user->employee['employeeSystemID'];

            $companies = EmployeeNavigation::where('employeeSystemID',$employeeSystemID)
                                                  ->with(['company'])
                                                  ->get();

            $setCompanies = array();

            foreach ($companies as $com) {
                if (!empty($com->company)) {
                    $label = $com->company->CompanyID . " - " . $com->company->CompanyName;

                    if (mb_strlen($label, 'utf-8') > 26) {
                        $label = mb_substr($label, 0, 25, 'utf-8') . '...';
                    }

                    $temp = array('value' => $com->company->companySystemID, 'label' => $label);
                    array_push($setCompanies, $temp);
                }
            }

           /* $companies = DB::table('employeesdepartments')
                //->select(DB::raw('employeesdepartments.companyId as value, ANY_VALUE(companymaster.CompanyName) as label'))
                ->select(DB::raw('ANY_VALUE(companymaster.companySystemID) as value, CONCAT(employeesdepartments.companyId , " - ", ANY_VALUE(companymaster.CompanyName)) as label'))
                ->join('companymaster', 'employeesdepartments.companyId', '=', 'companymaster.CompanyID')
                ->where('employeesdepartments.employeeID', $empId)
                ->where('employeesdepartments.dischargedYN', 0)
                ->whereNotNull('employeesdepartments.companyId')
                ->groupBy('employeesdepartments.companyId')
                ->get();*/


            $data = array('companies' => $setCompanies,'default_company' =>  $user->employee['empCompanySystemID']);


            return $data;

        } catch (\Exception $ex) {
            return $ex->getMessage();
        }

        return $this->sendResponse($user->toArray(), trans('custom.user_compnies_retrieved_successfully'));
    }

    /**
     * Store a newly created User in storage.
     * POST /users
     *
     * @param CreateUserAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUserAPIRequest $request)
    {
        $input = $request->all();

        $users = $this->userRepository->create($input);

        return $this->sendResponse($users->toArray(), trans('custom.user_saved_successfully'));
    }

    /**
     * Display the specified User.
     * GET|HEAD /users/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError(trans('custom.user_not_found'));
        }

        return $this->sendResponse($user->toArray(), trans('custom.user_retrieved_successfully'));
    }

    /**
     * Update the specified User in storage.
     * PUT/PATCH /users/{id}
     *
     * @param  int $id
     * @param UpdateUserAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError(trans('custom.user_not_found'));
        }

        $user = $this->userRepository->update($input, $id);

        return $this->sendResponse($user->toArray(), trans('custom.user_updated_successfully'));
    }

    /**
     * Remove the specified User from storage.
     * DELETE /users/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendError(trans('custom.user_not_found'));
        }

        $user->delete();

        return $this->sendResponse($id, trans('custom.user_deleted_successfully'));
    }

    public function loginwithToken(request $request){
      $users=  User::with(['employee'])->where('login_token',$request->id)->first();
      if($users){
          $data['uname'] = $users->email;
          $data['pw'] = null;
          $this->userRepository->update(['login_token'=>NULL],$users->id);
          return $this->sendResponse($data, trans('custom.user_retrieved_successfully'));
      }else{
          return $this->sendError('', 500);
      }

    }

    public function getCurrentUserInfo(Request $request){
        $output = Helper::getEmployeeInfo();

        //filter the output to only include the fields that are needed
        $output = $output->only([
            'empID',
            'empCompanySystemID',
            'empCompanyID',
            'empFullName',
            'empName',
            'empEmail',
            'empUserName',
            'language',
            'profilepic',
            'user_data',
        ]);

        /*if($output->profilepic){
            $output->profilepic->profileImage = public_path().$output->profilepic->profileImage;
        }
        $output["imagePath"] =  Illuminate\Support\Facades\Storage::disk('public')->temporaryUrl('noEmployeeImage.JPG', now()->addMinutes(5));*/
        return $this->sendResponse($output, trans('custom.record_retrieve', ['attribute' => trans('custom.user')]));
    }

    public function getNotifications(Request $request)
    {
        $notificationData = WebPushNotificationService::getUserNotifications();

        return $this->sendResponse($notificationData, "Notifications retrieved successfully");
    } 


    public function updateNotification(Request $request)
    {
        $input = $request->all();

        $notificationData = WebPushNotificationService::updateNotifications($input);

        $processNotification = WebPushNotificationService::processnotificationData($input);

        return $this->sendResponse($processNotification, "Notifications updated successfully");
    }

    public function getAllNotifications(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $notificationData = WebPushNotificationService::getAllNotifications();

        return \DataTables::of($notificationData)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
