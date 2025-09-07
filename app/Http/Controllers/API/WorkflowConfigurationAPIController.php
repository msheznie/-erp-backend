<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkflowConfigurationAPIRequest;
use App\Http\Requests\API\UpdateWorkflowConfigurationAPIRequest;
use App\Models\Company;
use App\Models\HodAction;
use App\Models\WorkflowConfiguration;
use App\Models\WorkflowConfigurationHodAction;
use App\Repositories\WorkflowConfigurationRepository;
use App\Traits\AuditLogsTrait;
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
    use AuditLogsTrait;
    
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

        return $this->sendResponse($workflowConfigurations->toArray(), trans('custom.workflow_configurations_retrieved_successfully'));
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
            'allocation' => 'required_if:method,2',
            'finalApproval' => 'required',
            'hodActions' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        // Custom validation for workflowName uniqueness
        $existingWorkflow = WorkflowConfiguration::where('workflowName', $input['workflowName'])
            ->where('companySystemID', $input['companySystemID'])
            ->exists();

        if ($existingWorkflow) {
            return $this->sendError(trans('custom.workflow_name_already_exists_please_enter_a_unique'), 500);
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
        // Add audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');


         // Get created workflow with HOD actions for audit log
         $createdWorkflow = $this->workflowConfigurationRepository->with('hodActions')->findWithoutFail($workflowConfiguration->id);
         $newValues = $createdWorkflow->toArray();
         
         $this->auditLog(
             $db, 
             $workflowConfiguration->id, 
             $uuid, 
             "erp_workflow_configurations",
             "Workflow Configuration ".$input['workflowName']." has been created",
             "C", 
             $newValues, 
             []
         );

        foreach ($input['hodActions'] as $hodAction) {
            $hodAction['workflowConfigurationID'] = $workflowConfiguration->id;
            $hodAction['hodActionID'] = $hodAction['id'];
            $hodAction['parent'] = $hodAction['parentHod'];
            $hodAction['child'] = $hodAction['childHod'];
            $result = WorkflowConfigurationHodAction::create($hodAction);

            // Add audit log
            $this->auditLog(
                $db, 
                $result->id, 
                $uuid, 
                "erp_workflow_configuration_hod_actions",
                "HOD Action has been Added to Workflow Configuration",
                "C", 
                $hodAction, 
                [],
                $workflowConfiguration->id,
                "erp_workflow_configurations"
            );
        }

        return $this->sendResponse($workflowConfiguration->toArray(), trans('custom.workflow_configuration_saved_successfully'));
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
            return $this->sendError(trans('custom.workflow_configuration_not_found'));
        }

        return $this->sendResponse($workflowConfiguration->toArray(), trans('custom.workflow_configuration_retrieved_successfully'));
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
            'allocation' => 'required_if:method,2',
            'finalApproval' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        // Custom validation for workflowName uniqueness (excluding current record)
        $existingWorkflow = WorkflowConfiguration::where('workflowName', $input['workflowName'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingWorkflow) {
            return $this->sendError(trans('custom.workflow_name_already_exists_please_enter_a_unique'), 500);
        }

        $data = array_except($input, ['hodActions']);
        $data = $this->convertArrayToValue($data);

        /** @var WorkflowConfiguration $workflowConfiguration */
        $workflowConfiguration = $this->workflowConfigurationRepository->findWithoutFail($id);

        if (empty($workflowConfiguration)) {
            return $this->sendError(trans('custom.workflow_configuration_not_found'));
        }

        // Store old values for audit log
        $oldValues = $workflowConfiguration->toArray();
        $oldHodActions = $workflowConfiguration->hodActions()->get()->toArray();

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }
        $data['companyID'] = $company->CompanyID;

        // Get audit log parameters
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');

        $workflowConfiguration = $this->workflowConfigurationRepository->update($data, $id);
        // Delete old HOD actions and audit log them as deletions
        foreach ($oldHodActions as $oldHodAction) {
            $this->auditLog(
                $db, 
                $oldHodAction['id'], 
                $uuid, 
                "erp_workflow_configuration_hod_actions",
                "HOD Action has been deleted during workflow update",
                "D", 
                [], 
                $oldHodAction,
                $id,
                "erp_workflow_configurations"
            );
        }

        $workflowConfiguration->hodActions()->delete();

        // Create new HOD actions and audit log them as creations
        foreach ($input['hodActions'] as $hodAction) {
            $hodAction['workflowConfigurationID'] = $id;
            $hodAction['hodActionID'] = $hodAction['id'];
            $hodAction['parent'] = $hodAction['parentHod'];
            $hodAction['child'] = $hodAction['childHod'];
            $newHodAction = WorkflowConfigurationHodAction::create($hodAction);
            
            // Audit log the new HOD action
            $this->auditLog(
                $db, 
                $newHodAction->id, 
                $uuid, 
                "erp_workflow_configuration_hod_actions",
                "HOD Action has been created during workflow update",
                "C", 
                $newHodAction->toArray(), 
                [],
                $id,
                "erp_workflow_configurations"
            );
        }

        // Get updated values for audit log
        $updatedWorkflow = $this->workflowConfigurationRepository->findWithoutFail($id);
        $newValues = $updatedWorkflow->toArray();

        // Add audit log for workflow configuration changes
        $this->auditLog(
            $db, 
            $id, 
            $uuid, 
            "erp_workflow_configurations", 
            "Workflow Configuration ".$input['workflowName']." has been updated",
            "U", 
            $newValues, 
            $oldValues
        );

        return $this->sendResponse($workflowConfiguration->toArray(), trans('custom.workflow_configuration_updated_successfully'));
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
    public function destroy($id, Request $request)
    {
        /** @var WorkflowConfiguration $workflowConfiguration */
        $workflowConfiguration = $this->workflowConfigurationRepository->findWithoutFail($id);

        if (empty($workflowConfiguration)) {
            return $this->sendError(trans('custom.workflow_configuration_not_found'));
        }

        // Store old values for audit log
        $oldValues = $workflowConfiguration->toArray();
        $oldHodActions = $workflowConfiguration->hodActions()->get()->toArray();

        // Get audit log parameters
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');

        // Audit log individual HOD action deletions
        foreach ($oldHodActions as $oldHodAction) {
            $this->auditLog(
                $db, 
                $oldHodAction['id'], 
                $uuid, 
                "erp_workflow_configuration_hod_actions",
                "HOD Action has been deleted during workflow deletion",
                "D", 
                [], 
                $oldHodAction,
                $id,
                "erp_workflow_configurations"
            );
        }

        $workflowConfiguration->hodActions()->delete();

        $workflowConfiguration->delete();

        // Add audit log for workflow configuration deletion
        $this->auditLog(
            $db, 
            $id, 
            $uuid, 
            "erp_workflow_configurations",
            "Workflow Configuration ".$oldValues['workflowName']." has been deleted",
            "D", 
            [], 
            $oldValues
        );

        return $this->sendResponse(null,trans('custom.workflow_configuration_deleted_successfully'));
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
            return $this->sendError(trans('custom.workflow_configuration_not_found'));
        }

        $oldValue = $workflowConfiguration->toArray();

        $workflowConfiguration->isActive = $input['isActive'];
        $workflowConfiguration->save();

        $newValue = $workflowConfiguration->refresh()->toArray();

        // Add audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog(
            $db,
            $input['id'],
            $uuid,
            "erp_workflow_configurations",
            "Workflow Configuration ".$workflowConfiguration->workflowName." has been updated",
            "U",
            $newValue,
            $oldValue
        );

        return $this->sendResponse($newValue, trans('custom.workflowconfiguration_updated_successfully'));
    }
}
