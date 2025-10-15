<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyPolicyCategoryTranslationsAPIRequest;
use App\Http\Requests\API\UpdateCompanyPolicyCategoryTranslationsAPIRequest;
use App\Models\CompanyPolicyCategoryTranslations;
use App\Repositories\CompanyPolicyCategoryTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyPolicyCategoryTranslationsController
 * @package App\Http\Controllers\API
 */

class CompanyPolicyCategoryTranslationsAPIController extends AppBaseController
{
    /** @var  CompanyPolicyCategoryTranslationsRepository */
    private $companyPolicyCategoryTranslationsRepository;

    public function __construct(CompanyPolicyCategoryTranslationsRepository $companyPolicyCategoryTranslationsRepo)
    {
        $this->companyPolicyCategoryTranslationsRepository = $companyPolicyCategoryTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/companyPolicyCategoryTranslations",
     *      summary="getCompanyPolicyCategoryTranslationsList",
     *      tags={"CompanyPolicyCategoryTranslations"},
     *      description="Get all CompanyPolicyCategoryTranslations",
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
     *                  @OA\Items(ref="#/definitions/CompanyPolicyCategoryTranslations")
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
        $this->companyPolicyCategoryTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->companyPolicyCategoryTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepository->all();

        return $this->sendResponse($companyPolicyCategoryTranslations->toArray(), 'Company Policy Category Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/companyPolicyCategoryTranslations",
     *      summary="createCompanyPolicyCategoryTranslations",
     *      tags={"CompanyPolicyCategoryTranslations"},
     *      description="Create CompanyPolicyCategoryTranslations",
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
     *                  ref="#/definitions/CompanyPolicyCategoryTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyPolicyCategoryTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $companyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepository->create($input);

        return $this->sendResponse($companyPolicyCategoryTranslations->toArray(), 'Company Policy Category Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/companyPolicyCategoryTranslations/{id}",
     *      summary="getCompanyPolicyCategoryTranslationsItem",
     *      tags={"CompanyPolicyCategoryTranslations"},
     *      description="Get CompanyPolicyCategoryTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyCategoryTranslations",
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
     *                  ref="#/definitions/CompanyPolicyCategoryTranslations"
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
        /** @var CompanyPolicyCategoryTranslations $companyPolicyCategoryTranslations */
        $companyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepository->findWithoutFail($id);

        if (empty($companyPolicyCategoryTranslations)) {
            return $this->sendError('Company Policy Category Translations not found');
        }

        return $this->sendResponse($companyPolicyCategoryTranslations->toArray(), 'Company Policy Category Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/companyPolicyCategoryTranslations/{id}",
     *      summary="updateCompanyPolicyCategoryTranslations",
     *      tags={"CompanyPolicyCategoryTranslations"},
     *      description="Update CompanyPolicyCategoryTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyCategoryTranslations",
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
     *                  ref="#/definitions/CompanyPolicyCategoryTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyPolicyCategoryTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyPolicyCategoryTranslations $companyPolicyCategoryTranslations */
        $companyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepository->findWithoutFail($id);

        if (empty($companyPolicyCategoryTranslations)) {
            return $this->sendError('Company Policy Category Translations not found');
        }

        $companyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepository->update($input, $id);

        return $this->sendResponse($companyPolicyCategoryTranslations->toArray(), 'CompanyPolicyCategoryTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/companyPolicyCategoryTranslations/{id}",
     *      summary="deleteCompanyPolicyCategoryTranslations",
     *      tags={"CompanyPolicyCategoryTranslations"},
     *      description="Delete CompanyPolicyCategoryTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyCategoryTranslations",
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
        /** @var CompanyPolicyCategoryTranslations $companyPolicyCategoryTranslations */
        $companyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepository->findWithoutFail($id);

        if (empty($companyPolicyCategoryTranslations)) {
            return $this->sendError('Company Policy Category Translations not found');
        }

        $companyPolicyCategoryTranslations->delete();

        return $this->sendSuccess('Company Policy Category Translations deleted successfully');
    }
}
