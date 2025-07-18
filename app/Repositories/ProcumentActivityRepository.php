<?php

namespace App\Repositories;

use App\Models\ProcumentActivity;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProcumentActivityRepository
 * @package App\Repositories
 * @version March 16, 2022, 1:06 pm +04
 *
 * @method ProcumentActivity findWithoutFail($id, $columns = ['*'])
 * @method ProcumentActivity find($id, $columns = ['*'])
 * @method ProcumentActivity first($columns = ['*'])
*/
class ProcumentActivityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'category_id',
        'company_id',
        'created_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProcumentActivity::class;
    }

    public function getProcumentActivityForAmd($tender_id){
        return $this->model->getProcumentActivityForAmd($tender_id);
    }
}
