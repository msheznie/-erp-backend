<?php

namespace App\Repositories;

use App\Models\ChequeUpdateReason;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChequeUpdateReasonRepository
 * @package App\Repositories
 * @version March 20, 2025, 10:40 am +04
 *
 * @method ChequeUpdateReason findWithoutFail($id, $columns = ['*'])
 * @method ChequeUpdateReason find($id, $columns = ['*'])
 * @method ChequeUpdateReason first($columns = ['*'])
*/
class ChequeUpdateReasonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cheque_register_detail_id',
        'is_switch',
        'update_switch_reason',
        'current_cheque_id',
        'previous_cheque_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChequeUpdateReason::class;
    }
}
