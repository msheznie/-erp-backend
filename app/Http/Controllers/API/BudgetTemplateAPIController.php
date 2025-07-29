<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTemplateAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplateAPIRequest;
use App\Models\BudgetTemplate;
use App\Models\DepartmentBudgetTemplate;
use App\Models\CompanyDepartment;
use App\Models\DepBudgetTemplateGl;
use App\Repositories\BudgetTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\AuditLogsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Class BudgetTemplateController
 * @package App\Http\Controllers\API
 */

class BudgetTemplateAPIController extends AppBaseController
{
    use AuditLogsTrait;
    
    /** @var  BudgetTemplateRepository */
    private $budgetTemplateRepository;

    public function __construct(BudgetTemplateRepository $budgetTemplateRepo)
    {
        $this->budgetTemplateRepository = $budgetTemplateRepo;
    }

    /**
     * Display a listing of the BudgetTemplate.
     * GET|HEAD /budget_templates
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTemplates = $this->budgetTemplateRepository->all();

        return $this->sendResponse($budgetTemplates->toArray(), 'Budget Templates retrieved successfully');
    }

    /**
     * Store a newly created BudgetTemplate in storage.
     * POST /budget_templates
     *
     * @param CreateBudgetTemplateAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetTemplateAPIRequest $request)
    {
        $input = $request->all();
        
        // Set user ID for audit trail
        $input['createdUserSystemID'] = auth()->id();
        
        $budgetTemplate = $this->budgetTemplateRepository->create($input);

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $budgetTemplate->budgetTemplateID, $uuid, "budget_templates", "Budget template ".$budgetTemplate->description." has been created", "C", $budgetTemplate->toArray(), []);

        return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template saved successfully');
    }

    /**
     * Display the specified BudgetTemplate.
     * GET|HEAD /budget_templates/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BudgetTemplate $budgetTemplate */
        $budgetTemplate = $this->budgetTemplateRepository->find($id);

        if (empty($budgetTemplate)) {
            return $this->sendError('Budget Template not found');
        }

