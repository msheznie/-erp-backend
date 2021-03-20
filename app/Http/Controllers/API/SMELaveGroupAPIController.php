<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMELaveGroupAPIRequest;
use App\Http\Requests\API\UpdateSMELaveGroupAPIRequest;
use App\Models\SMELaveGroup;
use App\Repositories\SMELaveGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMELaveGroupController
 * @package App\Http\Controllers\API
 */

class SMELaveGroupAPIController extends AppBaseController
{
    /** @var  SMELaveGroupRepository */
    private $sMELaveGroupRepository;

    public function __construct(SMELaveGroupRepository $sMELaveGroupRepo)
    {
        $this->sMELaveGroupRepository = $sMELaveGroupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMELaveGroups",
     *      summary="Get a listing of the SMELaveGroups.",
     *      tags={"SMELaveGroup"},
     *      description="Get all SMELaveGroups",
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
     *                  @SWG\Items(ref="#/definitions/SMELaveGroup")
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
        $this->sMELaveGroupRepository->pushCriteria(new RequestCriteria($request));
        $this->sMELaveGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMELaveGroups = $this->sMELaveGroupRepository->all();

        return $this->sendResponse($sMELaveGroups->toArray(), 'S M E Lave Groups retrieved successfully');
    }

    /**
     * @param CreateSMELaveGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMELaveGroups",
     *      summary="Store a newly created SMELaveGroup in storage",
     *      tags={"SMELaveGroup"},
     *      description="Store SMELaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMELaveGroup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMELaveGroup")
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
     *                  ref="#/definitions/SMELaveGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMELaveGroupAPIRequest $request)
    {
        $input = $request->all();

        $sMELaveGroup = $this->sMELaveGroupRepository->create($input);

        return $this->sendResponse($sMELaveGroup->toArray(), 'S M E Lave Group saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMELaveGroups/{id}",
     *      summary="Display the specified SMELaveGroup",
     *      tags={"SMELaveGroup"},
     *      description="Get SMELaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMELaveGroup",
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
     *                  ref="#/definitions/SMELaveGroup"
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
        /** @var SMELaveGroup $sMELaveGroup */
        $sMELaveGroup = $this->sMELaveGroupRepository->findWithoutFail($id);

        if (empty($sMELaveGroup)) {
            return $this->sendError('S M E Lave Group not found');
        }

        return $this->sendResponse($sMELaveGroup->toArray(), 'S M E Lave Group retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSMELaveGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMELaveGroups/{id}",
     *      summary="Update the specified SMELaveGroup in storage",
     *      tags={"SMELaveGroup"},
     *      description="Update SMELaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMELaveGroup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMELaveGroup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMELaveGroup")
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
     *                  ref="#/definitions/SMELaveGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMELaveGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMELaveGroup $sMELaveGroup */
        $sMELaveGroup = $this->sMELaveGroupRepository->findWithoutFail($id);

        if (empty($sMELaveGroup)) {
            return $this->sendError('S M E Lave Group not found');
        }

        $sMELaveGroup = $this->sMELaveGroupRepository->update($input, $id);

        return $this->sendResponse($sMELaveGroup->toArray(), 'SMELaveGroup updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMELaveGroups/{id}",
     *      summary="Remove the specified SMELaveGroup from storage",
     *      tags={"SMELaveGroup"},
     *      description="Delete SMELaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMELaveGroup",
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
        /** @var SMELaveGroup $sMELaveGroup */
        $sMELaveGroup = $this->sMELaveGroupRepository->findWithoutFail($id);

        if (empty($sMELaveGroup)) {
            return $this->sendError('S M E Lave Group not found');
        }

        $sMELaveGroup->delete();

        return $this->sendSuccess('S M E Lave Group deleted successfully');
    }
}
