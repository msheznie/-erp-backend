<?php

namespace App\Repositories;

use App\Models\PaymentTermTemplateAssigned;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentTermTemplateAssignedRepository
 * @package App\Repositories
 * @version February 12, 2024, 8:43 am +04
 *
 * @method PaymentTermTemplateAssigned findWithoutFail($id, $columns = ['*'])
 * @method PaymentTermTemplateAssigned find($id, $columns = ['*'])
 * @method PaymentTermTemplateAssigned first($columns = ['*'])
*/
class PaymentTermTemplateAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateID',
        'companySystemID',
        'supplierCategoryID',
        'supplierID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentTermTemplateAssigned::class;
    }
}
