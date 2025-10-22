<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpensesClaimTypeLanguageAPIRequest;
use App\Http\Requests\API\UpdateExpensesClaimTypeLanguageAPIRequest;
use App\Models\ExpensesClaimTypeLanguage;
use App\Repositories\ExpensesClaimTypeLanguageRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpensesClaimTypeLanguageController
 * @package App\Http\Controllers\API
 */

class ExpensesClaimTypeLanguageAPIController extends AppBaseController
{
    /** @var  ExpensesClaimTypeLanguageRepository */
    private $expensesClaimTypeLanguageRepository;

    public function __construct(ExpensesClaimTypeLanguageRepository $expensesClaimTypeLanguageRepo)
    {
        $this->expensesClaimTypeLanguageRepository = $expensesClaimTypeLanguageRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/expensesClaimTypeLanguages",
     *      summary="getExpensesClaimTypeLanguageList",
     *      tags={"ExpensesClaimTypeLanguage"},
     *      description="Get all ExpensesClaimTypeLanguages",
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
     *                  @OA\Items(ref="#/definitions/ExpensesClaimTypeLanguage")
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
        $this->expensesClaimTypeLanguageRepository->pushCriteria(new RequestCriteria($request));
        $this->expensesClaimTypeLanguageRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expensesClaimTypeLanguages = $this->expensesClaimTypeLanguageRepository->all();

        return $this->sendResponse($expensesClaimTypeLanguages->toArray(), 'Expenses Claim Type Languages retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/expensesClaimTypeLanguages",
     *      summary="createExpensesClaimTypeLanguage",
     *      tags={"ExpensesClaimTypeLanguage"},
     *      description="Create ExpensesClaimTypeLanguage",
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
     *                  ref="#/definitions/ExpensesClaimTypeLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpensesClaimTypeLanguageAPIRequest $request)
    {
        $input = $request->all();

        $expensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepository->create($input);

        return $this->sendResponse($expensesClaimTypeLanguage->toArray(), 'Expenses Claim Type Language saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/expensesClaimTypeLanguages/{id}",
     *      summary="getExpensesClaimTypeLanguageItem",
     *      tags={"ExpensesClaimTypeLanguage"},
     *      description="Get ExpensesClaimTypeLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ExpensesClaimTypeLanguage",
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
     *                  ref="#/definitions/ExpensesClaimTypeLanguage"
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
        /** @var ExpensesClaimTypeLanguage $expensesClaimTypeLanguage */
        $expensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepository->findWithoutFail($id);

        if (empty($expensesClaimTypeLanguage)) {
            return $this->sendError('Expenses Claim Type Language not found');
        }

        return $this->sendResponse($expensesClaimTypeLanguage->toArray(), 'Expenses Claim Type Language retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/expensesClaimTypeLanguages/{id}",
     *      summary="updateExpensesClaimTypeLanguage",
     *      tags={"ExpensesClaimTypeLanguage"},
     *      description="Update ExpensesClaimTypeLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ExpensesClaimTypeLanguage",
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
     *                  ref="#/definitions/ExpensesClaimTypeLanguage"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpensesClaimTypeLanguageAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpensesClaimTypeLanguage $expensesClaimTypeLanguage */
        $expensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepository->findWithoutFail($id);

        if (empty($expensesClaimTypeLanguage)) {
            return $this->sendError('Expenses Claim Type Language not found');
        }

        $expensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepository->update($input, $id);

        return $this->sendResponse($expensesClaimTypeLanguage->toArray(), 'ExpensesClaimTypeLanguage updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/expensesClaimTypeLanguages/{id}",
     *      summary="deleteExpensesClaimTypeLanguage",
     *      tags={"ExpensesClaimTypeLanguage"},
     *      description="Delete ExpensesClaimTypeLanguage",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ExpensesClaimTypeLanguage",
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
        /** @var ExpensesClaimTypeLanguage $expensesClaimTypeLanguage */
        $expensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepository->findWithoutFail($id);

        if (empty($expensesClaimTypeLanguage)) {
            return $this->sendError('Expenses Claim Type Language not found');
        }

        $expensesClaimTypeLanguage->delete();

        return $this->sendSuccess('Expenses Claim Type Language deleted successfully');
    }
}
