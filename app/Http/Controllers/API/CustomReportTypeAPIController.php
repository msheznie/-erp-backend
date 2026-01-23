<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomReportTypeAPIRequest;
use App\Http\Requests\API\UpdateCustomReportTypeAPIRequest;
use App\Models\CustomReportType;
use App\Repositories\CustomReportTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomReportTypeController
 * @package App\Http\Controllers\API
 */
class CustomReportTypeAPIController extends AppBaseController
{
    /** @var  CustomReportTypeRepository */
    private $customReportTypeRepository;

    public function __construct(CustomReportTypeRepository $customReportTypeRepo)
    {
        $this->customReportTypeRepository = $customReportTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportTypes",
     *      summary="Get a listing of the CustomReportTypes.",
     *      tags={"CustomReportType"},
     *      description="Get all CustomReportTypes",
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
     *                  @SWG\Items(ref="#/definitions/CustomReportType")
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
        $this->customReportTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->customReportTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customReportTypes = $this->customReportTypeRepository->whereHas('templates', function ($q) {
                     $q->where('is_active',1);
            })
            ->where('is_active',1)
            ->with(['templates'])
            ->get();

        return $this->sendResponse($customReportTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_types')]));
    }

    /**
     * @param CreateCustomReportTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customReportTypes",
     *      summary="Store a newly created CustomReportType in storage",
     *      tags={"CustomReportType"},
     *      description="Store CustomReportType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportType")
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
     *                  ref="#/definitions/CustomReportType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomReportTypeAPIRequest $request)
    {
        $input = $request->all();

        $customReportType = $this->customReportTypeRepository->create($input);

        return $this->sendResponse($customReportType->toArray(), trans('custom.save', ['attribute' => trans('custom.custom_report_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportTypes/{id}",
     *      summary="Display the specified CustomReportType",
     *      tags={"CustomReportType"},
     *      description="Get CustomReportType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportType",
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
     *                  ref="#/definitions/CustomReportType"
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
        /** @var CustomReportType $customReportType */
        $customReportType = $this->customReportTypeRepository->findWithoutFail($id);

        if (empty($customReportType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_types')]));
        }

        return $this->sendResponse($customReportType->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_types')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomReportTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customReportTypes/{id}",
     *      summary="Update the specified CustomReportType in storage",
     *      tags={"CustomReportType"},
     *      description="Update CustomReportType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportType")
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
     *                  ref="#/definitions/CustomReportType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomReportTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomReportType $customReportType */
        $customReportType = $this->customReportTypeRepository->findWithoutFail($id);

        if (empty($customReportType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_types')]));
        }

        $customReportType = $this->customReportTypeRepository->update($input, $id);

        return $this->sendResponse($customReportType->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_report_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customReportTypes/{id}",
     *      summary="Remove the specified CustomReportType from storage",
     *      tags={"CustomReportType"},
     *      description="Delete CustomReportType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportType",
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
        /** @var CustomReportType $customReportType */
        $customReportType = $this->customReportTypeRepository->findWithoutFail($id);

        if (empty($customReportType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_types')]));
        }

        $customReportType->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.custom_report_types')]));
    }
}
