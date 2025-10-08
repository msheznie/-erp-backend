<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseRightsAPIRequest;
use App\Http\Requests\API\UpdateWarehouseRightsAPIRequest;
use App\Models\WarehouseRights;
use App\Repositories\WarehouseRightsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Models\EmployeeNavigation;
use Illuminate\Support\Facades\DB;

/**
 * Class WarehouseRightsController
 * @package App\Http\Controllers\API
 */

class WarehouseRightsAPIController extends AppBaseController
{
    /** @var  WarehouseRightsRepository */
    private $warehouseRightsRepository;
    private $userRepository;

    public function __construct(WarehouseRightsRepository $warehouseRightsRepo, UserRepository $userRepo)
    {
        $this->warehouseRightsRepository = $warehouseRightsRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseRights",
     *      summary="Get a listing of the WarehouseRights.",
     *      tags={"WarehouseRights"},
     *      description="Get all WarehouseRights",
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
     *                  @SWG\Items(ref="#/definitions/WarehouseRights")
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
        $this->warehouseRightsRepository->pushCriteria(new RequestCriteria($request));
        $this->warehouseRightsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $warehouseRights = $this->warehouseRightsRepository->all();

        return $this->sendResponse($warehouseRights->toArray(), trans('custom.warehouse_rights_retrieved_successfully'));
    }

    /**
     * @param CreateWarehouseRightsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/warehouseRights",
     *      summary="Store a newly created WarehouseRights in storage",
     *      tags={"WarehouseRights"},
     *      description="Store WarehouseRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseRights that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseRights")
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
     *                  ref="#/definitions/WarehouseRights"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWarehouseRightsAPIRequest $request)
    {
        $id = Auth::id();
        $user = $this->userRepository->findWithoutFail($id);

        $userID = $user->employee_id;
        $pcID = gethostname();
        $timestamp = date("Y-m-d h:i:s");

        $input = $request->all();
        $company = $input['companyID'];
        $warehouseSelectedItems = isset($input['warehouseSelectedItems'])?$input['warehouseSelectedItems']:false;
        $employeeSystemID =  isset($input['employeeSystemID'])?$input['employeeSystemID']:false;
        $warehouse = array_pluck($warehouseSelectedItems, 'id');
        $employee = array_pluck($employeeSystemID, 'employeeSystemID');


        $arr = [];
        if ($warehouse) {
            $i = 0;
            foreach ($warehouse as $seg) {
                $arr[$i]['companySystemID'] = $company;
                $arr[$i]['wareHouseSystemCode'] = $seg;
                $i++;
            }
        } else {
            return $this->sendError(trans('custom.segment_required'), 500);
        }
        $finalArr = [];

       $allrecords= WarehouseRights::select(DB::raw("CONCAT(wareHouseSystemCode,'-',employeeSystemID) as keyValue"))->where('companySystemID',$company)->whereIn('wareHouseSystemCode',$warehouse)->get();

        if ($employee) {
            $x = 0;
            foreach ($employee as $empID) {
                foreach ($arr as $item) {

                    $keyValue1 = $item['wareHouseSystemCode'].'-'.$empID;

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
            $segmentRights = WarehouseRights::insert($finalArr);
            return $this->sendResponse('', trans('custom.warehouse_rights_created_successfully'));
        }else{
            return $this->sendError( trans('custom.employee_already_exist'),500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseRights/{id}",
     *      summary="Display the specified WarehouseRights",
     *      tags={"WarehouseRights"},
     *      description="Get WarehouseRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseRights",
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
     *                  ref="#/definitions/WarehouseRights"
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
        /** @var WarehouseRights $warehouseRights */
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            return $this->sendError(trans('custom.warehouse_rights_not_found'));
        }

        return $this->sendResponse($warehouseRights->toArray(), trans('custom.warehouse_rights_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateWarehouseRightsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/warehouseRights/{id}",
     *      summary="Update the specified WarehouseRights in storage",
     *      tags={"WarehouseRights"},
     *      description="Update WarehouseRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseRights",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseRights that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseRights")
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
     *                  ref="#/definitions/WarehouseRights"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWarehouseRightsAPIRequest $request)
    {
        $input = $request->all();

        /** @var WarehouseRights $warehouseRights */
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            return $this->sendError(trans('custom.warehouse_rights_not_found'));
        }

        $warehouseRights = $this->warehouseRightsRepository->update($input, $id);

        return $this->sendResponse($warehouseRights->toArray(), trans('custom.warehouserights_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/warehouseRights/{id}",
     *      summary="Remove the specified WarehouseRights from storage",
     *      tags={"WarehouseRights"},
     *      description="Delete WarehouseRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseRights",
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
        /** @var WarehouseRights $warehouseRights */
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            return $this->sendError(trans('custom.warehouse_rights_not_found'));
        }

        $warehouseRights->delete();

        return $this->sendResponse($id, trans('custom.warehouse_rights_deleted_successfully'));
    }

    public function getWarehouseRightEmployees(Request $request)
    {
        $input = $request->all();
        $selectedCompanyID = isset($input['selectedCompanyID']) ? $input['selectedCompanyID'] : false;
        $warehouseSystemID = isset($input['warehouseSystemID']) ? $input['warehouseSystemID'] : false;
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

            $serviceline = DB::table('warehouserights')
                ->join('companymaster', 'companymaster.companySystemID', '=', 'warehouserights.companySystemID')
                ->join('warehousemaster', 'warehouserights.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
                ->join('employees', 'warehouserights.employeeSystemID', '=', 'employees.employeeSystemID')
                ->whereIN('warehouserights.companySystemID', $company);

            if ($warehouseSystemID) {
                $serviceline->where('warehouserights.wareHouseSystemCode', $warehouseSystemID);
            }
            if ($employeeSystemID) {
                $serviceline->where('warehouserights.employeeSystemID', $employeeSystemID);
            }


            $output = $serviceline->select('companymaster.CompanyID', 'companymaster.CompanyName', 'warehousemaster.wareHouseCode', 'warehousemaster.wareHouseDescription', 'employees.empID', 'employees.empName', 'warehouseRightsID')->get();
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
