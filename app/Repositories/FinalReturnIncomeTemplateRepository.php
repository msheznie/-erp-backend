<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeTemplateRepository
 * @package App\Repositories
 * @version August 15, 2025, 12:27 pm +04
 *
 * @method FinalReturnIncomeTemplate findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplate find($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplate first($columns = ['*'])
*/
class FinalReturnIncomeTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'isActive',
        'isDefault',
        'companySystemID',
        'createdPCID',
        'createdUserSystemID',
        'modifiedPCID',
        'modifiedUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeTemplate::class;
    }
}
