<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFillingCategoryLanguageAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingCategoryLanguageAPIRequest;
use App\Models\VatReturnFillingCategoryLanguage;
use App\Repositories\VatReturnFillingCategoryLanguageRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFillingCategoryLanguageController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingCategoryLanguageAPIController extends AppBaseController
{
    /** @var  VatReturnFillingCategoryLanguageRepository */
    private $vatReturnFillingCategoryLanguageRepository;

    public function __construct(VatReturnFillingCategoryLanguageRepository $vatReturnFillingCategoryLanguageRepo)
    {
        $this->vatReturnFillingCategoryLanguageRepository = $vatReturnFillingCategoryLanguageRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/vatReturnFillingCategoryLanguages",
     *      summary="getVatReturnFillingCategoryLanguageList",
     *      tags={"VatReturnFillingCategoryLanguage"},
     *      description="Get all VatReturnFillingCategoryLanguages",
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
     *                  @OA\Items(ref="#/definitions/VatReturnFillingCategoryLanguage")
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
        $this->vatReturnFillingCategoryLanguageRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingCategoryLanguageRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingCategoryLanguages = $this->vatReturnFillingCategoryLanguageRepository->all();

        return $this->sendResponse($vatReturnFillingCategoryLanguages->toArray(), 'Vat Return Filling Category Languages retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/vatReturnFillingCategoryLanguages",
     *      summary="createVatReturnFillingCategoryLanguage",
     *      tags={"VatReturnFillingCategoryLanguage"},
     *      description="Create VatReturnFillingCategoryLanguage",
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
     *                  ref="#/definitions/VatReturnFillingCategoryLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFillingCategoryLanguageAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepository->create($input);

        return $this->sendResponse($vatReturnFillingCategoryLanguage->toArray(), 'Vat Return Filling Category Language saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/vatReturnFillingCategoryLanguages/{id}",
     *      summary="getVatReturnFillingCategoryLanguageItem",
     *      tags={"VatReturnFillingCategoryLanguage"},
     *      description="Get VatReturnFillingCategoryLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingCategoryLanguage",
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
     *                  ref="#/definitions/VatReturnFillingCategoryLanguage"
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
        /** @var VatReturnFillingCategoryLanguage $vatReturnFillingCategoryLanguage */
        $vatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepository->findWithoutFail($id);

        if (empty($vatReturnFillingCategoryLanguage)) {
            return $this->sendError('Vat Return Filling Category Language not found');
        }

        return $this->sendResponse($vatReturnFillingCategoryLanguage->toArray(), 'Vat Return Filling Category Language retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/vatReturnFillingCategoryLanguages/{id}",
     *      summary="updateVatReturnFillingCategoryLanguage",
     *      tags={"VatReturnFillingCategoryLanguage"},
     *      description="Update VatReturnFillingCategoryLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingCategoryLanguage",
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
     *                  ref="#/definitions/VatReturnFillingCategoryLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingCategoryLanguageAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingCategoryLanguage $vatReturnFillingCategoryLanguage */
        $vatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepository->findWithoutFail($id);

        if (empty($vatReturnFillingCategoryLanguage)) {
            return $this->sendError('Vat Return Filling Category Language not found');
        }

        $vatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepository->update($input, $id);

        return $this->sendResponse($vatReturnFillingCategoryLanguage->toArray(), 'VatReturnFillingCategoryLanguage updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/vatReturnFillingCategoryLanguages/{id}",
     *      summary="deleteVatReturnFillingCategoryLanguage",
     *      tags={"VatReturnFillingCategoryLanguage"},
     *      description="Delete VatReturnFillingCategoryLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingCategoryLanguage",
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
        /** @var VatReturnFillingCategoryLanguage $vatReturnFillingCategoryLanguage */
        $vatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepository->findWithoutFail($id);

        if (empty($vatReturnFillingCategoryLanguage)) {
            return $this->sendError('Vat Return Filling Category Language not found');
        }

        $vatReturnFillingCategoryLanguage->delete();

        return $this->sendSuccess('Vat Return Filling Category Language deleted successfully');
    }
}
