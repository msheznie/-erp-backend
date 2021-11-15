<?php

namespace App\Repositories;

use App\Models\SlotDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SlotDetailsRepository
 * @package App\Repositories
 * @version November 11, 2021, 8:46 am +04
 *
 * @method SlotDetails findWithoutFail($id, $columns = ['*'])
 * @method SlotDetails find($id, $columns = ['*'])
 * @method SlotDetails first($columns = ['*'])
*/
class SlotDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'date',
        'slot_master_id',
        'status',
        'time_from',
        'time_to'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SlotDetails::class;
    }
}
