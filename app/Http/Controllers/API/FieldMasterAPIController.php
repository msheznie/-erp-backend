<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFieldMasterAPIRequest;
use App\Http\Requests\API\UpdateFieldMasterAPIRequest;
use App\Models\FieldMaster;
use App\Repositories\FieldMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FieldMasterController
 * @package App\Http\Controllers\API
 */

class FieldMasterAPIController extends AppBaseController
{
    /** @var  FieldMasterRepository */
    private $fieldMasterRepository;

    public function __construct(FieldMasterRepository $fieldMasterRepo)
    {
        $this->fieldMasterRepository = $fieldMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fieldMasters",
     *      summary="Get a listing of the FieldMasters.",
     *      tags={"FieldMaster"},
     *      description="Get all FieldMasters",
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
     *                  @SWG\Items(ref="#/definitions/FieldMaster")
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
        $this->fieldMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fieldMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fieldMasters = $this->fieldMasterRepository->all();

        return $this->sendResponse($fieldMasters->toArray(), trans('custom.field_masters_retrieved_successfully'));
    }

    /**
     * @param CreateFieldMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fieldMasters",
     *      summary="Store a newly created FieldMaster in storage",
     *      tags={"FieldMaster"},
     *      description="Store FieldMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FieldMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FieldMaster")
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
     *                  ref="#/definitions/FieldMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFieldMasterAPIRequest $request)
    {
        $input = $request->all();

        $fieldMasters = $this->fieldMasterRepository->create($input);

        return $this->sendResponse($fieldMasters->toArray(), trans('custom.field_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fieldMasters/{id}",
     *      summary="Display the specified FieldMaster",
     *      tags={"FieldMaster"},
     *      description="Get FieldMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FieldMaster",
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
     *                  ref="#/definitions/FieldMaster"
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
        /** @var FieldMaster $fieldMaster */
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            return $this->sendError(trans('custom.field_master_not_found'));
        }

        return $this->sendResponse($fieldMaster->toArray(), trans('custom.field_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFieldMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fieldMasters/{id}",
     *      summary="Update the specified FieldMaster in storage",
     *      tags={"FieldMaster"},
     *      description="Update FieldMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FieldMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FieldMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FieldMaster")
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
     *                  ref="#/definitions/FieldMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFieldMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var FieldMaster $fieldMaster */
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            return $this->sendError(trans('custom.field_master_not_found'));
        }

        $fieldMaster = $this->fieldMasterRepository->update($input, $id);

        return $this->sendResponse($fieldMaster->toArray(), trans('custom.fieldmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fieldMasters/{id}",
     *      summary="Remove the specified FieldMaster from storage",
     *      tags={"FieldMaster"},
     *      description="Delete FieldMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FieldMaster",
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
        /** @var FieldMaster $fieldMaster */
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            return $this->sendError(trans('custom.field_master_not_found'));
        }

        $fieldMaster->delete();

        return $this->sendResponse($id, trans('custom.field_master_deleted_successfully'));
    }
}
