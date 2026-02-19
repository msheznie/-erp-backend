<?php

namespace App\Repositories;

use App\Models\TenderMasterSupplier;
use App\Repositories\BaseRepository;

/**
 * Class TenderMasterSupplierRepository
 * @package App\Repositories
 * @version March 31, 2022, 9:56 am +04
 *
 * @method TenderMasterSupplier findWithoutFail($id, $columns = ['*'])
 * @method TenderMasterSupplier find($id, $columns = ['*'])
 * @method TenderMasterSupplier first($columns = ['*'])
*/
class TenderMasterSupplierRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_by',
        'purchased_by',
        'purchased_date',
        'status',
        'tender_master_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderMasterSupplier::class;
    }
}
