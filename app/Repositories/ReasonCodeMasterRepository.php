<?php

namespace App\Repositories;

use App\Models\ReasonCodeMaster;
use App\Repositories\BaseRepository;

/**
 * Class ReasonCodeMasterRepository
 * @package App\Repositories
 * @version June 24, 2022, 4:29 pm +04
 *
 * @method ReasonCodeMaster findWithoutFail($id, $columns = ['*'])
 * @method ReasonCodeMaster find($id, $columns = ['*'])
 * @method ReasonCodeMaster first($columns = ['*'])
*/
class ReasonCodeMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'isPost',
        'glCode'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReasonCodeMaster::class;
    }
}
