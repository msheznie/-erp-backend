<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateYesNoMinusSelectionLanguageAPIRequest;
use App\Http\Requests\API\UpdateYesNoMinusSelectionLanguageAPIRequest;
use App\Models\YesNoMinusSelectionLanguage;
use App\Repositories\YesNoMinusSelectionLanguageRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class YesNoMinusSelectionLanguageController
 * @package App\Http\Controllers\API
 */

class YesNoMinusSelectionLanguageAPIController extends AppBaseController
{
    /** @var  YesNoMinusSelectionLanguageRepository */
    private $yesNoMinusSelectionLanguageRepository;

    public function __construct(YesNoMinusSelectionLanguageRepository $yesNoMinusSelectionLanguageRepo)
    {
        $this->yesNoMinusSelectionLanguageRepository = $yesNoMinusSelectionLanguageRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/yesNoMinusSelectionLanguages",
     *      summary="getYesNoMinusSelectionLanguageList",
     *      tags={"YesNoMinusSelectionLanguage"},
     *      description="Get all YesNoMinusSelectionLanguages",
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
     *                  @OA\Items(ref="#/definitions/YesNoMinusSelectionLanguage")
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
        $this->yesNoMinusSelectionLanguageRepository->pushCriteria(new RequestCriteria($request));
        $this->yesNoMinusSelectionLanguageRepository->pushCriteria(new LimitOffsetCriteria($request));
        $yesNoMinusSelectionLanguages = $this->yesNoMinusSelectionLanguageRepository->all();

        return $this->sendResponse($yesNoMinusSelectionLanguages->toArray(), 'Yes No Minus Selection Languages retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/yesNoMinusSelectionLanguages",
     *      summary="createYesNoMinusSelectionLanguage",
     *      tags={"YesNoMinusSelectionLanguage"},
     *      description="Create YesNoMinusSelectionLanguage",
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
     *                  ref="#/definitions/YesNoMinusSelectionLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateYesNoMinusSelectionLanguageAPIRequest $request)
    {
        $input = $request->all();

        $yesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepository->create($input);

        return $this->sendResponse($yesNoMinusSelectionLanguage->toArray(), 'Yes No Minus Selection Language saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/yesNoMinusSelectionLanguages/{id}",
     *      summary="getYesNoMinusSelectionLanguageItem",
     *      tags={"YesNoMinusSelectionLanguage"},
     *      description="Get YesNoMinusSelectionLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of YesNoMinusSelectionLanguage",
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
     *                  ref="#/definitions/YesNoMinusSelectionLanguage"
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
        /** @var YesNoMinusSelectionLanguage $yesNoMinusSelectionLanguage */
        $yesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepository->findWithoutFail($id);

        if (empty($yesNoMinusSelectionLanguage)) {
            return $this->sendError('Yes No Minus Selection Language not found');
        }

        return $this->sendResponse($yesNoMinusSelectionLanguage->toArray(), 'Yes No Minus Selection Language retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/yesNoMinusSelectionLanguages/{id}",
     *      summary="updateYesNoMinusSelectionLanguage",
     *      tags={"YesNoMinusSelectionLanguage"},
     *      description="Update YesNoMinusSelectionLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of YesNoMinusSelectionLanguage",
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
     *                  ref="#/definitions/YesNoMinusSelectionLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateYesNoMinusSelectionLanguageAPIRequest $request)
    {
        $input = $request->all();

        /** @var YesNoMinusSelectionLanguage $yesNoMinusSelectionLanguage */
        $yesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepository->findWithoutFail($id);

        if (empty($yesNoMinusSelectionLanguage)) {
            return $this->sendError('Yes No Minus Selection Language not found');
        }

        $yesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepository->update($input, $id);

        return $this->sendResponse($yesNoMinusSelectionLanguage->toArray(), 'YesNoMinusSelectionLanguage updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/yesNoMinusSelectionLanguages/{id}",
     *      summary="deleteYesNoMinusSelectionLanguage",
     *      tags={"YesNoMinusSelectionLanguage"},
     *      description="Delete YesNoMinusSelectionLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of YesNoMinusSelectionLanguage",
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
        /** @var YesNoMinusSelectionLanguage $yesNoMinusSelectionLanguage */
        $yesNoMinusSelectionLanguage = $this->yesNoMinusSelectionLanguageRepository->findWithoutFail($id);

        if (empty($yesNoMinusSelectionLanguage)) {
            return $this->sendError('Yes No Minus Selection Language not found');
        }

        $yesNoMinusSelectionLanguage->delete();

        return $this->sendSuccess('Yes No Minus Selection Language deleted successfully');
    }
}
