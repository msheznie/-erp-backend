<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatelogUploadBudgetAPIRequest;
use App\Http\Requests\API\UpdatelogUploadBudgetAPIRequest;
use App\Models\logUploadBudget;
use App\Repositories\logUploadBudgetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class logUploadBudgetController
 * @package App\Http\Controllers\API
 */

class logUploadBudgetAPIController extends AppBaseController
{
    /** @var  logUploadBudgetRepository */
    private $logUploadBudgetRepository;

    public function __construct(logUploadBudgetRepository $logUploadBudgetRepo)
    {
        $this->logUploadBudgetRepository = $logUploadBudgetRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/logUploadBudgets",
     *      summary="getlogUploadBudgetList",
     *      tags={"logUploadBudget"},
     *      description="Get all logUploadBudgets",
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
     *                  @OA\Items(ref="#/definitions/logUploadBudget")
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
        $this->logUploadBudgetRepository->pushCriteria(new RequestCriteria($request));
        $this->logUploadBudgetRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logUploadBudgets = $this->logUploadBudgetRepository->all();

        return $this->sendResponse($logUploadBudgets->toArray(), trans('custom.log_upload_budgets_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/logUploadBudgets",
     *      summary="createlogUploadBudget",
     *      tags={"logUploadBudget"},
     *      description="Create logUploadBudget",
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
     *                  ref="#/definitions/logUploadBudget"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatelogUploadBudgetAPIRequest $request)
    {
        $input = $request->all();

        $logUploadBudget = $this->logUploadBudgetRepository->create($input);

        return $this->sendResponse($logUploadBudget->toArray(), trans('custom.log_upload_budget_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/logUploadBudgets/{id}",
     *      summary="getlogUploadBudgetItem",
     *      tags={"logUploadBudget"},
     *      description="Get logUploadBudget",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of logUploadBudget",
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
     *                  ref="#/definitions/logUploadBudget"
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
        /** @var logUploadBudget $logUploadBudget */
        $logUploadBudget = $this->logUploadBudgetRepository->findWithoutFail($id);

        if (empty($logUploadBudget)) {
            return $this->sendError(trans('custom.log_upload_budget_not_found'));
        }

        return $this->sendResponse($logUploadBudget->toArray(), trans('custom.log_upload_budget_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/logUploadBudgets/{id}",
     *      summary="updatelogUploadBudget",
     *      tags={"logUploadBudget"},
     *      description="Update logUploadBudget",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of logUploadBudget",
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
     *                  ref="#/definitions/logUploadBudget"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatelogUploadBudgetAPIRequest $request)
    {
        $input = $request->all();

        /** @var logUploadBudget $logUploadBudget */
        $logUploadBudget = $this->logUploadBudgetRepository->findWithoutFail($id);

        if (empty($logUploadBudget)) {
            return $this->sendError(trans('custom.log_upload_budget_not_found'));
        }

        $logUploadBudget = $this->logUploadBudgetRepository->update($input, $id);

        return $this->sendResponse($logUploadBudget->toArray(), trans('custom.loguploadbudget_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/logUploadBudgets/{id}",
     *      summary="deletelogUploadBudget",
     *      tags={"logUploadBudget"},
     *      description="Delete logUploadBudget",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of logUploadBudget",
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
        /** @var logUploadBudget $logUploadBudget */
        $logUploadBudget = $this->logUploadBudgetRepository->findWithoutFail($id);

        if (empty($logUploadBudget)) {
            return $this->sendError(trans('custom.log_upload_budget_not_found'));
        }

        $logUploadBudget->delete();

        return $this->sendSuccess('Log Upload Budget deleted successfully');
    }
}
