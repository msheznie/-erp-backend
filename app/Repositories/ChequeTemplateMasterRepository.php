<?php

namespace App\Repositories;

use App\Models\ChequeTemplateMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChequeTemplateMasterRepository
 * @package App\Repositories
 * @version September 28, 2021, 6:16 pm +04
 *
 * @method ChequeTemplateMaster findWithoutFail($id, $columns = ['*'])
 * @method ChequeTemplateMaster find($id, $columns = ['*'])
 * @method ChequeTemplateMaster first($columns = ['*'])
*/
class ChequeTemplateMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'view_name',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChequeTemplateMaster::class;
    }
}
