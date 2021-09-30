<?php

namespace App\Repositories;

use App\Models\SystemGlCodeScenario;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SystemGlCodeScenarioRepository
 * @package App\Repositories
 * @version August 30, 2021, 1:47 pm +04
 *
 * @method SystemGlCodeScenario findWithoutFail($id, $columns = ['*'])
 * @method SystemGlCodeScenario find($id, $columns = ['*'])
 * @method SystemGlCodeScenario first($columns = ['*'])
*/
class SystemGlCodeScenarioRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SystemGlCodeScenario::class;
    }

    public function un_assign_scenario($company_id){
        $data = $this->model->where('isActive', 1);
        $data = $data->whereDoesntHave('company_scenario', function ($q) use ($company_id){
            $q->where('companySystemID', $company_id);
        });

        return $data->get();
    }

}
