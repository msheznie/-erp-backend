<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpAttributesFieldTypeAPIRequest;
use App\Http\Requests\API\UpdateErpAttributesFieldTypeAPIRequest;
use App\Models\ErpAttributesFieldType;
use App\Repositories\ErpAttributesFieldTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpAttributesFieldTypeController
 * @package App\Http\Controllers\API
 */

class ErpAttributesFieldTypeAPIController extends AppBaseController
{
    /** @var  ErpAttributesFieldTypeRepository */
    private $erpAttributesFieldTypeRepository;

    public function __construct(ErpAttributesFieldTypeRepository $erpAttributesFieldTypeRepo)
    {
        $this->erpAttributesFieldTypeRepository = $erpAttributesFieldTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpAttributesFieldTypes",
     *      summary="Get a listing of the ErpAttributesFieldTypes.",
     *      tags={"ErpAttributesFieldType"},
     *      description="Get all ErpAttributesFieldTypes",
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
     *                  @SWG\Items(ref="#/definitions/ErpAttributesFieldType")
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
        $this->erpAttributesFieldTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->erpAttributesFieldTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpAttributesFieldTypes = $this->erpAttributesFieldTypeRepository->all();

        return $this->sendResponse($erpAttributesFieldTypes->toArray(), trans('custom.erp_attributes_field_types_retrieved_successfully'));
    }

    /**
     * @param CreateErpAttributesFieldTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpAttributesFieldTypes",
     *      summary="Store a newly created ErpAttributesFieldType in storage",
     *      tags={"ErpAttributesFieldType"},
     *      description="Store ErpAttributesFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpAttributesFieldType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpAttributesFieldType")
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
     *                  ref="#/definitions/ErpAttributesFieldType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpAttributesFieldTypeAPIRequest $request)
    {
        $input = $request->all();

        $erpAttributesFieldType = $this->erpAttributesFieldTypeRepository->create($input);

        return $this->sendResponse($erpAttributesFieldType->toArray(), trans('custom.erp_attributes_field_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpAttributesFieldTypes/{id}",
     *      summary="Display the specified ErpAttributesFieldType",
     *      tags={"ErpAttributesFieldType"},
     *      description="Get ErpAttributesFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributesFieldType",
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
     *                  ref="#/definitions/ErpAttributesFieldType"
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
        /** @var ErpAttributesFieldType $erpAttributesFieldType */
        $erpAttributesFieldType = $this->erpAttributesFieldTypeRepository->findWithoutFail($id);

        if (empty($erpAttributesFieldType)) {
            return $this->sendError(trans('custom.erp_attributes_field_type_not_found'));
        }

        return $this->sendResponse($erpAttributesFieldType->toArray(), trans('custom.erp_attributes_field_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateErpAttributesFieldTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpAttributesFieldTypes/{id}",
     *      summary="Update the specified ErpAttributesFieldType in storage",
     *      tags={"ErpAttributesFieldType"},
     *      description="Update ErpAttributesFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributesFieldType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpAttributesFieldType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpAttributesFieldType")
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
     *                  ref="#/definitions/ErpAttributesFieldType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpAttributesFieldTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpAttributesFieldType $erpAttributesFieldType */
        $erpAttributesFieldType = $this->erpAttributesFieldTypeRepository->findWithoutFail($id);

        if (empty($erpAttributesFieldType)) {
            return $this->sendError(trans('custom.erp_attributes_field_type_not_found'));
        }

        $erpAttributesFieldType = $this->erpAttributesFieldTypeRepository->update($input, $id);

        return $this->sendResponse($erpAttributesFieldType->toArray(), trans('custom.erpattributesfieldtype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpAttributesFieldTypes/{id}",
     *      summary="Remove the specified ErpAttributesFieldType from storage",
     *      tags={"ErpAttributesFieldType"},
     *      description="Delete ErpAttributesFieldType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributesFieldType",
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
        /** @var ErpAttributesFieldType $erpAttributesFieldType */
        $erpAttributesFieldType = $this->erpAttributesFieldTypeRepository->findWithoutFail($id);

        if (empty($erpAttributesFieldType)) {
            return $this->sendError(trans('custom.erp_attributes_field_type_not_found'));
        }

        $erpAttributesFieldType->delete();

        return $this->sendSuccess('Erp Attributes Field Type deleted successfully');
    }
}
