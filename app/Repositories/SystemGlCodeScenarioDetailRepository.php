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

    public function fetch_company_scenarios($company_list, $search){
        $data = $this->model;
        $data = $data->with('master');
        $data = $data->with('chart_of_account:chartOfAccountSystemID,AccountCode,AccountDescription');
        $data = $data->with('company:companySystemID,CompanyID,CompanyName');

        return $data;
    }
}
