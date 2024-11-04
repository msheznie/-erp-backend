<?php

namespace App\Repositories;

use App\Models\ApprovalLevel;
use App\Models\Company;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ApprovalLevelRepository
 * @package App\Repositories
 * @version March 22, 2018, 1:35 pm UTC
 *
 * @method ApprovalLevel findWithoutFail($id, $columns = ['*'])
 * @method ApprovalLevel find($id, $columns = ['*'])
 * @method ApprovalLevel first($columns = ['*'])
 */
class ApprovalLevelRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineWise',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'levelDescription',
        'noOfLevels',
        'valueWise',
        'valueFrom',
        'valueTo',
        'isCategoryWiseApproval',
        'categoryID',
        'isActive',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ApprovalLevel::class;
    }

    public function getGroupApprovalLevelDatatable($input)
    {
        $search = $input['search']['value'];
        $sort = "";
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $approvalLevel = $this->model
            ->with(['company' => function ($query) use ($search) {
                $query->select('companySystemID', 'CompanyName', 'CompanyID');
            }, 'department' => function ($query) use ($search) {
                $query->select('departmentSystemID', 'DepartmentDescription');
            }, 'document' => function ($query) use ($search) {
                $query->select('documentSystemID', 'documentDescription');
            }, 'serviceline' => function ($query) use ($search) {
                $query->select('serviceLineSystemID', 'ServiceLineDes');
            }, 'category' => function ($query) use ($search) {
                $query->select('itemCategoryID', 'categoryDescription');
            }])->select('erp_approvallevel.*')->orderBy('approvalLevelID', 'desc');
        
        $approvalLevel->where('is_deleted',0);
        $approvalLevel->where('companySystemID',$input['globalCompanyId']);
        
        if (array_key_exists('selectedCompanyID', $input)) {
            if ($input['selectedCompanyID'] > 0) {
                $approvalLevel->where('companySystemID', $input['selectedCompanyID']);
            }
        } 

        if (array_key_exists('documentSystemID', $input)) {
            if ($input['documentSystemID'] > 0) {
                $approvalLevel->where('documentSystemID', $input['documentSystemID']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] > 0) {
                $approvalLevel->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('isActive', $input)) {

            $approvalLevel->where('isActive', $input['isActive']);

        }

        if ($search) {
            $approvalLevel = $approvalLevel->where('levelDescription', 'LIKE', "%{$search}%")
            ->orWhereHas('document', function($query) use ($search) {
                $query->where('documentDescription', 'LIKE', "%{$search}%");
            });
        }

        $approvalLevel->where('companySystemID',$input['globalCompanyId']);

        return \DataTables::eloquent($approvalLevel)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('approvalLevelID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getGroupFilterData($input)
    {

    }

}
