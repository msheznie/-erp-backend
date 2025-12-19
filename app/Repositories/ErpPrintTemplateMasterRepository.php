<?php

namespace App\Repositories;

use App\Models\ErpPrintTemplateMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpPrintTemplateMasterRepository
 * @package App\Repositories
 * @version January 30, 2020, 4:28 pm +04
 *
 * @method ErpPrintTemplateMaster findWithoutFail($id, $columns = ['*'])
 * @method ErpPrintTemplateMaster find($id, $columns = ['*'])
 * @method ErpPrintTemplateMaster first($columns = ['*'])
*/
class ErpPrintTemplateMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'printTemplateName',
        'printTemplateBlade'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpPrintTemplateMaster::class;
    }
}
