<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalIncomeTemplateDefaultTranslationAPIRequest;
use App\Http\Requests\API\UpdateFinalIncomeTemplateDefaultTranslationAPIRequest;
use App\Models\FinalIncomeTemplateDefaultTranslation;
use App\Repositories\FinalIncomeTemplateDefaultTranslationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalIncomeTemplateDefaultTranslationController
 * @package App\Http\Controllers\API
 */

class FinalIncomeTemplateDefaultTranslationAPIController extends AppBaseController
{
    /** @var  FinalIncomeTemplateDefaultTranslationRepository */
    private $finalIncomeTemplateDefaultTranslationRepository;

    public function __construct(FinalIncomeTemplateDefaultTranslationRepository $finalIncomeTemplateDefaultTranslationRepo)
    {
        $this->finalIncomeTemplateDefaultTranslationRepository = $finalIncomeTemplateDefaultTranslationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalIncomeTemplateDefaultTranslations",
     *      summary="getFinalIncomeTemplateDefaultTranslationList",
     *      tags={"FinalIncomeTemplateDefaultTranslation"},
     *      description="Get all FinalIncomeTemplateDefaultTranslations",
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
     *                  @OA\Items(ref="#/definitions/FinalIncomeTemplateDefaultTranslation")
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
        $this->finalIncomeTemplateDefaultTranslationRepository->pushCriteria(new RequestCriteria($request));
        $this->finalIncomeTemplateDefaultTranslationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalIncomeTemplateDefaultTranslations = $this->finalIncomeTemplateDefaultTranslationRepository->all();

        return $this->sendResponse($finalIncomeTemplateDefaultTranslations->toArray(), 'Final Income Template Default Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalIncomeTemplateDefaultTranslations",
     *      summary="createFinalIncomeTemplateDefaultTranslation",
     *      tags={"FinalIncomeTemplateDefaultTranslation"},
     *      description="Create FinalIncomeTemplateDefaultTranslation",
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
     *                  ref="#/definitions/FinalIncomeTemplateDefaultTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinalIncomeTemplateDefaultTranslationAPIRequest $request)
    {
        $input = $request->all();

        $finalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepository->create($input);

        return $this->sendResponse($finalIncomeTemplateDefaultTranslation->toArray(), 'Final Income Template Default Translation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalIncomeTemplateDefaultTranslations/{id}",
     *      summary="getFinalIncomeTemplateDefaultTranslationItem",
     *      tags={"FinalIncomeTemplateDefaultTranslation"},
     *      description="Get FinalIncomeTemplateDefaultTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalIncomeTemplateDefaultTranslation",
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
     *                  ref="#/definitions/FinalIncomeTemplateDefaultTranslation"
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
        /** @var FinalIncomeTemplateDefaultTranslation $finalIncomeTemplateDefaultTranslation */
        $finalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepository->findWithoutFail($id);

        if (empty($finalIncomeTemplateDefaultTranslation)) {
            return $this->sendError('Final Income Template Default Translation not found');
        }

        return $this->sendResponse($finalIncomeTemplateDefaultTranslation->toArray(), 'Final Income Template Default Translation retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalIncomeTemplateDefaultTranslations/{id}",
     *      summary="updateFinalIncomeTemplateDefaultTranslation",
     *      tags={"FinalIncomeTemplateDefaultTranslation"},
     *      description="Update FinalIncomeTemplateDefaultTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalIncomeTemplateDefaultTranslation",
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
     *                  ref="#/definitions/FinalIncomeTemplateDefaultTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalIncomeTemplateDefaultTranslationAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalIncomeTemplateDefaultTranslation $finalIncomeTemplateDefaultTranslation */
        $finalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepository->findWithoutFail($id);

        if (empty($finalIncomeTemplateDefaultTranslation)) {
            return $this->sendError('Final Income Template Default Translation not found');
        }

        $finalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepository->update($input, $id);

        return $this->sendResponse($finalIncomeTemplateDefaultTranslation->toArray(), 'FinalIncomeTemplateDefaultTranslation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalIncomeTemplateDefaultTranslations/{id}",
     *      summary="deleteFinalIncomeTemplateDefaultTranslation",
     *      tags={"FinalIncomeTemplateDefaultTranslation"},
     *      description="Delete FinalIncomeTemplateDefaultTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalIncomeTemplateDefaultTranslation",
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
        /** @var FinalIncomeTemplateDefaultTranslation $finalIncomeTemplateDefaultTranslation */
        $finalIncomeTemplateDefaultTranslation = $this->finalIncomeTemplateDefaultTranslationRepository->findWithoutFail($id);

        if (empty($finalIncomeTemplateDefaultTranslation)) {
            return $this->sendError('Final Income Template Default Translation not found');
        }

        $finalIncomeTemplateDefaultTranslation->delete();

        return $this->sendSuccess('Final Income Template Default Translation deleted successfully');
    }
}
