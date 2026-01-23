<?php

namespace App\Repositories;

use App\Models\WeekDays;
use App\Repositories\BaseRepository;

/**
 * Class WeekDaysRepository
 * @package App\Repositories
 * @version November 10, 2021, 2:26 pm +04
 *
 * @method WeekDays findWithoutFail($id, $columns = ['*'])
 * @method WeekDays find($id, $columns = ['*'])
 * @method WeekDays first($columns = ['*'])
*/
class WeekDaysRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WeekDays::class;
    }
}
