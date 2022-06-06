<?php

namespace App\Repositories;

use App\Models\TenderSupplierAssignee;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderSupplierAssigneeRepository
 * @package App\Repositories
 * @version June 2, 2022, 12:07 pm +04
 *
 * @method TenderSupplierAssignee findWithoutFail($id, $columns = ['*'])
 * @method TenderSupplierAssignee find($id, $columns = ['*'])
 * @method TenderSupplierAssignee first($columns = ['*'])
*/
class TenderSupplierAssigneeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'registration_link_id',
        'supplier_assigned_id',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSupplierAssignee::class;
    }
}
