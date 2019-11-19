<?php
/**
=============================================
-- File Name : HRMSLeaveAccrualMasterAPIController.php
-- Project Name : ERP
-- Module Name :  LEAVE
-- Author : Mohamed Rilwan
-- Create date : 19 - November 2019
-- Description :
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSLeaveAccrualMasterAPIRequest;
use App\Http\Requests\API\UpdateHRMSLeaveAccrualMasterAPIRequest;
use App\Models\HRMSLeaveAccrualMaster;
use App\Repositories\HRMSLeaveAccrualMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSLeaveAccrualMasterController
 * @package App\Http\Controllers\API
 */

class HRMSLeaveAccrualMasterAPIController extends AppBaseController
{
    /** @var  HRMSLeaveAccrualMasterRepository */
    private $hRMSLeaveAccrualMasterRepository;

    public function __construct(HRMSLeaveAccrualMasterRepository $hRMSLeaveAccrualMasterRepo)
    {
        $this->hRMSLeaveAccrualMasterRepository = $hRMSLeaveAccrualMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSLeaveAccrualMasters",
     *      summary="Get a listing of the HRMSLeaveAccrualMasters.",
     *      tags={"HRMSLeaveAccrualMaster"},
     *      description="Get all HRMSLeaveAccrualMasters",
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
     *                  @SWG\Items(ref="#/definitions/HRMSLeaveAccrualMaster")
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
        $this->hRMSLeaveAccrualMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSLeaveAccrualMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSLeaveAccrualMasters = $this->hRMSLeaveAccrualMasterRepository->all();

        return $this->sendResponse($hRMSLeaveAccrualMasters->toArray(), 'H R M S Leave Accrual Masters retrieved successfully');
    }

    /**
     * @param CreateHRMSLeaveAccrualMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSLeaveAccrualMasters",
     *      summary="Store a newly created HRMSLeaveAccrualMaster in storage",
     *      tags={"HRMSLeaveAccrualMaster"},
     *      description="Store HRMSLeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSLeaveAccrualMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSLeaveAccrualMaster")
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
     *                  ref="#/definitions/HRMSLeaveAccrualMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSLeaveAccrualMasterAPIRequest $request)
    {
        $input = $request->all();

        $hRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepository->create($input);

        return $this->sendResponse($hRMSLeaveAccrualMaster->toArray(), 'H R M S Leave Accrual Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSLeaveAccrualMasters/{id}",
     *      summary="Display the specified HRMSLeaveAccrualMaster",
     *      tags={"HRMSLeaveAccrualMaster"},
     *      description="Get HRMSLeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualMaster",
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
     *                  ref="#/definitions/HRMSLeaveAccrualMaster"
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
        /** @var HRMSLeaveAccrualMaster $hRMSLeaveAccrualMaster */
        $hRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualMaster)) {
            return $this->sendError('H R M S Leave Accrual Master not found');
        }

        return $this->sendResponse($hRMSLeaveAccrualMaster->toArray(), 'H R M S Leave Accrual Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateHRMSLeaveAccrualMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSLeaveAccrualMasters/{id}",
     *      summary="Update the specified HRMSLeaveAccrualMaster in storage",
     *      tags={"HRMSLeaveAccrualMaster"},
     *      description="Update HRMSLeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSLeaveAccrualMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSLeaveAccrualMaster")
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
     *                  ref="#/definitions/HRMSLeaveAccrualMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSLeaveAccrualMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSLeaveAccrualMaster $hRMSLeaveAccrualMaster */
        $hRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualMaster)) {
            return $this->sendError('H R M S Leave Accrual Master not found');
        }

        $hRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepository->update($input, $id);

        return $this->sendResponse($hRMSLeaveAccrualMaster->toArray(), 'HRMSLeaveAccrualMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSLeaveAccrualMasters/{id}",
     *      summary="Remove the specified HRMSLeaveAccrualMaster from storage",
     *      tags={"HRMSLeaveAccrualMaster"},
     *      description="Delete HRMSLeaveAccrualMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualMaster",
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
        /** @var HRMSLeaveAccrualMaster $hRMSLeaveAccrualMaster */
        $hRMSLeaveAccrualMaster = $this->hRMSLeaveAccrualMasterRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualMaster)) {
            return $this->sendError('H R M S Leave Accrual Master not found');
        }

        $hRMSLeaveAccrualMaster->delete();

        return $this->sendResponse($id, 'H R M S Leave Accrual Master deleted successfully');
    }
}
