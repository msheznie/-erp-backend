<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentMasterTranslationsAPIRequest;
use App\Http\Requests\API\UpdateDepartmentMasterTranslationsAPIRequest;
use App\Models\DepartmentMasterTranslations;
use App\Repositories\DepartmentMasterTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DepartmentMasterTranslationsController
 * @package App\Http\Controllers\API
 */

class DepartmentMasterTranslationsAPIController extends AppBaseController
{
    /** @var  DepartmentMasterTranslationsRepository */
    private $departmentMasterTranslationsRepository;

    public function __construct(DepartmentMasterTranslationsRepository $departmentMasterTranslationsRepo)
    {
        $this->departmentMasterTranslationsRepository = $departmentMasterTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/departmentMasterTranslations",
     *      summary="getDepartmentMasterTranslationsList",
     *      tags={"DepartmentMasterTranslations"},
     *      description="Get all DepartmentMasterTranslations",
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
     *                  @OA\Items(ref="#/definitions/DepartmentMasterTranslations")
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
        $this->departmentMasterTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->departmentMasterTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $departmentMasterTranslations = $this->departmentMasterTranslationsRepository->all();

        return $this->sendResponse($departmentMasterTranslations->toArray(), 'Department Master Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/departmentMasterTranslations",
     *      summary="createDepartmentMasterTranslations",
     *      tags={"DepartmentMasterTranslations"},
     *      description="Create DepartmentMasterTranslations",
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
     *                  ref="#/definitions/DepartmentMasterTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepartmentMasterTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $departmentMasterTranslations = $this->departmentMasterTranslationsRepository->create($input);

        return $this->sendResponse($departmentMasterTranslations->toArray(), 'Department Master Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/departmentMasterTranslations/{id}",
     *      summary="getDepartmentMasterTranslationsItem",
     *      tags={"DepartmentMasterTranslations"},
     *      description="Get DepartmentMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentMasterTranslations",
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
     *                  ref="#/definitions/DepartmentMasterTranslations"
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
        /** @var DepartmentMasterTranslations $departmentMasterTranslations */
        $departmentMasterTranslations = $this->departmentMasterTranslationsRepository->findWithoutFail($id);

        if (empty($departmentMasterTranslations)) {
            return $this->sendError('Department Master Translations not found');
        }

        return $this->sendResponse($departmentMasterTranslations->toArray(), 'Department Master Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/departmentMasterTranslations/{id}",
     *      summary="updateDepartmentMasterTranslations",
     *      tags={"DepartmentMasterTranslations"},
     *      description="Update DepartmentMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentMasterTranslations",
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
     *                  ref="#/definitions/DepartmentMasterTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepartmentMasterTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepartmentMasterTranslations $departmentMasterTranslations */
        $departmentMasterTranslations = $this->departmentMasterTranslationsRepository->findWithoutFail($id);

        if (empty($departmentMasterTranslations)) {
            return $this->sendError('Department Master Translations not found');
        }

        $departmentMasterTranslations = $this->departmentMasterTranslationsRepository->update($input, $id);

        return $this->sendResponse($departmentMasterTranslations->toArray(), 'DepartmentMasterTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/departmentMasterTranslations/{id}",
     *      summary="deleteDepartmentMasterTranslations",
     *      tags={"DepartmentMasterTranslations"},
     *      description="Delete DepartmentMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentMasterTranslations",
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
        /** @var DepartmentMasterTranslations $departmentMasterTranslations */
        $departmentMasterTranslations = $this->departmentMasterTranslationsRepository->findWithoutFail($id);

        if (empty($departmentMasterTranslations)) {
            return $this->sendError('Department Master Translations not found');
        }

        $departmentMasterTranslations->delete();

        return $this->sendSuccess('Department Master Translations deleted successfully');
    }
}
