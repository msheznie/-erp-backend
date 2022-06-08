<?php

namespace App\Repositories;

use App\Models\CalendarDatesDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CalendarDatesDetailRepository
 * @package App\Repositories
 * @version June 7, 2022, 1:44 pm +04
 *
 * @method CalendarDatesDetail findWithoutFail($id, $columns = ['*'])
 * @method CalendarDatesDetail find($id, $columns = ['*'])
 * @method CalendarDatesDetail first($columns = ['*'])
*/
class CalendarDatesDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'calendar_date_id',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CalendarDatesDetail::class;
    }
}
