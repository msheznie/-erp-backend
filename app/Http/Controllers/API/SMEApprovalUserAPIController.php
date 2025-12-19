<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEApprovalUserAPIRequest;
use App\Http\Requests\API\UpdateSMEApprovalUserAPIRequest;
use App\Models\SMEApprovalUser;
use App\Repositories\SMEApprovalUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEApprovalUserController
 * @package App\Http\Controllers\API
 */

class SMEApprovalUserAPIController extends AppBaseController
{
    /** @var  SMEApprovalUserRepository */
    private $sMEApprovalUserRepository;

    public function __construct(SMEApprovalUserRepository $sMEApprovalUserRepo)
    {
        $this->sMEApprovalUserRepository = $sMEApprovalUserRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sMEApprovalUsers",
     *      summary="getSMEApprovalUserList",
     *      tags={"SMEApprovalUser"},
     *      description="Get all SMEApprovalUsers",
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
     *                  @OA\Items(ref="#/definitions/SMEApprovalUser")
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
        $this->sMEApprovalUserRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEApprovalUserRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEApprovalUsers = $this->sMEApprovalUserRepository->all();

        return $this->sendResponse($sMEApprovalUsers->toArray(), trans('custom.s_m_e_approval_users_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sMEApprovalUsers",
     *      summary="createSMEApprovalUser",
     *      tags={"SMEApprovalUser"},
     *      description="Create SMEApprovalUser",
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
     *                  ref="#/definitions/SMEApprovalUser"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEApprovalUserAPIRequest $request)
    {
        $input = $request->all();

        $sMEApprovalUser = $this->sMEApprovalUserRepository->create($input);

        return $this->sendResponse($sMEApprovalUser->toArray(), trans('custom.s_m_e_approval_user_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sMEApprovalUsers/{id}",
     *      summary="getSMEApprovalUserItem",
     *      tags={"SMEApprovalUser"},
     *      description="Get SMEApprovalUser",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SMEApprovalUser",
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
     *                  ref="#/definitions/SMEApprovalUser"
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
        /** @var SMEApprovalUser $sMEApprovalUser */
        $sMEApprovalUser = $this->sMEApprovalUserRepository->findWithoutFail($id);

        if (empty($sMEApprovalUser)) {
            return $this->sendError(trans('custom.s_m_e_approval_user_not_found'));
        }

        return $this->sendResponse($sMEApprovalUser->toArray(), trans('custom.s_m_e_approval_user_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sMEApprovalUsers/{id}",
     *      summary="updateSMEApprovalUser",
     *      tags={"SMEApprovalUser"},
     *      description="Update SMEApprovalUser",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SMEApprovalUser",
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
     *                  ref="#/definitions/SMEApprovalUser"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEApprovalUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEApprovalUser $sMEApprovalUser */
        $sMEApprovalUser = $this->sMEApprovalUserRepository->findWithoutFail($id);

        if (empty($sMEApprovalUser)) {
            return $this->sendError(trans('custom.s_m_e_approval_user_not_found'));
        }

        $sMEApprovalUser = $this->sMEApprovalUserRepository->update($input, $id);

        return $this->sendResponse($sMEApprovalUser->toArray(), trans('custom.smeapprovaluser_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sMEApprovalUsers/{id}",
     *      summary="deleteSMEApprovalUser",
     *      tags={"SMEApprovalUser"},
     *      description="Delete SMEApprovalUser",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SMEApprovalUser",
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
        /** @var SMEApprovalUser $sMEApprovalUser */
        $sMEApprovalUser = $this->sMEApprovalUserRepository->findWithoutFail($id);

        if (empty($sMEApprovalUser)) {
            return $this->sendError(trans('custom.s_m_e_approval_user_not_found'));
        }

        $sMEApprovalUser->delete();

        return $this->sendSuccess('S M E Approval User deleted successfully');
    }
}
