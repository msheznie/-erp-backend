<?php

namespace App\Repositories;

use App\Models\TenderSiteVisitDates;
use App\Repositories\BaseRepository;

/**
 * Class TenderSiteVisitDatesRepository
 * @package App\Repositories
 * @version March 16, 2022, 2:55 pm +04
 *
 * @method TenderSiteVisitDates findWithoutFail($id, $columns = ['*'])
 * @method TenderSiteVisitDates find($id, $columns = ['*'])
 * @method TenderSiteVisitDates first($columns = ['*'])
*/
class TenderSiteVisitDatesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'date',
        'company_id',
        'created_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSiteVisitDates::class;
    }
}
