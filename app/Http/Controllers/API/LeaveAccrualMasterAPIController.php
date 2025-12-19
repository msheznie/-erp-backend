<?php

namespace App\Http\Controllers\API;

use App\helper\CommonJobService;
use App\helper\LeaveAccrualService;
use App\Http\Requests\API\CreateLeaveAccrualMasterAPIRequest;
use App\Http\Requests\API\UpdateLeaveAccrualMasterAPIRequest;
use App\Jobs\LeaveAccrualInitiate;
use App\Models\Company;
use App\Models\LeaveAccrualMaster;
use App\Repositories\LeaveAccrualMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveAccrualMasterController
 * @package App\Http\Controllers\API
 */

class LeaveAccrualMasterAPIController extends AppBaseController
{
    /** @var  LeaveAccrualMasterRepository */
    private $leaveAccrualMasterRepository;

    public function __construct(LeaveAccrualMasterRepository $leaveAccrualMasterRepo)
    {
        $this->leaveAccrualMasterRepository = $leaveAccrualMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveAccrualMasters",
     *      summary="Get a listing of the LeaveAccrualMasters.",
     *      tags={"LeaveAccrualMaster"},
     *      description="Get all LeaveAccrualMasters",
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
     *                  @SWG\Items(ref="#/definitions/LeaveAccrualMaster")
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
        $this->leaveAccrualMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveAccrualMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveAccrualMasters = $this->leaveAccrualMasterRepository->all();

        return $this->sendResponse($leaveAccrualMasters->toArray(), trans('custom.leave_accrual_masters_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveAccrualMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveAccrualMasters",
     *      summary="Store a newly created LeaveAccrualMaster in storage",
     *      tags={"LeaveAccrualMaster"},
     *      description="Store LeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveAccrualMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveAccrualMaster")
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
     *                  ref="#/definitions/LeaveAccrualMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveAccrualMasterAPIRequest $request)
    {
        $input = $request->all();

        $leaveAccrualMaster = $this->leaveAccrualMasterRepository->create($input);

        return $this->sendResponse($leaveAccrualMaster->toArray(), trans('custom.leave_accrual_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveAccrualMasters/{id}",
     *      summary="Display the specified LeaveAccrualMaster",
     *      tags={"LeaveAccrualMaster"},
     *      description="Get LeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveAccrualMaster",
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
     *                  ref="#/definitions/LeaveAccrualMaster"
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
        /** @var LeaveAccrualMaster $leaveAccrualMaster */
        $leaveAccrualMaster = $this->leaveAccrualMasterRepository->findWithoutFail($id);

        if (empty($leaveAccrualMaster)) {
            return $this->sendError(trans('custom.leave_accrual_master_not_found'));
        }

        return $this->sendResponse($leaveAccrualMaster->toArray(), trans('custom.leave_accrual_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveAccrualMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveAccrualMasters/{id}",
     *      summary="Update the specified LeaveAccrualMaster in storage",
     *      tags={"LeaveAccrualMaster"},
     *      description="Update LeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveAccrualMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveAccrualMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveAccrualMaster")
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
     *                  ref="#/definitions/LeaveAccrualMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveAccrualMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveAccrualMaster $leaveAccrualMaster */
        $leaveAccrualMaster = $this->leaveAccrualMasterRepository->findWithoutFail($id);

        if (empty($leaveAccrualMaster)) {
            return $this->sendError(trans('custom.leave_accrual_master_not_found'));
        }

        $leaveAccrualMaster = $this->leaveAccrualMasterRepository->update($input, $id);

        return $this->sendResponse($leaveAccrualMaster->toArray(), trans('custom.leaveaccrualmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveAccrualMasters/{id}",
     *      summary="Remove the specified LeaveAccrualMaster from storage",
     *      tags={"LeaveAccrualMaster"},
     *      description="Delete LeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveAccrualMaster",
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
        /** @var LeaveAccrualMaster $leaveAccrualMaster */
        $leaveAccrualMaster = $this->leaveAccrualMasterRepository->findWithoutFail($id);

        if (empty($leaveAccrualMaster)) {
            return $this->sendError(trans('custom.leave_accrual_master_not_found'));
        }

        $leaveAccrualMaster->delete();

        return $this->sendSuccess('Leave Accrual Master deleted successfully');
    }

    function accrual_service_test(Request $request){
        //LeaveAccrualInitiate::dispatch(); return 'true';

        $policy = $request->input('policy');
        $dailyBasis = $request->input('dailyBasis');
        $dailyBasis = ($dailyBasis == 'true');

        $company = Company::selectRaw('companySystemID AS id, CompanyID AS code, CompanyName AS name')->find(1);

        $company = $company->toArray();
        $accrual_type_det = ['policy'=> $policy, 'dailyBasis'=> $dailyBasis];
        //echo '<pre>'; print_r($company); echo '</pre>'; //exit;

        $path = CommonJobService::get_specific_log_file('leave-accrual');
        Log::useFiles($path);

        $ser_per = new LeaveAccrualService($company, $accrual_type_det, []);
        $groups = $ser_per->prepare_for_accrual();

        echo '<pre>'; print_r($groups); echo '</pre>'; // exit;
        //return 'true';

        if(count($groups) == 0){
            return 'false';
        }

        foreach ($groups as $group){
            $ser = new LeaveAccrualService($company, $accrual_type_det, $group);
            $ser->create_accrual();
        }

        return 'true';
    }
}
