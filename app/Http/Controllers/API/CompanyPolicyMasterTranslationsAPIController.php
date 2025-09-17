<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyPolicyMasterTranslationsAPIRequest;
use App\Http\Requests\API\UpdateCompanyPolicyMasterTranslationsAPIRequest;
use App\Models\CompanyPolicyMasterTranslations;
use App\Repositories\CompanyPolicyMasterTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyPolicyMasterTranslationsController
 * @package App\Http\Controllers\API
 */

class CompanyPolicyMasterTranslationsAPIController extends AppBaseController
{
    /** @var  CompanyPolicyMasterTranslationsRepository */
    private $companyPolicyMasterTranslationsRepository;

    public function __construct(CompanyPolicyMasterTranslationsRepository $companyPolicyMasterTranslationsRepo)
    {
        $this->companyPolicyMasterTranslationsRepository = $companyPolicyMasterTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/companyPolicyMasterTranslations",
     *      summary="getCompanyPolicyMasterTranslationsList",
     *      tags={"CompanyPolicyMasterTranslations"},
     *      description="Get all CompanyPolicyMasterTranslations",
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
     *                  @OA\Items(ref="#/definitions/CompanyPolicyMasterTranslations")
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
        $this->companyPolicyMasterTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->companyPolicyMasterTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepository->all();

        return $this->sendResponse($companyPolicyMasterTranslations->toArray(), 'Company Policy Master Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/companyPolicyMasterTranslations",
     *      summary="createCompanyPolicyMasterTranslations",
     *      tags={"CompanyPolicyMasterTranslations"},
     *      description="Create CompanyPolicyMasterTranslations",
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
     *                  ref="#/definitions/CompanyPolicyMasterTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyPolicyMasterTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $companyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepository->create($input);

        return $this->sendResponse($companyPolicyMasterTranslations->toArray(), 'Company Policy Master Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/companyPolicyMasterTranslations/{id}",
     *      summary="getCompanyPolicyMasterTranslationsItem",
     *      tags={"CompanyPolicyMasterTranslations"},
     *      description="Get CompanyPolicyMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyMasterTranslations",
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
     *                  ref="#/definitions/CompanyPolicyMasterTranslations"
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
        /** @var CompanyPolicyMasterTranslations $companyPolicyMasterTranslations */
        $companyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepository->findWithoutFail($id);

        if (empty($companyPolicyMasterTranslations)) {
            return $this->sendError('Company Policy Master Translations not found');
        }

        return $this->sendResponse($companyPolicyMasterTranslations->toArray(), 'Company Policy Master Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/companyPolicyMasterTranslations/{id}",
     *      summary="updateCompanyPolicyMasterTranslations",
     *      tags={"CompanyPolicyMasterTranslations"},
     *      description="Update CompanyPolicyMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyMasterTranslations",
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
     *                  ref="#/definitions/CompanyPolicyMasterTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyPolicyMasterTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyPolicyMasterTranslations $companyPolicyMasterTranslations */
        $companyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepository->findWithoutFail($id);

        if (empty($companyPolicyMasterTranslations)) {
            return $this->sendError('Company Policy Master Translations not found');
        }

        $companyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepository->update($input, $id);

        return $this->sendResponse($companyPolicyMasterTranslations->toArray(), 'CompanyPolicyMasterTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/companyPolicyMasterTranslations/{id}",
     *      summary="deleteCompanyPolicyMasterTranslations",
     *      tags={"CompanyPolicyMasterTranslations"},
     *      description="Delete CompanyPolicyMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyMasterTranslations",
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
        /** @var CompanyPolicyMasterTranslations $companyPolicyMasterTranslations */
        $companyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepository->findWithoutFail($id);

        if (empty($companyPolicyMasterTranslations)) {
            return $this->sendError('Company Policy Master Translations not found');
        }

        $companyPolicyMasterTranslations->delete();

        return $this->sendSuccess('Company Policy Master Translations deleted successfully');
    }
}
