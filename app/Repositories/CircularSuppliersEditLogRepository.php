<?php

namespace App\Repositories;

use App\Models\CircularSuppliersEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CircularSuppliersEditLogRepository
 * @package App\Repositories
 * @version June 17, 2025, 6:05 pm +04
 *
 * @method CircularSuppliersEditLog findWithoutFail($id, $columns = ['*'])
 * @method CircularSuppliersEditLog find($id, $columns = ['*'])
 * @method CircularSuppliersEditLog first($columns = ['*'])
*/
class CircularSuppliersEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'circular_id',
        'created_by',
        'id',
        'is_deleted',
        'level_no',
        'status',
        'supplier_id',
        'updated_by',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CircularSuppliersEditLog::class;
    }
}
