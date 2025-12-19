<?php

namespace App\Repositories;

use App\Models\MonthlyDeclarationsTypes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MonthlyDeclarationsTypesRepository
 * @package App\Repositories
 * @version July 29, 2021, 1:14 pm +04
 *
 * @method MonthlyDeclarationsTypes findWithoutFail($id, $columns = ['*'])
 * @method MonthlyDeclarationsTypes find($id, $columns = ['*'])
 * @method MonthlyDeclarationsTypes first($columns = ['*'])
*/
class MonthlyDeclarationsTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monthlyDeclaration',
        'monthlyDeclarationType',
        'salaryCategoryID',
        'expenseGLCode',
        'isPayrollCategory',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MonthlyDeclarationsTypes::class;
    }
}
