<?php

namespace App\Repositories;

use App\Models\TenderBidClarifications;
use App\Repositories\BaseRepository;

/**
 * Class TenderBidClarificationsRepository
 * @package App\Repositories
 * @version April 12, 2022, 11:55 am +04
 *
 * @method TenderBidClarifications findWithoutFail($id, $columns = ['*'])
 * @method TenderBidClarifications find($id, $columns = ['*'])
 * @method TenderBidClarifications first($columns = ['*'])
*/
class TenderBidClarificationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'comment',
        'company_id',
        'created_by',
        'is_answered',
        'is_public',
        'parent_id',
        'post',
        'supplier_id',
        'tender_master_id',
        'updated_by',
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBidClarifications::class;
    }
}
