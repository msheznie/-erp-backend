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

        $userGroup = $this->model->with('company')->where('delegation_id',0);
        if(array_key_exists ('selectedCompanyID' , $input)){
            if($input['selectedCompanyID'] > 0){
                $userGroup->where('srp_erp_usergroups.companyID',$input['selectedCompanyID']);
            }
        }else{
            $companiesByGroup = "";
            if(isset($input['globalCompanyId'])) {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $companiesByGroup = $input['globalCompanyId'];
                    $userGroup->where('srp_erp_usergroups.companyID', $companiesByGroup);
                }
            }

            $userGroup->orderBy('userGroupID', 'desc');
        }
        $userGroup = $userGroup->where('isDeleted', 0);
        $search = $input['search']['value'];
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $userGroup = $userGroup->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('company', function ($query) use ($search) {
                        $query->where('CompanyName', 'LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($userGroup)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('userGroupID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getUserGroup($input)
    {
        $userGroup = $this->model->where('companyID',$input["companyID"])->where('isDeleted', 0)->where('delegation_id',0)->get();
        return $userGroup;
    }
}
