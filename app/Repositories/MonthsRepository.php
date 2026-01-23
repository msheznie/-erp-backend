<?php

namespace App\Repositories;

use App\Models\Months;
use App\Repositories\BaseRepository;

/**
 * Class MonthsRepository
 * @package App\Repositories
 * @version March 27, 2018, 7:40 am UTC
 *
 * @method Months findWithoutFail($id, $columns = ['*'])
 * @method Months find($id, $columns = ['*'])
 * @method Months first($columns = ['*'])
*/
class MonthsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monthDes'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Months::class;
    }
}
