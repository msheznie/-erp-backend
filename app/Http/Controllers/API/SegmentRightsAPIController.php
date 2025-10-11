<?php
/**
 * =============================================
 * -- File Name : SegmentRightsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Segments
 * -- Author : Mohamed Zakeeul
 * -- Create date : 20 - February 2020
 * -- Description : This file contains the all CRUD for Segment Rights
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSegmentRightsAPIRequest;
use App\Http\Requests\API\UpdateSegmentRightsAPIRequest;
use App\Models\SegmentRights;
use App\Models\EmployeeNavigation;
use App\Repositories\SegmentRightsRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class SegmentRightsController
 * @package App\Http\Controllers\API
 */
class SegmentRightsAPIController extends AppBaseController
{
    /** @var  SegmentRightsRepository */
    private $segmentRightsRepository;
    private $userRepository;

    public function __construct(SegmentRightsRepository $segmentRightsRepo, UserRepository $userRepo)
    {
        $this->segmentRightsRepository = $segmentRightsRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SegmentRights.
     * GET|HEAD /segmentRights
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->segmentRightsRepository->pushCriteria(new RequestCriteria($request));
        $this->segmentRightsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $segmentRights = $this->segmentRightsRepository->all();

        return $this->sendResponse($segmentRights->toArray(), trans('custom.segment_rights_retrieved_successfully'));
    }

