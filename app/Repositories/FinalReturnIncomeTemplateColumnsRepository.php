<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeTemplateColumns;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeTemplateColumnsRepository
 * @package App\Repositories
 * @version August 20, 2025, 8:53 pm +04
 *
 * @method FinalReturnIncomeTemplateColumns findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateColumns find($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateColumns first($columns = ['*'])
*/
class FinalReturnIncomeTemplateColumnsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateMasterID',
        'description',
        'sortOrder',
        'isHide',
        'width',
        'bgColor',
        'companySystemID',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeTemplateColumns::class;
    }
}
