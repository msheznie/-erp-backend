<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatSubCategoryTypeAPIRequest;
use App\Http\Requests\API\UpdateVatSubCategoryTypeAPIRequest;
use App\Models\VatSubCategoryType;
use App\Repositories\VatSubCategoryTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatSubCategoryTypeController
 * @package App\Http\Controllers\API
 */

class VatSubCategoryTypeAPIController extends AppBaseController
{
    /** @var  VatSubCategoryTypeRepository */
    private $vatSubCategoryTypeRepository;

    public function __construct(VatSubCategoryTypeRepository $vatSubCategoryTypeRepo)
    {
        $this->vatSubCategoryTypeRepository = $vatSubCategoryTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatSubCategoryTypes",
     *      summary="Get a listing of the VatSubCategoryTypes.",
     *      tags={"VatSubCategoryType"},
     *      description="Get all VatSubCategoryTypes",
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
     *                  @SWG\Items(ref="#/definitions/VatSubCategoryType")
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
        $this->vatSubCategoryTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->vatSubCategoryTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatSubCategoryTypes = $this->vatSubCategoryTypeRepository->all();

        return $this->sendResponse($vatSubCategoryTypes->toArray(), trans('custom.vat_sub_category_types_retrieved_successfully'));
    }

    /**
     * @param CreateVatSubCategoryTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatSubCategoryTypes",
     *      summary="Store a newly created VatSubCategoryType in storage",
     *      tags={"VatSubCategoryType"},
     *      description="Store VatSubCategoryType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatSubCategoryType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatSubCategoryType")
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
     *                  ref="#/definitions/VatSubCategoryType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatSubCategoryTypeAPIRequest $request)
    {
        $input = $request->all();

        $vatSubCategoryType = $this->vatSubCategoryTypeRepository->create($input);

        return $this->sendResponse($vatSubCategoryType->toArray(), trans('custom.vat_sub_category_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatSubCategoryTypes/{id}",
     *      summary="Display the specified VatSubCategoryType",
     *      tags={"VatSubCategoryType"},
     *      description="Get VatSubCategoryType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatSubCategoryType",
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
     *                  ref="#/definitions/VatSubCategoryType"
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
        /** @var VatSubCategoryType $vatSubCategoryType */
        $vatSubCategoryType = $this->vatSubCategoryTypeRepository->findWithoutFail($id);

        if (empty($vatSubCategoryType)) {
            return $this->sendError(trans('custom.vat_sub_category_type_not_found'));
        }

        return $this->sendResponse($vatSubCategoryType->toArray(), trans('custom.vat_sub_category_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateVatSubCategoryTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatSubCategoryTypes/{id}",
     *      summary="Update the specified VatSubCategoryType in storage",
     *      tags={"VatSubCategoryType"},
     *      description="Update VatSubCategoryType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatSubCategoryType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatSubCategoryType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatSubCategoryType")
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
     *                  ref="#/definitions/VatSubCategoryType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatSubCategoryTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatSubCategoryType $vatSubCategoryType */
        $vatSubCategoryType = $this->vatSubCategoryTypeRepository->findWithoutFail($id);

        if (empty($vatSubCategoryType)) {
            return $this->sendError(trans('custom.vat_sub_category_type_not_found'));
        }

        $vatSubCategoryType = $this->vatSubCategoryTypeRepository->update($input, $id);

        return $this->sendResponse($vatSubCategoryType->toArray(), trans('custom.vatsubcategorytype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatSubCategoryTypes/{id}",
     *      summary="Remove the specified VatSubCategoryType from storage",
     *      tags={"VatSubCategoryType"},
     *      description="Delete VatSubCategoryType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatSubCategoryType",
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
        /** @var VatSubCategoryType $vatSubCategoryType */
        $vatSubCategoryType = $this->vatSubCategoryTypeRepository->findWithoutFail($id);

        if (empty($vatSubCategoryType)) {
            return $this->sendError(trans('custom.vat_sub_category_type_not_found'));
        }

        $vatSubCategoryType->delete();

        return $this->sendSuccess('Vat Sub Category Type deleted successfully');
    }
}
