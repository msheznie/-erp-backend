<?php
/**
 * =============================================
 * -- File Name : CounterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Counter
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - January 2019
 * -- Description : This file contains the all CRUD for Counter
 * -- REVISION HISTORY
 * -- Date: 07 - January 2019 By: Fayas Description: Added new function getCountersByCompany(),getCounterFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCounterAPIRequest;
use App\Http\Requests\API\UpdateCounterAPIRequest;
use App\Models\Counter;
use App\Models\WarehouseMaster;
use App\Repositories\CounterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CounterController
 * @package App\Http\Controllers\API
 */

class CounterAPIController extends AppBaseController
{
    /** @var  CounterRepository */
    private $counterRepository;

    public function __construct(CounterRepository $counterRepo)
    {
        $this->counterRepository = $counterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/counters",
     *      summary="Get a listing of the Counters.",
     *      tags={"Counter"},
     *      description="Get all Counters",
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
     *                  @SWG\Items(ref="#/definitions/Counter")
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
        $this->counterRepository->pushCriteria(new RequestCriteria($request));
        $this->counterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $counters = $this->counterRepository->all();

        return $this->sendResponse($counters->toArray(), 'Counters retrieved successfully');
    }

    /**
     * @param CreateCounterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/counters",
     *      summary="Store a newly created Counter in storage",
     *      tags={"Counter"},
     *      description="Store Counter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Counter that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Counter")
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
     *                  ref="#/definitions/Counter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCounterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = array(
            'wareHouseID.required'   => 'The outlet field is required.'
        );

        $validator = \Validator::make($input, [
            'counterCode' => 'required',
            'companySystemID' => 'required',
            'counterName' => 'required',
            'wareHouseID' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $checkCounterCode = Counter::where('companySystemID',$input['companySystemID'])
                                     ->where('wareHouseID',$input['wareHouseID'])
                                     ->where('counterCode',$input['counterCode'])
                                     ->count();

        if($checkCounterCode > 0){
            return $this->sendError('Counter code already exists in selected outlet', 500 );
        }

        $input['companyID'] = \Helper::getCompanyById($input['companySystemID']);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdUserName'] = $employee->empName;
        $counters = $this->counterRepository->create($input);
        return $this->sendResponse($counters->toArray(), 'Counter saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/counters/{id}",
     *      summary="Display the specified Counter",
     *      tags={"Counter"},
     *      description="Get Counter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Counter",
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
     *                  ref="#/definitions/Counter"
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
        /** @var Counter $counter */
        $counter = $this->counterRepository->findWithoutFail($id);

        if (empty($counter)) {
            return $this->sendError('Counter not found');
        }

        return $this->sendResponse($counter->toArray(), 'Counter retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCounterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/counters/{id}",
     *      summary="Update the specified Counter in storage",
     *      tags={"Counter"},
     *      description="Update Counter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Counter",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Counter that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Counter")
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
     *                  ref="#/definitions/Counter"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCounterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['warehouse']);
        $input = $this->convertArrayToValue($input);
        /** @var Counter $counter */
        $counter = $this->counterRepository->findWithoutFail($id);

        if (empty($counter)) {
            return $this->sendError('Counter not found');
        }

        $messages = array(
            'wareHouseID.required'   => 'The outlet field is required.'
        );

        $validator = \Validator::make($input, [
            'counterCode' => 'required',
            'companySystemID' => 'required',
            'counterName' => 'required',
            'wareHouseID' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $checkCounterCode = Counter::where('companySystemID',$input['companySystemID'])
            ->where('wareHouseID',$input['wareHouseID'])
            ->where('counterID','!=',$input['counterID'])
            ->where('counterCode',$input['counterCode'])
            ->count();

        if($checkCounterCode > 0){
            return $this->sendError('Counter code already exists in selected outlet', 500 );
        }

        $input['companyID'] = \Helper::getCompanyById($input['companySystemID']);
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUserName'] = $employee->empName;
        $input['timestamp'] = now();

        $counter = $this->counterRepository->update($input, $id);

        return $this->sendResponse($counter->toArray(), 'Counter updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/counters/{id}",
     *      summary="Remove the specified Counter from storage",
     *      tags={"Counter"},
     *      description="Delete Counter",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Counter",
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
        /** @var Counter $counter */
        $counter = $this->counterRepository->findWithoutFail($id);

        if (empty($counter)) {
            return $this->sendError('Counter not found');
        }

        // Have to check counter shift

        $counter->delete();

        return $this->sendResponse($id, 'Counter deleted successfully');
    }

    public function getCountersByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('wareHouseID'));

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

        $counters = Counter::whereIn('companySystemID', $subCompanies)
                            ->with(['warehouse']);

        if (array_key_exists('wareHouseID', $input)) {
            if ($input['wareHouseID'] && !is_null($input['wareHouseID'])) {
                $counters->where('wareHouseID', $input['wareHouseID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $counters = $counters->where(function ($query) use ($search) {
                $query->where('counterName', 'like', "%{$search}%")
                    ->orWhere('counterCode', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q1) use ($search) {
                        $q1->where('wareHouseCode', 'like', "%{$search}%")
                           ->orWhere('wareHouseDescription', 'like', "%{$search}%");
                    });
            });
        }

        return \DataTables::of($counters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('counterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCounterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $outlets = WarehouseMaster::where("companySystemID", $companyId)
            ->where('isPosLocation',-1)
            ->when(request('isFilter') == 0, function ($q) {
                return $q->where('isActive', 1);
            })
            ->get();

        $output = array(
            'outlets' => $outlets
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
