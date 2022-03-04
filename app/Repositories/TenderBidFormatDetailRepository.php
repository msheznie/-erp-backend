<?php

namespace App\Repositories;

use App\Models\TenderBidFormatDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBidFormatDetailRepository
 * @package App\Repositories
 * @version March 4, 2022, 10:33 am +04
 *
 * @method TenderBidFormatDetail findWithoutFail($id, $columns = ['*'])
 * @method TenderBidFormatDetail find($id, $columns = ['*'])
 * @method TenderBidFormatDetail first($columns = ['*'])
*/
class TenderBidFormatDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'label',
        'field_type',
        'is_disabled',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBidFormatDetail::class;
    }
}
