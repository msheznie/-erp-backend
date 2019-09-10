<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLeaveDocumentApprovedAPIRequest;
use App\Http\Requests\API\UpdateLeaveDocumentApprovedAPIRequest;
use App\Models\LeaveDocumentApproved;
use App\Repositories\LeaveDocumentApprovedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveDocumentApprovedController
 * @package App\Http\Controllers\API
 */

class LeaveDocumentApprovedAPIController extends AppBaseController
{
    /** @var  LeaveDocumentApprovedRepository */
    private $leaveDocumentApprovedRepository;

    public function __construct(LeaveDocumentApprovedRepository $leaveDocumentApprovedRepo)
    {
        $this->leaveDocumentApprovedRepository = $leaveDocumentApprovedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDocumentApproveds",
     *      summary="Get a listing of the LeaveDocumentApproveds.",
     *      tags={"LeaveDocumentApproved"},
     *      description="Get all LeaveDocumentApproveds",
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
     *                  @SWG\Items(ref="#/definitions/LeaveDocumentApproved")
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
        $this->leaveDocumentApprovedRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveDocumentApprovedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveDocumentApproveds = $this->leaveDocumentApprovedRepository->all();

        return $this->sendResponse($leaveDocumentApproveds->toArray(), 'Leave Document Approveds retrieved successfully');
    }

    /**
     * @param CreateLeaveDocumentApprovedAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveDocumentApproveds",
     *      summary="Store a newly created LeaveDocumentApproved in storage",
     *      tags={"LeaveDocumentApproved"},
     *      description="Store LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDocumentApproved that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDocumentApproved")
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
     *                  ref="#/definitions/LeaveDocumentApproved"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->create($input);

        return $this->sendResponse($leaveDocumentApproved->toArray(), 'Leave Document Approved saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDocumentApproveds/{id}",
     *      summary="Display the specified LeaveDocumentApproved",
     *      tags={"LeaveDocumentApproved"},
     *      description="Get LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDocumentApproved",
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
     *                  ref="#/definitions/LeaveDocumentApproved"
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
        /** @var LeaveDocumentApproved $leaveDocumentApproved */
        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->findWithoutFail($id);

        if (empty($leaveDocumentApproved)) {
            return $this->sendError('Leave Document Approved not found');
        }

        return $this->sendResponse($leaveDocumentApproved->toArray(), 'Leave Document Approved retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLeaveDocumentApprovedAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveDocumentApproveds/{id}",
     *      summary="Update the specified LeaveDocumentApproved in storage",
     *      tags={"LeaveDocumentApproved"},
     *      description="Update LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDocumentApproved",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDocumentApproved that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDocumentApproved")
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
     *                  ref="#/definitions/LeaveDocumentApproved"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveDocumentApproved $leaveDocumentApproved */
        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->findWithoutFail($id);

        if (empty($leaveDocumentApproved)) {
            return $this->sendError('Leave Document Approved not found');
        }

        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->update($input, $id);

        return $this->sendResponse($leaveDocumentApproved->toArray(), 'LeaveDocumentApproved updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveDocumentApproveds/{id}",
     *      summary="Remove the specified LeaveDocumentApproved from storage",
     *      tags={"LeaveDocumentApproved"},
     *      description="Delete LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDocumentApproved",
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
        /** @var LeaveDocumentApproved $leaveDocumentApproved */
        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->findWithoutFail($id);

        if (empty($leaveDocumentApproved)) {
            return $this->sendError('Leave Document Approved not found');
        }

        $leaveDocumentApproved->delete();

        return $this->sendResponse($id, 'Leave Document Approved deleted successfully');
    }
}
