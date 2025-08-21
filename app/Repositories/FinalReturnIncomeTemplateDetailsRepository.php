<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeTemplateDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeTemplateDetailsRepository
 * @package App\Repositories
 * @version August 17, 2025, 4:47 am +04
 *
 * @method FinalReturnIncomeTemplateDetails findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateDetails find($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateDetails first($columns = ['*'])
*/
class FinalReturnIncomeTemplateDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateMasterID',
        'description',
        'itemType',
        'sectionType',
        'sortOrder',
        'masterID',
        'isFinalLevel',
        'bgColor',
        'fontColor',
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
        return FinalReturnIncomeTemplateDetails::class;
    }
}
