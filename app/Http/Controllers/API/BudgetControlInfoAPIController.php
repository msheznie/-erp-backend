<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetControlInfoAPIRequest;
use App\Http\Requests\API\UpdateBudgetControlInfoAPIRequest;
use App\Models\BudgetControlInfo;
use App\Repositories\BudgetControlInfoRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetControlInfoController
 * @package App\Http\Controllers\API
 */

class BudgetControlInfoAPIController extends AppBaseController
{
    /** @var  BudgetControlInfoRepository */
    private $budgetControlInfoRepository;

    public function __construct(BudgetControlInfoRepository $budgetControlInfoRepo)
    {
        $this->budgetControlInfoRepository = $budgetControlInfoRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetControlInfos",
     *      summary="getBudgetControlInfoList",
     *      tags={"BudgetControlInfo"},
     *      description="Get all BudgetControlInfos",
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
     *                  @OA\Items(ref="#/definitions/BudgetControlInfo")
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
        $this->budgetControlInfoRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetControlInfoRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetControlInfos = $this->budgetControlInfoRepository->all();

        return $this->sendResponse($budgetControlInfos->toArray(), trans('custom.budget_control_infos_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/budgetControlInfos",
     *      summary="createBudgetControlInfo",
     *      tags={"BudgetControlInfo"},
     *      description="Create BudgetControlInfo",
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
     *                  ref="#/definitions/BudgetControlInfo"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetControlInfoAPIRequest $request)
    {
        $input = $request->all();

        $budgetControlInfo = $this->budgetControlInfoRepository->create($input);

        return $this->sendResponse($budgetControlInfo->toArray(), trans('custom.budget_control_info_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetControlInfos/{id}",
     *      summary="getBudgetControlInfoItem",
     *      tags={"BudgetControlInfo"},
     *      description="Get BudgetControlInfo",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetControlInfo",
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
     *                  ref="#/definitions/BudgetControlInfo"
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
        /** @var BudgetControlInfo $budgetControlInfo */
        $budgetControlInfo = $this->budgetControlInfoRepository->findWithoutFail($id);

        if (empty($budgetControlInfo)) {
            return $this->sendError(trans('custom.budget_control_info_not_found'));
        }

        return $this->sendResponse($budgetControlInfo->toArray(), trans('custom.budget_control_info_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/budgetControlInfos/{id}",
     *      summary="updateBudgetControlInfo",
     *      tags={"BudgetControlInfo"},
     *      description="Update BudgetControlInfo",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetControlInfo",
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
     *                  ref="#/definitions/BudgetControlInfo"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetControlInfoAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetControlInfo $budgetControlInfo */
        $budgetControlInfo = $this->budgetControlInfoRepository->findWithoutFail($id);

        if (empty($budgetControlInfo)) {
            return $this->sendError(trans('custom.budget_control_info_not_found'));
        }

        $budgetControlInfo = $this->budgetControlInfoRepository->update($input, $id);

        return $this->sendResponse($budgetControlInfo->toArray(), trans('custom.budgetcontrolinfo_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/budgetControlInfos/{id}",
     *      summary="deleteBudgetControlInfo",
     *      tags={"BudgetControlInfo"},
     *      description="Delete BudgetControlInfo",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetControlInfo",
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
        /** @var BudgetControlInfo $budgetControlInfo */
        $budgetControlInfo = $this->budgetControlInfoRepository->findWithoutFail($id);

        if (empty($budgetControlInfo)) {
            return $this->sendError(trans('custom.budget_control_info_not_found'));
        }

        $budgetControlInfo->delete();

        return $this->sendSuccess('Budget Control Info deleted successfully');
    }


    public function getBudgetControl(Request $request) {
        $companyId = $request['companyId'];

        $output = BudgetControlInfo::selectRaw('*,0 as expanded')->where('companySystemID', $companyId)
            ->with('gl_links')
            ->get();



        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
