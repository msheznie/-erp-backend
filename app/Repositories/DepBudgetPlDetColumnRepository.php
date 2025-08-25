<?php

namespace App\Repositories;

use App\Models\DepBudgetPlDetColumn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepBudgetPlDetColumnRepository
 * @package App\Repositories
 * @version August 22, 2025, 4:32 am +04
 *
 * @method DepBudgetPlDetColumn findWithoutFail($id, $columns = ['*'])
 * @method DepBudgetPlDetColumn find($id, $columns = ['*'])
 * @method DepBudgetPlDetColumn first($columns = ['*'])
*/
class DepBudgetPlDetColumnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'columnName',
        'slug',
        'isDefault'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepBudgetPlDetColumn::class;
    }
}
