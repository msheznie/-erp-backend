<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkflowConfigurationAPIRequest;
use App\Http\Requests\API\UpdateWorkflowConfigurationAPIRequest;
use App\Models\Company;
use App\Models\HodAction;
use App\Models\WorkflowConfiguration;
use App\Models\WorkflowConfigurationHodAction;
use App\Repositories\WorkflowConfigurationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WorkflowConfigurationController
 * @package App\Http\Controllers\API
 */

class WorkflowConfigurationAPIController extends AppBaseController
{
    /** @var  WorkflowConfigurationRepository */
    private $workflowConfigurationRepository;

    public function __construct(WorkflowConfigurationRepository $workflowConfigurationRepo)
    {
        $this->workflowConfigurationRepository = $workflowConfigurationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/workflowConfigurations",
     *      summary="getWorkflowConfigurationList",
     *      tags={"WorkflowConfiguration"},
     *      description="Get all WorkflowConfigurations",
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
     *                  @OA\Items(ref="#/definitions/WorkflowConfiguration")
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
        $this->workflowConfigurationRepository->pushCriteria(new RequestCriteria($request));
        $this->workflowConfigurationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $workflowConfigurations = $this->workflowConfigurationRepository->all();

        return $this->sendResponse($workflowConfigurations->toArray(), 'Workflow Configurations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/workflowConfigurations",
     *      summary="createWorkflowConfiguration",
     *      tags={"WorkflowConfiguration"},
     *      description="Create WorkflowConfiguration",
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
     *                  ref="#/definitions/WorkflowConfiguration"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWorkflowConfigurationAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'workflowName' => 'required',
            'initiateBudget' => 'required',
            'method' => 'required',
            'allocation' => 'required',
            'finalApproval' => 'required',
            'hodActions' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $data = array_except($input, ['hodActions']);
        $data = $this->convertArrayToValue($data);

        $data['isActive'] = 0;

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }

        $data['companyID'] = $company->CompanyID;

        $workflowConfiguration = $this->workflowConfigurationRepository->create($data);

        foreach ($input['hodActions'] as $hodAction) {
            $hodAction['workflowConfigurationID'] = $workflowConfiguration->id;
            $hodAction['hodActionID'] = $hodAction['id'];
            $hodAction['parent'] = $hodAction['parentHod'];
            $hodAction['child'] = $hodAction['childHod'];
            WorkflowConfigurationHodAction::create($hodAction);
        }

        return $this->sendResponse($workflowConfiguration->toArray(), 'Workflow Configuration saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/workflowConfigurations/{id}",
     *      summary="getWorkflowConfigurationItem",
     *      tags={"WorkflowConfiguration"},
     *      description="Get WorkflowConfiguration",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of WorkflowConfiguration",
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
     *                  ref="#/definitions/WorkflowConfiguration"
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
        /** @var WorkflowConfiguration $workflowConfiguration */
        $workflowConfiguration = $this->workflowConfigurationRepository->with('hodActions')->findWithoutFail($id);

        if (empty($workflowConfiguration)) {
            return $this->sendError('Workflow Configuration not found');
        }

        return $this->sendResponse($workflowConfiguration->toArray(), 'Workflow Configuration retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/workflowConfigurations/{id}",
     *      summary="updateWorkflowConfiguration",
     *      tags={"WorkflowConfiguration"},
     *      description="Update WorkflowConfiguration",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of WorkflowConfiguration",
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
     *                  ref="#/definitions/WorkflowConfiguration"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWorkflowConfigurationAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'workflowName' => 'required',
            'initiateBudget' => 'required',
            'method' => 'required',
            'allocation' => 'required',
            'finalApproval' => 'required',
            'hodActions' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $data = array_except($input, ['hodActions']);
        $data = $this->convertArrayToValue($data);

        /** @var WorkflowConfiguration $workflowConfiguration */
        $workflowConfiguration = $this->workflowConfigurationRepository->findWithoutFail($id);

        if (empty($workflowConfiguration)) {
            return $this->sendError('Workflow Configuration not found');
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }
        $data['companyID'] = $company->CompanyID;

        $workflowConfiguration = $this->workflowConfigurationRepository->update($data, $id);
        $workflowConfiguration->hodActions()->delete();

        foreach ($input['hodActions'] as $hodAction) {
            $hodAction['workflowConfigurationID'] = $id;
            $hodAction['hodActionID'] = $hodAction['id'];
            $hodAction['parent'] = $hodAction['parentHod'];
            $hodAction['child'] = $hodAction['childHod'];
            WorkflowConfigurationHodAction::create($hodAction);
        }

        return $this->sendResponse($workflowConfiguration->toArray(), 'WorkflowConfiguration updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/workflowConfigurations/{id}",
     *      summary="deleteWorkflowConfiguration",
     *      tags={"WorkflowConfiguration"},
     *      description="Delete WorkflowConfiguration",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of WorkflowConfiguration",
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
        /** @var WorkflowConfiguration $workflowConfiguration */
        $workflowConfiguration = $this->workflowConfigurationRepository->findWithoutFail($id);

        if (empty($workflowConfiguration)) {
            return $this->sendError('Workflow Configuration not found');
        }

        $workflowConfiguration->hodActions()->delete();

        $workflowConfiguration->delete();

        return $this->sendResponse(null,'Workflow Configuration deleted successfully');
    }

    public function getWorkflowConfiguration(Request $request) {
        $input = $request->all();

        $selectedCompanyId = $input['companyId'] ?? 0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $workflowConfigurations = WorkflowConfiguration::select('*')->where('companySystemID', $selectedCompanyId)->orderBy('id', $sort);
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $workflowConfigurations = $workflowConfigurations->where(function ($query) use ($search) {
                $query->where('workflowName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($workflowConfigurations)
            ->addIndexColumn()
            ->make(true);
    }

    public function getWorkflowConfigurationFormData(Request $request) {
        $input = $request->all();

        $data = [];

        $data['hodActions'] = HodAction::all();

        return $this->sendResponse($data, 'Workflow Configuration Form Data');
    }

    public function changeWorkflowConfigurationStatus(Request $request) {
        $input = $request->all();

        $workflowConfiguration = $this->workflowConfigurationRepository->findWithoutFail($input['id']);

        if (empty($workflowConfiguration)) {
            return $this->sendError('Workflow Configuration not found');
        }

        $workflowConfiguration->isActive = $input['isActive'];
        $workflowConfiguration->save();

        return $this->sendResponse($workflowConfiguration->refresh()->toArray(), 'WorkflowConfiguration updated successfully');
    }
}
