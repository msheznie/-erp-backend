<?php

namespace App\Repositories;

use App\Models\SRMSupplierValues;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SRMSupplierValuesRepository
 * @package App\Repositories
 * @version January 3, 2024, 5:04 pm +04
 *
 * @method SRMSupplierValues findWithoutFail($id, $columns = ['*'])
 * @method SRMSupplierValues find($id, $columns = ['*'])
 * @method SRMSupplierValues first($columns = ['*'])
*/
class SRMSupplierValuesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_name',
        'name',
        'uuid',
        'company_id',
        'supplier_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMSupplierValues::class;
    }
}
