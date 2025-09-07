<?php
/**
 * =============================================
 * -- File Name : LeaveDataMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 01 - September 2019
 * -- Description : This file contains the all related functions for leave appliation
 * -- REVISION HISTORY
 * -- Date: 01 - September 2019 By: Rilwan Description: Added new function getLeaveTypes()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateLeaveMasterAPIRequest;
use App\Http\Requests\API\UpdateLeaveMasterAPIRequest;
use App\Models\CalenderMaster;
use App\Models\LeaveDataMaster;
use App\Models\LeaveMaster;
use App\Models\QryLeavesAccrued;
use App\Models\QryLeavesApplied;
use App\Repositories\LeaveMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveMasterController
 * @package App\Http\Controllers\API
 */

class LeaveMasterAPIController extends AppBaseController
{
    /** @var  LeaveMasterRepository */
    private $leaveMasterRepository;

    public function __construct(LeaveMasterRepository $leaveMasterRepo)
    {
        $this->leaveMasterRepository = $leaveMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveMasters",
     *      summary="Get a listing of the LeaveMasters.",
     *      tags={"LeaveMaster"},
     *      description="Get all LeaveMasters",
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
     *                  @SWG\Items(ref="#/definitions/LeaveMaster")
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
        $this->leaveMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveMasters = $this->leaveMasterRepository->all();

        return $this->sendResponse($leaveMasters->toArray(), trans('custom.leave_masters_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveMasters",
     *      summary="Store a newly created LeaveMaster in storage",
     *      tags={"LeaveMaster"},
     *      description="Store LeaveMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveMaster")
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
     *                  ref="#/definitions/LeaveMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveMasterAPIRequest $request)
    {
        $input = $request->all();

        $leaveMaster = $this->leaveMasterRepository->create($input);

        return $this->sendResponse($leaveMaster->toArray(), trans('custom.leave_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveMasters/{id}",
     *      summary="Display the specified LeaveMaster",
     *      tags={"LeaveMaster"},
     *      description="Get LeaveMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveMaster",
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
     *                  ref="#/definitions/LeaveMaster"
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
        /** @var LeaveMaster $leaveMaster */
        $leaveMaster = $this->leaveMasterRepository->findWithoutFail($id);

        if (empty($leaveMaster)) {
            return $this->sendError(trans('custom.leave_master_not_found'));
        }

        return $this->sendResponse($leaveMaster->toArray(), trans('custom.leave_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveMasters/{id}",
     *      summary="Update the specified LeaveMaster in storage",
     *      tags={"LeaveMaster"},
     *      description="Update LeaveMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveMaster")
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
     *                  ref="#/definitions/LeaveMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveMaster $leaveMaster */
        $leaveMaster = $this->leaveMasterRepository->findWithoutFail($id);

        if (empty($leaveMaster)) {
            return $this->sendError(trans('custom.leave_master_not_found'));
        }

        $leaveMaster = $this->leaveMasterRepository->update($input, $id);

        return $this->sendResponse($leaveMaster->toArray(), trans('custom.leavemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveMasters/{id}",
     *      summary="Remove the specified LeaveMaster from storage",
     *      tags={"LeaveMaster"},
     *      description="Delete LeaveMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveMaster",
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
        /** @var LeaveMaster $leaveMaster */
        $leaveMaster = $this->leaveMasterRepository->findWithoutFail($id);

        if (empty($leaveMaster)) {
            return $this->sendError(trans('custom.leave_master_not_found'));
        }

        $leaveMaster->delete();

        return $this->sendResponse($id, trans('custom.leave_master_deleted_successfully'));
    }

    public function getLeaveTypes()
    {
        $leaveMasters =LeaveMaster::select('leavemasterID','leavetype')->get();
        return $this->sendResponse($leaveMasters->toArray(), trans('custom.leave_type_details_retrieved_successfully'));
    }


}
