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
         $approvalLevel = $this->model
            ->with(['company' => function($query) {
                $query->select('CompanyName');
            },'department' => function($query) {
                $query->select('DepartmentDescription');
            },'document' => function($query) {
                $query->select('documentDescription');
            },'serviceline' => function($query) {
                $query->select('ServiceLineDes');
            }])->orderBy('approvalLevelID', 'desc');

        if(array_key_exists ('selectedCompanyID' , $input)){
            if($input['selectedCompanyID'] > 0){
                $approvalLevel->where('erp_approvallevel.companySystemID',$input['selectedCompanyID']);
            }
        }else{
            $companiesByGroup = Company::where("masterCompanySystemIDReorting", $input['globalCompanyId'])
                ->where("isGroup", 0)->pluck("companySystemID");
            $approvalLevel->whereIn('erp_approvallevel.companySystemID',$companiesByGroup);
        }

        if(array_key_exists ('documentSystemID' , $input)){
            if($input['documentSystemID'] > 0){
                $approvalLevel->where('erp_approvallevel.documentSystemID',$input['documentSystemID']);
            }
        }


        //return datatables($approvalLevel)->toJson();
        return \DataTables::eloquent($approvalLevel)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getGroupFilterData($input){

    }

}
