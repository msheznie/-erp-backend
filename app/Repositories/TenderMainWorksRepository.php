<?php

namespace App\Repositories;

use App\Models\TenderMainWorks;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderMainWorksRepository
 * @package App\Repositories
 * @version April 6, 2022, 1:35 pm +04
 *
 * @method TenderMainWorks findWithoutFail($id, $columns = ['*'])
 * @method TenderMainWorks find($id, $columns = ['*'])
 * @method TenderMainWorks first($columns = ['*'])
*/
class TenderMainWorksRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'schedule_id',
        'item',
        'description',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderMainWorks::class;
    }
}
