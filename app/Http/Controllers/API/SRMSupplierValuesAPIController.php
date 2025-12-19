<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMSupplierValuesAPIRequest;
use App\Http\Requests\API\UpdateSRMSupplierValuesAPIRequest;
use App\Models\SRMSupplierValues;
use App\Repositories\SRMSupplierValuesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SRMSupplierValuesController
 * @package App\Http\Controllers\API
 */

class SRMSupplierValuesAPIController extends AppBaseController
{
    /** @var  SRMSupplierValuesRepository */
    private $sRMSupplierValuesRepository;

    public function __construct(SRMSupplierValuesRepository $sRMSupplierValuesRepo)
    {
        $this->sRMSupplierValuesRepository = $sRMSupplierValuesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMSupplierValues",
     *      summary="getSRMSupplierValuesList",
     *      tags={"SRMSupplierValues"},
     *      description="Get all SRMSupplierValues",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/SRMSupplierValues")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->sRMSupplierValuesRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMSupplierValuesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMSupplierValues = $this->sRMSupplierValuesRepository->all();

        return $this->sendResponse($sRMSupplierValues->toArray(), trans('custom.s_r_m_supplier_values_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMSupplierValues",
     *      summary="createSRMSupplierValues",
     *      tags={"SRMSupplierValues"},
     *      description="Create SRMSupplierValues",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SRMSupplierValues"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMSupplierValuesAPIRequest $request)
    {
        $input = $request->all();

        $sRMSupplierValues = $this->sRMSupplierValuesRepository->create($input);

        return $this->sendResponse($sRMSupplierValues->toArray(), trans('custom.s_r_m_supplier_values_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMSupplierValues/{id}",
     *      summary="getSRMSupplierValuesItem",
     *      tags={"SRMSupplierValues"},
     *      description="Get SRMSupplierValues",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMSupplierValues",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SRMSupplierValues"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var SRMSupplierValues $sRMSupplierValues */
        $sRMSupplierValues = $this->sRMSupplierValuesRepository->findWithoutFail($id);

        if (empty($sRMSupplierValues)) {
            return $this->sendError(trans('custom.s_r_m_supplier_values_not_found'));
        }

        return $this->sendResponse($sRMSupplierValues->toArray(), trans('custom.s_r_m_supplier_values_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMSupplierValues/{id}",
     *      summary="updateSRMSupplierValues",
     *      tags={"SRMSupplierValues"},
     *      description="Update SRMSupplierValues",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMSupplierValues",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SRMSupplierValues"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSRMSupplierValuesAPIRequest $request)
    {
        $input = $request->all();

        /** @var SRMSupplierValues $sRMSupplierValues */
        $sRMSupplierValues = $this->sRMSupplierValuesRepository->findWithoutFail($id);

        if (empty($sRMSupplierValues)) {
            return $this->sendError(trans('custom.s_r_m_supplier_values_not_found'));
        }

        $sRMSupplierValues = $this->sRMSupplierValuesRepository->update($input, $id);

        return $this->sendResponse($sRMSupplierValues->toArray(), trans('custom.srmsuppliervalues_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMSupplierValues/{id}",
     *      summary="deleteSRMSupplierValues",
     *      tags={"SRMSupplierValues"},
     *      description="Delete SRMSupplierValues",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMSupplierValues",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var SRMSupplierValues $sRMSupplierValues */
        $sRMSupplierValues = $this->sRMSupplierValuesRepository->findWithoutFail($id);

        if (empty($sRMSupplierValues)) {
            return $this->sendError(trans('custom.s_r_m_supplier_values_not_found'));
        }

        $sRMSupplierValues->delete();

        return $this->sendSuccess('S R M Supplier Values deleted successfully');
    }
}
