<?php

namespace App\Repositories;

use App\Models\BudgetDetailComment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetDetailCommentRepository
 * @package App\Repositories
 * @version August 20, 2021, 11:13 am +04
 *
 * @method BudgetDetailComment findWithoutFail($id, $columns = ['*'])
 * @method BudgetDetailComment find($id, $columns = ['*'])
 * @method BudgetDetailComment first($columns = ['*'])
*/
class BudgetDetailCommentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budgetDetailID',
        'comment',
        'created_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetDetailComment::class;
    }
}
