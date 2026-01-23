<?php

namespace App\Repositories;

use App\Models\CalendarDates;
use App\Repositories\BaseRepository;

/**
 * Class CalendarDatesRepository
 * @package App\Repositories
 * @version June 7, 2022, 1:43 pm +04
 *
 * @method CalendarDates findWithoutFail($id, $columns = ['*'])
 * @method CalendarDates find($id, $columns = ['*'])
 * @method CalendarDates first($columns = ['*'])
*/
class CalendarDatesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'calendar_date',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CalendarDates::class;
    }
}
