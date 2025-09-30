<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserTypeAPIRequest;
use App\Http\Requests\API\UpdateUserTypeAPIRequest;
use App\Models\UserType;
use App\Repositories\UserTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserTypeController
 * @package App\Http\Controllers\API
 */

class UserTypeAPIController extends AppBaseController
{
    /** @var  UserTypeRepository */
    private $userTypeRepository;

    public function __construct(UserTypeRepository $userTypeRepo)
    {
        $this->userTypeRepository = $userTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/userTypes",
     *      summary="getUserTypeList",
     *      tags={"UserType"},
     *      description="Get all UserTypes",
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
     *                  @OA\Items(ref="#/definitions/UserType")
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
        $this->userTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->userTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userTypes = $this->userTypeRepository->all();

        return $this->sendResponse($userTypes->toArray(), trans('custom.user_types_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/userTypes",
     *      summary="createUserType",
     *      tags={"UserType"},
     *      description="Create UserType",
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
     *                  ref="#/definitions/UserType"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserTypeAPIRequest $request)
    {
        $input = $request->all();

        $userType = $this->userTypeRepository->create($input);

        return $this->sendResponse($userType->toArray(), trans('custom.user_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/userTypes/{id}",
     *      summary="getUserTypeItem",
     *      tags={"UserType"},
     *      description="Get UserType",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of UserType",
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
     *                  ref="#/definitions/UserType"
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
        /** @var UserType $userType */
        $userType = $this->userTypeRepository->findWithoutFail($id);

        if (empty($userType)) {
            return $this->sendError(trans('custom.user_type_not_found'));
        }

        return $this->sendResponse($userType->toArray(), trans('custom.user_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/userTypes/{id}",
     *      summary="updateUserType",
     *      tags={"UserType"},
     *      description="Update UserType",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of UserType",
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
     *                  ref="#/definitions/UserType"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserType $userType */
        $userType = $this->userTypeRepository->findWithoutFail($id);

        if (empty($userType)) {
            return $this->sendError(trans('custom.user_type_not_found'));
        }

        $userType = $this->userTypeRepository->update($input, $id);

        return $this->sendResponse($userType->toArray(), trans('custom.usertype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/userTypes/{id}",
     *      summary="deleteUserType",
     *      tags={"UserType"},
     *      description="Delete UserType",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of UserType",
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
        /** @var UserType $userType */
        $userType = $this->userTypeRepository->findWithoutFail($id);

        if (empty($userType)) {
            return $this->sendError(trans('custom.user_type_not_found'));
        }

        $userType->delete();

        return $this->sendSuccess('User Type deleted successfully');
    }
}
