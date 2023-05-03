<?php

namespace App\Repositories;

use App\Models\TenderBoqItemsEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBoqItemsEditLogRepository
 * @package App\Repositories
 * @version April 7, 2023, 1:35 pm +04
 *
 * @method TenderBoqItemsEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderBoqItemsEditLog find($id, $columns = ['*'])
 * @method TenderBoqItemsEditLog first($columns = ['*'])
*/
class TenderBoqItemsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'description',
        'item_name',
        'main_work_id',
        'master_id',
        'modify_type',
        'qty',
        'tender_edit_version_id',
        'tender_id',
        'tender_ranking_line_item',
        'uom'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBoqItemsEditLog::class;
    }
}
