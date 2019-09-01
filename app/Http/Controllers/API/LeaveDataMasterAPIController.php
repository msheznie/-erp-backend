<?php
/**
 * =============================================
 * -- File Name : LeaveDataMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 29 - August 2019
 * -- Description : This file contains the all related functions for leave appliation
 * -- REVISION HISTORY
 * -- Date: 29- August 2019 By: Rilwan Description: Added new function getExpenseClaim()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateLeaveDataMasterAPIRequest;
use App\Http\Requests\API\UpdateLeaveDataMasterAPIRequest;
use App\Models\LeaveDataMaster;
use App\Models\QryLeavePosted;
use App\Repositories\LeaveDataMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveDataMasterController
 * @package App\Http\Controllers\API
 */

class LeaveDataMasterAPIController extends AppBaseController
{
    /** @var  LeaveDataMasterRepository */
    private $leaveDataMasterRepository;

    public function __construct(LeaveDataMasterRepository $leaveDataMasterRepo)
    {
        $this->leaveDataMasterRepository = $leaveDataMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataMasters",
     *      summary="Get a listing of the LeaveDataMasters.",
     *      tags={"LeaveDataMaster"},
     *      description="Get all LeaveDataMasters",
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
     *                  @SWG\Items(ref="#/definitions/LeaveDataMaster")
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
        $this->leaveDataMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveDataMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveDataMasters = $this->leaveDataMasterRepository->all();

        return $this->sendResponse($leaveDataMasters->toArray(), 'Leave Data Masters retrieved successfully');
    }

    /**
     * @param CreateLeaveDataMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveDataMasters",
     *      summary="Store a newly created LeaveDataMaster in storage",
     *      tags={"LeaveDataMaster"},
     *      description="Store LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataMaster")
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
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveDataMasterAPIRequest $request)
    {
        $input = $request->all();

        $leaveDataMaster = $this->leaveDataMasterRepository->create($input);

        return $this->sendResponse($leaveDataMaster->toArray(), 'Leave Data Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataMasters/{id}",
     *      summary="Display the specified LeaveDataMaster",
     *      tags={"LeaveDataMaster"},
     *      description="Get LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
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
     *                  ref="#/definitions/LeaveDataMaster"
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
        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        return $this->sendResponse($leaveDataMaster->toArray(), 'Leave Data Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLeaveDataMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveDataMasters/{id}",
     *      summary="Update the specified LeaveDataMaster in storage",
     *      tags={"LeaveDataMaster"},
     *      description="Update LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataMaster")
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
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveDataMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        $leaveDataMaster = $this->leaveDataMasterRepository->update($input, $id);

        return $this->sendResponse($leaveDataMaster->toArray(), 'LeaveDataMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveDataMasters/{id}",
     *      summary="Remove the specified LeaveDataMaster from storage",
     *      tags={"LeaveDataMaster"},
     *      description="Delete LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
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
        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        $leaveDataMaster->delete();

        return $this->sendResponse($id, 'Leave Data Master deleted successfully');
    }

    public function getLeaveHistory()
    {
        $emp_id = Helper::getEmployeeID();
        $leaveHistory =QryLeavePosted::select('createDate','leaveDataMasterCode','leavetype','Manager','Type','confirmedYN',
            'approvedYN','confirmedYN','leavedatamasterID','LeaveApplicationTypeID')
            ->where('empID',$emp_id)
            ->get();
        return $this->sendResponse($leaveHistory->toArray(), 'Leave history details retrieved successfully');
    }
}
