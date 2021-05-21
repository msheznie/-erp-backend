<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomReportMasterAPIRequest;
use App\Http\Requests\API\UpdateCustomReportMasterAPIRequest;
use App\Models\CustomReportMaster;
use App\Repositories\CustomReportMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomReportMasterController
 * @package App\Http\Controllers\API
 */

class CustomReportMasterAPIController extends AppBaseController
{
    /** @var  CustomReportMasterRepository */
    private $customReportMasterRepository;

    public function __construct(CustomReportMasterRepository $customReportMasterRepo)
    {
        $this->customReportMasterRepository = $customReportMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportMasters",
     *      summary="Get a listing of the CustomReportMasters.",
     *      tags={"CustomReportMaster"},
     *      description="Get all CustomReportMasters",
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
     *                  @SWG\Items(ref="#/definitions/CustomReportMaster")
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
        $this->customReportMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->customReportMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customReportMasters = $this->customReportMasterRepository->all();

        return $this->sendResponse($customReportMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_masters')]));
    }

    /**
     * @param CreateCustomReportMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customReportMasters",
     *      summary="Store a newly created CustomReportMaster in storage",
     *      tags={"CustomReportMaster"},
     *      description="Store CustomReportMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportMaster")
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
     *                  ref="#/definitions/CustomReportMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomReportMasterAPIRequest $request)
    {
        $input = $request->all();

        $customReportMaster = $this->customReportMasterRepository->create($input);

        return $this->sendResponse($customReportMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.custom_report_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportMasters/{id}",
     *      summary="Display the specified CustomReportMaster",
     *      tags={"CustomReportMaster"},
     *      description="Get CustomReportMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportMaster",
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
     *                  ref="#/definitions/CustomReportMaster"
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
        /** @var CustomReportMaster $customReportMaster */
        $customReportMaster = $this->customReportMasterRepository->findWithoutFail($id);

        if (empty($customReportMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_masters')]));
        }

        return $this->sendResponse($customReportMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_masters')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomReportMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customReportMasters/{id}",
     *      summary="Update the specified CustomReportMaster in storage",
     *      tags={"CustomReportMaster"},
     *      description="Update CustomReportMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportMaster")
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
     *                  ref="#/definitions/CustomReportMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomReportMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomReportMaster $customReportMaster */
        $customReportMaster = $this->customReportMasterRepository->findWithoutFail($id);

        if (empty($customReportMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_masters')]));
        }

        $customReportMaster = $this->customReportMasterRepository->update($input, $id);

        return $this->sendResponse($customReportMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_report_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customReportMasters/{id}",
     *      summary="Remove the specified CustomReportMaster from storage",
     *      tags={"CustomReportMaster"},
     *      description="Delete CustomReportMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportMaster",
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
        /** @var CustomReportMaster $customReportMaster */
        $customReportMaster = $this->customReportMasterRepository->findWithoutFail($id);

        if (empty($customReportMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_masters')]));
        }

        $customReportMaster->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.custom_report_masters')]));
    }
}
