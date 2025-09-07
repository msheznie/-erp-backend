<?php
/**
 * =============================================
 * -- File Name : GRVTypesAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Types
 * -- Author : Mohamed Nazir
 * -- Create date : 12-June 2018
 * -- Description : This file contains the all CRUD for GRV Types
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVTypesAPIRequest;
use App\Http\Requests\API\UpdateGRVTypesAPIRequest;
use App\Models\GRVTypes;
use App\Repositories\GRVTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GRVTypesController
 * @package App\Http\Controllers\API
 */

class GRVTypesAPIController extends AppBaseController
{
    /** @var  GRVTypesRepository */
    private $gRVTypesRepository;

    public function __construct(GRVTypesRepository $gRVTypesRepo)
    {
        $this->gRVTypesRepository = $gRVTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/gRVTypes",
     *      summary="Get a listing of the GRVTypes.",
     *      tags={"GRVTypes"},
     *      description="Get all GRVTypes",
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
     *                  @SWG\Items(ref="#/definitions/GRVTypes")
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
        $this->gRVTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->gRVTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gRVTypes = $this->gRVTypesRepository->all();

        return $this->sendResponse($gRVTypes->toArray(), trans('custom.g_r_v_types_retrieved_successfully'));
    }

    /**
     * @param CreateGRVTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/gRVTypes",
     *      summary="Store a newly created GRVTypes in storage",
     *      tags={"GRVTypes"},
     *      description="Store GRVTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GRVTypes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GRVTypes")
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
     *                  ref="#/definitions/GRVTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGRVTypesAPIRequest $request)
    {
        $input = $request->all();

        $gRVTypes = $this->gRVTypesRepository->create($input);

        return $this->sendResponse($gRVTypes->toArray(), trans('custom.g_r_v_types_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/gRVTypes/{id}",
     *      summary="Display the specified GRVTypes",
     *      tags={"GRVTypes"},
     *      description="Get GRVTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GRVTypes",
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
     *                  ref="#/definitions/GRVTypes"
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
        /** @var GRVTypes $gRVTypes */
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            return $this->sendError(trans('custom.g_r_v_types_not_found'));
        }

        return $this->sendResponse($gRVTypes->toArray(), trans('custom.g_r_v_types_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGRVTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/gRVTypes/{id}",
     *      summary="Update the specified GRVTypes in storage",
     *      tags={"GRVTypes"},
     *      description="Update GRVTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GRVTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GRVTypes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GRVTypes")
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
     *                  ref="#/definitions/GRVTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGRVTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var GRVTypes $gRVTypes */
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            return $this->sendError(trans('custom.g_r_v_types_not_found'));
        }

        $gRVTypes = $this->gRVTypesRepository->update($input, $id);

        return $this->sendResponse($gRVTypes->toArray(), trans('custom.grvtypes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/gRVTypes/{id}",
     *      summary="Remove the specified GRVTypes from storage",
     *      tags={"GRVTypes"},
     *      description="Delete GRVTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GRVTypes",
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
        /** @var GRVTypes $gRVTypes */
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            return $this->sendError(trans('custom.g_r_v_types_not_found'));
        }

        $gRVTypes->delete();

        return $this->sendResponse($id, trans('custom.g_r_v_types_deleted_successfully'));
    }
}
