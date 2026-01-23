<?php

namespace App\Repositories;

use App\Models\CalenderMaster;
use App\Repositories\BaseRepository;

/**
 * Class CalenderMasterRepository
 * @package App\Repositories
 * @version September 1, 2019, 10:43 am +04
 *
 * @method CalenderMaster findWithoutFail($id, $columns = ['*'])
 * @method CalenderMaster find($id, $columns = ['*'])
 * @method CalenderMaster first($columns = ['*'])
*/
class CalenderMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'calDate',
        'calMonth',
        'calYear',
        'isWorkingDay',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CalenderMaster::class;
    }
}
