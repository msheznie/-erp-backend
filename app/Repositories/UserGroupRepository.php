<?php

namespace App\Repositories;

use App\Models\UserGroup;
use App\Models\Company;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserGroupRepository
 * @package App\Repositories
 * @version March 16, 2018, 10:03 am UTC
 *
 * @method UserGroup findWithoutFail($id, $columns = ['*'])
 * @method UserGroup find($id, $columns = ['*'])
 * @method UserGroup first($columns = ['*'])
*/
class UserGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'description',
        'isActive',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserGroup::class;
    }

    public function getUserGroupByCompanyDatatable($input)
    {
        $userGroup = $this->model->with('company');
        if(array_key_exists ('selectedCompanyID' , $input)){
            if($input['selectedCompanyID'] > 0){
                $userGroup->where('srp_erp_usergroups.companyID',$input['selectedCompanyID'])->orderBy('userGroupID', 'desc');
            }
        }else{
            $companiesByGroup = Company::where("masterCompanySystemIDReorting", $input['globalCompanyId'])
                ->pluck("companySystemID");
            $userGroup->whereIn('srp_erp_usergroups.companyID',$companiesByGroup)->orderBy('userGroupID', 'desc');
        }

        return datatables($userGroup)->toJson();
        /*return \DataTables::eloquent($userGroup)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);*/
    }

    public function getUserGroup($input)
    {
        $userGroup = $this->model->where('companyID',$input["companyID"])->get();
        return $userGroup;
    }
}
