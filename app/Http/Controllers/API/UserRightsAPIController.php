<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserRightsAPIRequest;
use App\Http\Requests\API\UpdateUserRightsAPIRequest;
use App\Models\UserRights;
use App\Repositories\UserRightsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserRightsController
 * @package App\Http\Controllers\API
 */

class UserRightsAPIController extends AppBaseController
{
    /** @var  UserRightsRepository */
    private $userRightsRepository;

    public function __construct(UserRightsRepository $userRightsRepo)
    {
        $this->userRightsRepository = $userRightsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/userRights",
     *      summary="Get a listing of the UserRights.",
     *      tags={"UserRights"},
     *      description="Get all UserRights",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/UserRights")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->userRightsRepository->pushCriteria(new RequestCriteria($request));
        $this->userRightsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userRights = $this->userRightsRepository->all();

        return $this->sendResponse($userRights->toArray(), trans('custom.user_rights_retrieved_successfully'));
    }

    /**
     * @param CreateUserRightsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/userRights",
     *      summary="Store a newly created UserRights in storage",
     *      tags={"UserRights"},
     *      description="Store UserRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserRights that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserRights")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/UserRights"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserRightsAPIRequest $request)
    {
        $input = $request->all();

        $userRights = $this->userRightsRepository->create($input);

        return $this->sendResponse($userRights->toArray(), trans('custom.user_rights_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/userRights/{id}",
     *      summary="Display the specified UserRights",
     *      tags={"UserRights"},
     *      description="Get UserRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserRights",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/UserRights"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var UserRights $userRights */
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            return $this->sendError(trans('custom.user_rights_not_found'));
        }

        return $this->sendResponse($userRights->toArray(), trans('custom.user_rights_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateUserRightsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/userRights/{id}",
     *      summary="Update the specified UserRights in storage",
     *      tags={"UserRights"},
     *      description="Update UserRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserRights",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserRights that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserRights")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/UserRights"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserRightsAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserRights $userRights */
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            return $this->sendError(trans('custom.user_rights_not_found'));
        }

        $userRights = $this->userRightsRepository->update($input, $id);

        return $this->sendResponse($userRights->toArray(), trans('custom.userrights_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/userRights/{id}",
     *      summary="Remove the specified UserRights from storage",
     *      tags={"UserRights"},
     *      description="Delete UserRights",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserRights",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var UserRights $userRights */
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            return $this->sendError(trans('custom.user_rights_not_found'));
        }

        $userRights->delete();

        return $this->sendResponse($id, trans('custom.user_rights_deleted_successfully'));
    }
}
