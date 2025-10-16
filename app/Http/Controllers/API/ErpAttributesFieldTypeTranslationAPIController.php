<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpAttributesFieldTypeTranslationAPIRequest;
use App\Http\Requests\API\UpdateErpAttributesFieldTypeTranslationAPIRequest;
use App\Models\ErpAttributesFieldTypeTranslation;
use App\Repositories\ErpAttributesFieldTypeTranslationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpAttributesFieldTypeTranslationController
 * @package App\Http\Controllers\API
 */

class ErpAttributesFieldTypeTranslationAPIController extends AppBaseController
{
    /** @var  ErpAttributesFieldTypeTranslationRepository */
    private $erpAttributesFieldTypeTranslationRepository;

    public function __construct(ErpAttributesFieldTypeTranslationRepository $erpAttributesFieldTypeTranslationRepo)
    {
        $this->erpAttributesFieldTypeTranslationRepository = $erpAttributesFieldTypeTranslationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/erpAttributesFieldTypeTranslations",
     *      summary="getErpAttributesFieldTypeTranslationList",
     *      tags={"ErpAttributesFieldTypeTranslation"},
     *      description="Get all ErpAttributesFieldTypeTranslations",
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
     *                  @OA\Items(ref="#/definitions/ErpAttributesFieldTypeTranslation")
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
        $this->erpAttributesFieldTypeTranslationRepository->pushCriteria(new RequestCriteria($request));
        $this->erpAttributesFieldTypeTranslationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpAttributesFieldTypeTranslations = $this->erpAttributesFieldTypeTranslationRepository->all();

        return $this->sendResponse($erpAttributesFieldTypeTranslations->toArray(), 'Erp Attributes Field Type Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/erpAttributesFieldTypeTranslations",
     *      summary="createErpAttributesFieldTypeTranslation",
     *      tags={"ErpAttributesFieldTypeTranslation"},
     *      description="Create ErpAttributesFieldTypeTranslation",
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
     *                  ref="#/definitions/ErpAttributesFieldTypeTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpAttributesFieldTypeTranslationAPIRequest $request)
    {
        $input = $request->all();

        $erpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepository->create($input);

        return $this->sendResponse($erpAttributesFieldTypeTranslation->toArray(), 'Erp Attributes Field Type Translation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/erpAttributesFieldTypeTranslations/{id}",
     *      summary="getErpAttributesFieldTypeTranslationItem",
     *      tags={"ErpAttributesFieldTypeTranslation"},
     *      description="Get ErpAttributesFieldTypeTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ErpAttributesFieldTypeTranslation",
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
     *                  ref="#/definitions/ErpAttributesFieldTypeTranslation"
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
        /** @var ErpAttributesFieldTypeTranslation $erpAttributesFieldTypeTranslation */
        $erpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepository->findWithoutFail($id);

        if (empty($erpAttributesFieldTypeTranslation)) {
            return $this->sendError('Erp Attributes Field Type Translation not found');
        }

        return $this->sendResponse($erpAttributesFieldTypeTranslation->toArray(), 'Erp Attributes Field Type Translation retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/erpAttributesFieldTypeTranslations/{id}",
     *      summary="updateErpAttributesFieldTypeTranslation",
     *      tags={"ErpAttributesFieldTypeTranslation"},
     *      description="Update ErpAttributesFieldTypeTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ErpAttributesFieldTypeTranslation",
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
     *                  ref="#/definitions/ErpAttributesFieldTypeTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpAttributesFieldTypeTranslationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpAttributesFieldTypeTranslation $erpAttributesFieldTypeTranslation */
        $erpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepository->findWithoutFail($id);

        if (empty($erpAttributesFieldTypeTranslation)) {
            return $this->sendError('Erp Attributes Field Type Translation not found');
        }

        $erpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepository->update($input, $id);

        return $this->sendResponse($erpAttributesFieldTypeTranslation->toArray(), 'ErpAttributesFieldTypeTranslation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/erpAttributesFieldTypeTranslations/{id}",
     *      summary="deleteErpAttributesFieldTypeTranslation",
     *      tags={"ErpAttributesFieldTypeTranslation"},
     *      description="Delete ErpAttributesFieldTypeTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ErpAttributesFieldTypeTranslation",
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
        /** @var ErpAttributesFieldTypeTranslation $erpAttributesFieldTypeTranslation */
        $erpAttributesFieldTypeTranslation = $this->erpAttributesFieldTypeTranslationRepository->findWithoutFail($id);

        if (empty($erpAttributesFieldTypeTranslation)) {
            return $this->sendError('Erp Attributes Field Type Translation not found');
        }

        $erpAttributesFieldTypeTranslation->delete();

        return $this->sendSuccess('Erp Attributes Field Type Translation deleted successfully');
    }
}
