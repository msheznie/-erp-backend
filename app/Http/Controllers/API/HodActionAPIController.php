<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHodActionAPIRequest;
use App\Http\Requests\API\UpdateHodActionAPIRequest;
use App\Models\HodAction;
use App\Repositories\HodActionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HodActionController
 * @package App\Http\Controllers\API
 */

class HodActionAPIController extends AppBaseController
{
    /** @var  HodActionRepository */
    private $hodActionRepository;

    public function __construct(HodActionRepository $hodActionRepo)
    {
        $this->hodActionRepository = $hodActionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hodActions",
     *      summary="getHodActionList",
     *      tags={"HodAction"},
     *      description="Get all HodActions",
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
     *                  @OA\Items(ref="#/definitions/HodAction")
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
        $this->hodActionRepository->pushCriteria(new RequestCriteria($request));
        $this->hodActionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hodActions = $this->hodActionRepository->all();

        return $this->sendResponse($hodActions->toArray(), trans('custom.hod_actions_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hodActions",
     *      summary="createHodAction",
     *      tags={"HodAction"},
     *      description="Create HodAction",
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
     *                  ref="#/definitions/HodAction"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHodActionAPIRequest $request)
    {
        $input = $request->all();

        $hodAction = $this->hodActionRepository->create($input);

        return $this->sendResponse($hodAction->toArray(), trans('custom.hod_action_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hodActions/{id}",
     *      summary="getHodActionItem",
     *      tags={"HodAction"},
     *      description="Get HodAction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HodAction",
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
     *                  ref="#/definitions/HodAction"
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
        /** @var HodAction $hodAction */
        $hodAction = $this->hodActionRepository->findWithoutFail($id);

        if (empty($hodAction)) {
            return $this->sendError(trans('custom.hod_action_not_found'));
        }

        return $this->sendResponse($hodAction->toArray(), trans('custom.hod_action_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hodActions/{id}",
     *      summary="updateHodAction",
     *      tags={"HodAction"},
     *      description="Update HodAction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HodAction",
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
     *                  ref="#/definitions/HodAction"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHodActionAPIRequest $request)
    {
        $input = $request->all();

        /** @var HodAction $hodAction */
        $hodAction = $this->hodActionRepository->findWithoutFail($id);

        if (empty($hodAction)) {
            return $this->sendError(trans('custom.hod_action_not_found'));
        }

        $hodAction = $this->hodActionRepository->update($input, $id);

        return $this->sendResponse($hodAction->toArray(), trans('custom.hodaction_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hodActions/{id}",
     *      summary="deleteHodAction",
     *      tags={"HodAction"},
     *      description="Delete HodAction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HodAction",
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
        /** @var HodAction $hodAction */
        $hodAction = $this->hodActionRepository->findWithoutFail($id);

        if (empty($hodAction)) {
            return $this->sendError(trans('custom.hod_action_not_found'));
        }

        $hodAction->delete();

        return $this->sendSuccess('Hod Action deleted successfully');
    }
}