    /**
     * Store a newly created SegmentRights in storage.
     * POST /segmentRights
     *
     * @param CreateSegmentRightsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSegmentRightsAPIRequest $request)
    {

        $id = Auth::id();
        $user = $this->userRepository->findWithoutFail($id);

        $userID=$user->employee_id;
        $pcID=gethostname();
        $timestamp = date("Y-m-d h:i:s");

        $input = $request->all();
        $company = $input['companyID'];
        $segmentSelectedItems = isset($input['segmentSelectedItems'])?$input['segmentSelectedItems']:false;
        $employeeSystemID =  isset($input['employeeSystemID'])?$input['employeeSystemID']:false;
        $segement = array_pluck($segmentSelectedItems, 'id');
        $employee = array_pluck($employeeSystemID, 'employeeSystemID');


        $arr = [];
        if ($segement) {
            $i = 0;
            foreach ($segement as $seg) {
                $arr[$i]['companySystemID'] = $company;
                $arr[$i]['serviceLineSystemID'] = $seg;
                $i++;
            }
        } else {
            return $this->sendError(trans('custom.segment_required'), 500);
        }
        $finalArr = [];

       $allrecords= SegmentRights::select(DB::raw("CONCAT(serviceLineSystemID,'-',employeeSystemID) as keyValue"))->where('companySystemID',$company)->whereIn('serviceLineSystemID',$segement)->get();

        if ($employee) {
            $x = 0;
            foreach ($employee as $empID) {
                foreach ($arr as $item) {

                    $keyValue1 = $item['serviceLineSystemID'].'-'.$empID;

                    if(count($allrecords) == 0){
                        $item['employeeSystemID'] = $empID;
                        $item['createdUserSystemID']=$userID;
                        $item['createdPcID']=$pcID;
                        $item['createdDateTime']=$timestamp;
                        $item['timestamp']=$timestamp;
                        $finalArr[$x] = $item;
                        $x++;
                    }else{
                       $data =  $allrecords->toArray();
                        $keys = array_keys(array_column($data, 'keyValue'), $keyValue1);
                        $lineArrTotal = array_map(function ($k) use ($data) {
                            return $data[$k];
                        }, $keys);
                        if(empty($lineArrTotal)){
                            $item['employeeSystemID'] = $empID;
                            $item['createdUserSystemID']=$userID;
                            $item['createdPcID']=$pcID;
                            $item['createdDateTime']=$timestamp;
                            $item['timestamp']=$timestamp;
                            $finalArr[$x] = $item;
                            $x++;
                        }
                    }

                }
            }
        } else {
            return $this->sendError(trans('custom.employee_required'), 500);
        }

        if(!empty($finalArr)){
            $segmentRights = SegmentRights::insert($finalArr);
            return $this->sendResponse('', trans('custom.segment_rights_created_successfully'));
        }else{
            return $this->sendError( trans('custom.employee_already_exist'),500);
        }



    }

    /**
     * Display the specified SegmentRights.
     * GET|HEAD /segmentRights/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SegmentRights $segmentRights */
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            return $this->sendError(trans('custom.segment_rights_not_found'));
        }

        return $this->sendResponse($segmentRights->toArray(), trans('custom.segment_rights_retrieved_successfully'));
    }

    /**
     * Update the specified SegmentRights in storage.
     * PUT/PATCH /segmentRights/{id}
     *
     * @param  int $id
     * @param UpdateSegmentRightsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSegmentRightsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SegmentRights $segmentRights */
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            return $this->sendError(trans('custom.segment_rights_not_found'));
        }

        $segmentRights = $this->segmentRightsRepository->update($input, $id);

        return $this->sendResponse($segmentRights->toArray(), trans('custom.segmentrights_updated_successfully'));
    }

    /**
     * Remove the specified SegmentRights from storage.
     * DELETE /segmentRights/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SegmentRights $segmentRights */
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            return $this->sendError(trans('custom.segment_rights_not_found'));
        }

        $segmentRights->delete();

        return $this->sendResponse($id, trans('custom.segment_rights_deleted_successfully'));
    }

    public function getSegmentRightEmployees(Request $request)
    {
        $input = $request->all();
        $selectedCompanyID = isset($input['selectedCompanyID']) ? $input['selectedCompanyID'] : false;
        $servicelineSystemID = isset($input['servicelineSystemID']) ? $input['servicelineSystemID'] : false;
        $employeeSystemID = isset($input['employeeSystemID']) ? $input['employeeSystemID'] : false;


        $id = Auth::id();
        $user = $this->userRepository->findWithoutFail($id);
        $employee = EmployeeNavigation::select('companyID')->where('employeeSystemID', $user->employee_id)->get();
        $companiesByGroup = array_pluck($employee, 'companyID');


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        if ($companiesByGroup) {
            if ($selectedCompanyID) {
                $company = array($selectedCompanyID);
            } else {
                $company = $companiesByGroup;

                $globalCompanyID = (isset($input['globalCompanyID'])) ? $input['globalCompanyID'] : 0;
                $isGroup = \Helper::checkIsCompanyGroup($globalCompanyID);

                if($isGroup){
                    $company = \Helper::getGroupCompany($globalCompanyID);
                }else{
                    $company = [$globalCompanyID];
                }
            }

            $search = $request->input('search.value');
            $where = "";
            /*   if ($search) {
                   $where = " WHERE  (company LIKE '%$search%' OR  masterCompany LIKE '%$search%' OR doc LIKE '%$search%' OR GL_CODE_ID LIKE '%$search%' OR LEDGER_NAME LIKE '%$search%' OR NARRATION LIKE '%$search%' OR amount LIKE '%$search%' OR PRIOR_LEVEL LIKE '%$search%'  )";
               }*/

            $serviceline = DB::table('segmentrights')
                ->join('companymaster', 'companymaster.companySystemID', '=', 'segmentrights.companySystemID')
                ->join('serviceline', 'segmentrights.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                ->join('employees', 'segmentrights.employeeSystemID', '=', 'employees.employeeSystemID')
                ->whereIN('segmentrights.companySystemID', $company);

            if ($servicelineSystemID) {
                $serviceline->where('segmentrights.serviceLineSystemID', $servicelineSystemID);
            }
            if ($employeeSystemID) {
                $serviceline->where('segmentrights.employeeSystemID', $employeeSystemID);
            }


            $output = $serviceline->select('companymaster.CompanyID', 'companymaster.CompanyName', 'serviceline.ServiceLineCode', 'serviceline.ServiceLineDes', 'employees.empID', 'employees.empName', 'segmentRightsID')->get();
            $request->request->remove('search.value');

            $col[0] = $input['order'][0]['column'];
            $col[1] = $input['order'][0]['dir'];
            $request->request->remove('order');
            $data['order'] = [];

            $request->merge($data);

        } else {
            $output = [];
        }


        return \DataTables::of($output)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('Index', 'Index', "Index")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
