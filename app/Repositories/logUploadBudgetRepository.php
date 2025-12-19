<?php

namespace App\Repositories;

use App\Models\logUploadBudget;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class logUploadBudgetRepository
 * @package App\Repositories
 * @version July 7, 2024, 7:57 pm +04
 *
 * @method logUploadBudget findWithoutFail($id, $columns = ['*'])
 * @method logUploadBudget find($id, $columns = ['*'])
 * @method logUploadBudget first($columns = ['*'])
*/
class logUploadBudgetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bugdet_upload_id',
        'companySystemID',
        'is_failed',
        'error_line',
        'log_message'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return logUploadBudget::class;
    }
}
