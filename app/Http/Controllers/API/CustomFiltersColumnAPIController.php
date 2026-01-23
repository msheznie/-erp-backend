<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomFiltersColumnAPIRequest;
use App\Http\Requests\API\UpdateCustomFiltersColumnAPIRequest;
use App\Models\CustomFiltersColumn;
use App\Repositories\CustomFiltersColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomFiltersColumnController
 * @package App\Http\Controllers\API
 */

class CustomFiltersColumnAPIController extends AppBaseController
{
    /** @var  CustomFiltersColumnRepository */
    private $customFiltersColumnRepository;

    public function __construct(CustomFiltersColumnRepository $customFiltersColumnRepo)
    {
        $this->customFiltersColumnRepository = $customFiltersColumnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customFiltersColumns",
     *      summary="Get a listing of the CustomFiltersColumns.",
     *      tags={"CustomFiltersColumn"},
     *      description="Get all CustomFiltersColumns",
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
     *                  @SWG\Items(ref="#/definitions/CustomFiltersColumn")
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
        $this->customFiltersColumnRepository->pushCriteria(new RequestCriteria($request));
        $this->customFiltersColumnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customFiltersColumns = $this->customFiltersColumnRepository->all();

        return $this->sendResponse($customFiltersColumns->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_filters_columns')]));
    }

    /**
     * @param CreateCustomFiltersColumnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customFiltersColumns",
     *      summary="Store a newly created CustomFiltersColumn in storage",
     *      tags={"CustomFiltersColumn"},
     *      description="Store CustomFiltersColumn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomFiltersColumn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomFiltersColumn")
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
     *                  ref="#/definitions/CustomFiltersColumn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomFiltersColumnAPIRequest $request)
    {
        $input = $request->all();

        $customFiltersColumn = $this->customFiltersColumnRepository->create($input);

        return $this->sendResponse($customFiltersColumn->toArray(), trans('custom.save', ['attribute' => trans('custom.custom_filters_columns')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customFiltersColumns/{id}",
     *      summary="Display the specified CustomFiltersColumn",
     *      tags={"CustomFiltersColumn"},
     *      description="Get CustomFiltersColumn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomFiltersColumn",
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
     *                  ref="#/definitions/CustomFiltersColumn"
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
        /** @var CustomFiltersColumn $customFiltersColumn */
        $customFiltersColumn = $this->customFiltersColumnRepository->findWithoutFail($id);

        if (empty($customFiltersColumn)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_filters_columns')]));
        }

        return $this->sendResponse($customFiltersColumn->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_filters_columns')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomFiltersColumnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customFiltersColumns/{id}",
     *      summary="Update the specified CustomFiltersColumn in storage",
     *      tags={"CustomFiltersColumn"},
     *      description="Update CustomFiltersColumn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomFiltersColumn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomFiltersColumn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomFiltersColumn")
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
     *                  ref="#/definitions/CustomFiltersColumn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomFiltersColumnAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomFiltersColumn $customFiltersColumn */
        $customFiltersColumn = $this->customFiltersColumnRepository->findWithoutFail($id);

        if (empty($customFiltersColumn)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_filters_columns')]));
        }

        $customFiltersColumn = $this->customFiltersColumnRepository->update($input, $id);

        return $this->sendResponse($customFiltersColumn->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_filters_columns')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customFiltersColumns/{id}",
     *      summary="Remove the specified CustomFiltersColumn from storage",
     *      tags={"CustomFiltersColumn"},
     *      description="Delete CustomFiltersColumn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomFiltersColumn",
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
        /** @var CustomFiltersColumn $customFiltersColumn */
        $customFiltersColumn = $this->customFiltersColumnRepository->findWithoutFail($id);

        if (empty($customFiltersColumn)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_filters_columns')]));
        }

        $customFiltersColumn->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.custom_filters_columns')]));
    }
}
