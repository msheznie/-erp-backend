<?php

namespace App\Repositories;

use App\Models\PoCutoffJobData;
use App\Repositories\BaseRepository;

/**
 * Class PoCutoffJobDataRepository
 * @package App\Repositories
 * @version August 17, 2022, 9:09 am +04
 *
 * @method PoCutoffJobData findWithoutFail($id, $columns = ['*'])
 * @method PoCutoffJobData find($id, $columns = ['*'])
 * @method PoCutoffJobData first($columns = ['*'])
*/
class PoCutoffJobDataRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentCode',
        'segment',
        'currency',
        'documentValue',
        'remainingValue',
        'cutOffDate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoCutoffJobData::class;
    }
}
