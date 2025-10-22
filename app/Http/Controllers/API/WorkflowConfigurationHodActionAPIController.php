<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkflowConfigurationHodActionAPIRequest;
use App\Http\Requests\API\UpdateWorkflowConfigurationHodActionAPIRequest;
use App\Models\WorkflowConfigurationHodAction;
use App\Repositories\WorkflowConfigurationHodActionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WorkflowConfigurationHodActionController
 * @package App\Http\Controllers\API
 */

class WorkflowConfigurationHodActionAPIController extends AppBaseController
{
    /** @var  WorkflowConfigurationHodActionRepository */
    private $workflowConfigurationHodActionRepository;

    public function __construct(WorkflowConfigurationHodActionRepository $workflowConfigurationHodActionRepo)
    {
        $this->workflowConfigurationHodActionRepository = $workflowConfigurationHodActionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/workflowConfigurationHodActions",
     *      summary="getWorkflowConfigurationHodActionList",
     *      tags={"WorkflowConfigurationHodAction"},
     *      description="Get all WorkflowConfigurationHodActions",
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
     *                  @OA\Items(ref="#/definitions/WorkflowConfigurationHodAction")
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
        $this->workflowConfigurationHodActionRepository->pushCriteria(new RequestCriteria($request));
        $this->workflowConfigurationHodActionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $workflowConfigurationHodActions = $this->workflowConfigurationHodActionRepository->all();

        return $this->sendResponse($workflowConfigurationHodActions->toArray(), trans('custom.workflow_configuration_hod_actions_retrieved_succe'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/workflowConfigurationHodActions",
     *      summary="createWorkflowConfigurationHodAction",
     *      tags={"WorkflowConfigurationHodAction"},
     *      description="Create WorkflowConfigurationHodAction",
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
     *                  ref="#/definitions/WorkflowConfigurationHodAction"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWorkflowConfigurationHodActionAPIRequest $request)
    {
        $input = $request->all();

        $workflowConfigurationHodAction = $this->workflowConfigurationHodActionRepository->create($input);

        return $this->sendResponse($workflowConfigurationHodAction->toArray(), trans('custom.workflow_configuration_hod_action_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/workflowConfigurationHodActions/{id}",
     *      summary="getWorkflowConfigurationHodActionItem",
     *      tags={"WorkflowConfigurationHodAction"},
     *      description="Get WorkflowConfigurationHodAction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of WorkflowConfigurationHodAction",
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
     *                  ref="#/definitions/WorkflowConfigurationHodAction"
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
        /** @var WorkflowConfigurationHodAction $workflowConfigurationHodAction */
        $workflowConfigurationHodAction = $this->workflowConfigurationHodActionRepository->findWithoutFail($id);

        if (empty($workflowConfigurationHodAction)) {
            return $this->sendError(trans('custom.workflow_configuration_hod_action_not_found'));
        }

        return $this->sendResponse($workflowConfigurationHodAction->toArray(), trans('custom.workflow_configuration_hod_action_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/workflowConfigurationHodActions/{id}",
     *      summary="updateWorkflowConfigurationHodAction",
     *      tags={"WorkflowConfigurationHodAction"},
     *      description="Update WorkflowConfigurationHodAction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of WorkflowConfigurationHodAction",
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
     *                  ref="#/definitions/WorkflowConfigurationHodAction"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWorkflowConfigurationHodActionAPIRequest $request)
    {
        $input = $request->all();

        /** @var WorkflowConfigurationHodAction $workflowConfigurationHodAction */
        $workflowConfigurationHodAction = $this->workflowConfigurationHodActionRepository->findWithoutFail($id);

        if (empty($workflowConfigurationHodAction)) {
            return $this->sendError(trans('custom.workflow_configuration_hod_action_not_found'));
        }

        $workflowConfigurationHodAction = $this->workflowConfigurationHodActionRepository->update($input, $id);

        return $this->sendResponse($workflowConfigurationHodAction->toArray(), trans('custom.workflowconfigurationhodaction_updated_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/workflowConfigurationHodActions/{id}",
     *      summary="deleteWorkflowConfigurationHodAction",
     *      tags={"WorkflowConfigurationHodAction"},
     *      description="Delete WorkflowConfigurationHodAction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of WorkflowConfigurationHodAction",
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
        /** @var WorkflowConfigurationHodAction $workflowConfigurationHodAction */
        $workflowConfigurationHodAction = $this->workflowConfigurationHodActionRepository->findWithoutFail($id);

        if (empty($workflowConfigurationHodAction)) {
            return $this->sendError(trans('custom.workflow_configuration_hod_action_not_found'));
        }

        $workflowConfigurationHodAction->delete();

        return $this->sendSuccess('Workflow Configuration Hod Action deleted successfully');
    }
}
