<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGrvTypeLanguageAPIRequest;
use App\Http\Requests\API\UpdateGrvTypeLanguageAPIRequest;
use App\Models\GrvTypeLanguage;
use App\Repositories\GrvTypeLanguageRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GrvTypeLanguageController
 * @package App\Http\Controllers\API
 */

class GrvTypeLanguageAPIController extends AppBaseController
{
    /** @var  GrvTypeLanguageRepository */
    private $grvTypeLanguageRepository;

    public function __construct(GrvTypeLanguageRepository $grvTypeLanguageRepo)
    {
        $this->grvTypeLanguageRepository = $grvTypeLanguageRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/grvTypeLanguages",
     *      summary="getGrvTypeLanguageList",
     *      tags={"GrvTypeLanguage"},
     *      description="Get all GrvTypeLanguages",
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
     *                  @OA\Items(ref="#/definitions/GrvTypeLanguage")
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
        $this->grvTypeLanguageRepository->pushCriteria(new RequestCriteria($request));
        $this->grvTypeLanguageRepository->pushCriteria(new LimitOffsetCriteria($request));
        $grvTypeLanguages = $this->grvTypeLanguageRepository->all();

        return $this->sendResponse($grvTypeLanguages->toArray(), 'Grv Type Languages retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/grvTypeLanguages",
     *      summary="createGrvTypeLanguage",
     *      tags={"GrvTypeLanguage"},
     *      description="Create GrvTypeLanguage",
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
     *                  ref="#/definitions/GrvTypeLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGrvTypeLanguageAPIRequest $request)
    {
        $input = $request->all();

        $grvTypeLanguage = $this->grvTypeLanguageRepository->create($input);

        return $this->sendResponse($grvTypeLanguage->toArray(), 'Grv Type Language saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/grvTypeLanguages/{id}",
     *      summary="getGrvTypeLanguageItem",
     *      tags={"GrvTypeLanguage"},
     *      description="Get GrvTypeLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of GrvTypeLanguage",
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
     *                  ref="#/definitions/GrvTypeLanguage"
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
        /** @var GrvTypeLanguage $grvTypeLanguage */
        $grvTypeLanguage = $this->grvTypeLanguageRepository->findWithoutFail($id);

        if (empty($grvTypeLanguage)) {
            return $this->sendError('Grv Type Language not found');
        }

        return $this->sendResponse($grvTypeLanguage->toArray(), 'Grv Type Language retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/grvTypeLanguages/{id}",
     *      summary="updateGrvTypeLanguage",
     *      tags={"GrvTypeLanguage"},
     *      description="Update GrvTypeLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of GrvTypeLanguage",
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
     *                  ref="#/definitions/GrvTypeLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGrvTypeLanguageAPIRequest $request)
    {
        $input = $request->all();

        /** @var GrvTypeLanguage $grvTypeLanguage */
        $grvTypeLanguage = $this->grvTypeLanguageRepository->findWithoutFail($id);

        if (empty($grvTypeLanguage)) {
            return $this->sendError('Grv Type Language not found');
        }

        $grvTypeLanguage = $this->grvTypeLanguageRepository->update($input, $id);

        return $this->sendResponse($grvTypeLanguage->toArray(), 'GrvTypeLanguage updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/grvTypeLanguages/{id}",
     *      summary="deleteGrvTypeLanguage",
     *      tags={"GrvTypeLanguage"},
     *      description="Delete GrvTypeLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of GrvTypeLanguage",
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
        /** @var GrvTypeLanguage $grvTypeLanguage */
        $grvTypeLanguage = $this->grvTypeLanguageRepository->findWithoutFail($id);

        if (empty($grvTypeLanguage)) {
            return $this->sendError('Grv Type Language not found');
        }

        $grvTypeLanguage->delete();

        return $this->sendSuccess('Grv Type Language deleted successfully');
    }
}