        return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template retrieved successfully');
    }

    /**
     * Update the specified BudgetTemplate in storage.
     * PUT/PATCH /budget_templates/{id}
     *
     * @param  int $id
     * @param UpdateBudgetTemplateAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetTemplate $budgetTemplate */
        $budgetTemplate = $this->budgetTemplateRepository->find($id);

        if (empty($budgetTemplate)) {
            return $this->sendError('Budget Template not found');
        }

        $oldValues = $budgetTemplate->toArray();

        if (isset($input['update']) && $input['update'] == 'default') {
            $budgetTemplate->isDefault = $input['isDefault'];
            $budgetTemplate->modifiedUserSystemID = auth()->id();
            $budgetTemplate->save();

            if($input['isDefault'] == 1) {
                //write a service to assign this template to all departments
                $this->assignTemplateToAllDepartments($id); 
            }

            //update all other templates to non-default
            BudgetTemplate::where('budgetTemplateID', '!=', $id)->update(['isDefault' => 0]);
            
            // Audit log for default update
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $id, $uuid, "budget_templates", "Budget template default status updated", "U", $budgetTemplate->toArray(), $oldValues);
            
            return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template updated successfully');
        }

        $companyDepartmentTemplate = DepartmentBudgetTemplate::where('budgetTemplateID', $id)->first();
        if ($companyDepartmentTemplate) {
            return $this->sendError('The template already assigned to the department cannot be amended');
        }

        // Set user ID for audit trail
        $input['modifiedUserSystemID'] = auth()->id();

        $budgetTemplate = $this->budgetTemplateRepository->update($input, $id);

        // Audit log for regular update
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "budget_templates", "Budget template ".$budgetTemplate->description." has been updated", "U", $budgetTemplate->toArray(), $oldValues);

        return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template updated successfully');
    }

    public function assignTemplateToAllDepartments($id)
    {
        $budgetTemplate = $this->budgetTemplateRepository->find($id);
        $departments = CompanyDepartment::where('isActive', 1)->get();
        foreach ($departments as $department) {

            // check if the template is already assigned to the department
            $departmentBudgetTemplateCheck = DepartmentBudgetTemplate::where('budgetTemplateID', $id)
                                                                    ->where('departmentSystemID', $department->departmentSystemID)
                                                                    ->where('isActive', 1)
                                                                    ->first();
            if(!$departmentBudgetTemplateCheck) {
                //check if the template type is already assigned to the department
                $departmentBudgetTemplateCheck = DepartmentBudgetTemplate::whereHas('budgetTemplate', function($query) use ($budgetTemplate) {
                                                                $query->where('type', $budgetTemplate->type);
                                                            })->where('departmentSystemID', $department->departmentSystemID)
                                                            ->where('isActive', 1)
                                                            ->first();

                $departmentBudgetTemplate = new DepartmentBudgetTemplate();
                $departmentBudgetTemplate->budgetTemplateID = $id;
                $departmentBudgetTemplate->departmentSystemID = $department->departmentSystemID;
                $departmentBudgetTemplate->isActive = $departmentBudgetTemplateCheck ? 0 : 1;
                $departmentBudgetTemplate->createdUserSystemID = auth()->id();
                $departmentBudgetTemplate->modifiedUserSystemID = auth()->id();
                $departmentBudgetTemplate->save();

                $departmentBudgetTemplateID = $departmentBudgetTemplate->departmentBudgetTemplateID;
                

                $items = \App\Models\ChartOfAccount::where('isActive', 1)->where('isApproved', 1)
                                                ->whereHas('chartofaccount_assigned', function($query) use ($department) {
                                                    $query->where('companySystemID', $department->companySystemID)
                                                        ->where('isAssigned', -1)
                                                        ->where('isActive', 1);
                                                })->when($budgetTemplate->type == 1, function ($query) {
                                                    $query->where('catogaryBLorPL', 'PL');
                                                })->when($budgetTemplate->type == 2, function ($query) {
                                                    $query->where('catogaryBLorPL', 'BS');
                                                })
                                                ->whereNotNull('reportTemplateCategory')
                                                ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'catogaryBLorPL', 'controlAccounts')
                                                ->get();

                foreach($items as $item) {
                    //assign the gl to DepBudgetTemplateGl
                    $depBudgetTemplateGl = new DepBudgetTemplateGl();
                    $depBudgetTemplateGl->departmentBudgetTemplateID = $departmentBudgetTemplateID;
                    $depBudgetTemplateGl->chartOfAccountSystemID = $item->chartOfAccountSystemID;
                    $depBudgetTemplateGl->createdUserSystemID = auth()->id();
                    $depBudgetTemplateGl->modifiedUserSystemID = auth()->id();
                    $depBudgetTemplateGl->save();
                }
            }
        }
    }

    /**
     * Remove the specified BudgetTemplate from storage.
     * DELETE /budget_templates/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        /** @var BudgetTemplate $budgetTemplate */
        $budgetTemplate = $this->budgetTemplateRepository->find($id);

        if (empty($budgetTemplate)) {
            return $this->sendError('Budget Template not found');
        }

        $previousValue = $budgetTemplate->toArray();

        //check if template is assigned to any department
        $departmentBudgetTemplate = DepartmentBudgetTemplate::where('budgetTemplateID', $id)->first();
        if($departmentBudgetTemplate) {
            return $this->sendError('The template is assigned to the department cannot be deleted');
        }

        //delete all columns assigned to the template
        \App\Models\BudgetTemplateColumn::where('budgetTemplateID', $id)->delete();

        $budgetTemplate->delete();

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "budget_templates", "Budget template ".$budgetTemplate->description." has been deleted", "D", [], $previousValue);

        return $this->sendResponse($id, 'Budget Template deleted successfully');
    }

    /**
     * Get all budget templates for DataTables
     *
     * @param Request $request
     * @return Response
     */
    public function getAllBudgetTemplates(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('isActive', 'type'));
        
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $budgetTemplates = $this->budgetTemplateRepository->budgetTemplateListQuery($request, $input, $search);

        return \DataTables::eloquent($budgetTemplates)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetTemplateID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Get form data for budget template
     *
     * @param Request $request
     * @return Response
     */
    public function getBudgetTemplateFormData(Request $request)
    {
        $data = [
            'typeOptions' => [
                ['value' => 1, 'label' => 'OPEX'],
                ['value' => 2, 'label' => 'CAPEX'],
                ['value' => 3, 'label' => 'Common']
            ]
        ];

        return $this->sendResponse($data, 'Budget Template form data retrieved successfully');
    }

    /**
     * Get budget templates by type (active only)
     *
     * @param string $type
     * @return Response
     */
    public function getBudgetTemplatesByType($type)
    {
        try {
            $templates = $this->budgetTemplateRepository->findWhere([
                'type' => $type,
                'isActive' => 1
            ]);

            return $this->sendResponse($templates, 'Budget templates retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error occurred while fetching budget templates', $e->getMessage());
        }
    }

    /**
     * Export budget templates
     *
     * @param Request $request
     * @return Response
     */
    public function exportBudgetTemplates(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');

        $budgetTemplates = $this->budgetTemplateRepository->budgetTemplateListQuery($request, $input, $search)->get();

        // Convert to export format
        $exportData = $budgetTemplates->map(function ($template) {
            $typeLabel = '';
            switch ($template->type) {
                case 1: $typeLabel = 'OPEX'; break;
                case 2: $typeLabel = 'CAPEX'; break;
                case 3: $typeLabel = 'Common'; break;
            }
            
            return [
                'Description' => $template->description,
                'Type' => $typeLabel,
                'Is Active' => $template->isActive ? 'Yes' : 'No',
                'Is Default' => $template->isDefault ? 'Yes' : 'No',
                'Created At' => $template->created_at ? $template->created_at->format('Y-m-d H:i:s') : '',
                'Updated At' => $template->updated_at ? $template->updated_at->format('Y-m-d H:i:s') : '',
            ];
        });

        return $this->sendResponse($exportData->toArray(), 'Budget Templates exported successfully');
    }
} 