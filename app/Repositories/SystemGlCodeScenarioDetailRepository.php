<?php

namespace App\Repositories;

use App\Models\SystemGlCodeScenarioDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SystemGlCodeScenarioDetailRepository
 * @package App\Repositories
 * @version August 30, 2021, 1:49 pm +04
 *
 * @method SystemGlCodeScenarioDetail findWithoutFail($id, $columns = ['*'])
 * @method SystemGlCodeScenarioDetail find($id, $columns = ['*'])
 * @method SystemGlCodeScenarioDetail first($columns = ['*'])
*/
class SystemGlCodeScenarioDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'systemGlScenarioID',
        'companySystemID',
        'chartOfAccountSystemID',
        'serviceLineSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SystemGlCodeScenarioDetail::class;
    }

    public function fetch_company_scenarios($company_list, $search,$departmentID = null){
        $data = $this->model;
        $data = $data->with('master');
        $data = $data->with('chart_of_account:chartOfAccountSystemID,AccountCode,AccountDescription');
        $data = $data->with('company:companySystemID,CompanyID,CompanyName');
        $data = $data->whereIn('companySystemID', $company_list);
        $data = $data->WhereHas('master',function ($q) use($departmentID){
            $q->where('department_master_id',$departmentID);
        });

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $data = $data->where(function ($query) use ($search) {
                $query->whereHas('chart_of_account',function ($q) use($search){
                    $q->where('AccountCode', 'LIKE', "%{$search}%")
                        ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('master',function ($q) use($search){
                    $q->where('description', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('company',function ($q) use($search){
                    $q->where('CompanyName', 'LIKE', "%{$search}%")
                        ->orWhere('CompanyID', 'LIKE', "%{$search}%");
                });
            });
        }

        return $data;
    }
}
