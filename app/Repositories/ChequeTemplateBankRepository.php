<?php

namespace App\Repositories;

use App\Models\ChequeTemplateBank;
use App\Repositories\BaseRepository;

/**
 * Class ChequeTemplateBankRepository
 * @package App\Repositories
 * @version September 29, 2021, 9:56 am +04
 *
 * @method ChequeTemplateBank findWithoutFail($id, $columns = ['*'])
 * @method ChequeTemplateBank find($id, $columns = ['*'])
 * @method ChequeTemplateBank first($columns = ['*'])
*/
class ChequeTemplateBankRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cheque_template_master_id',
        'bank_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChequeTemplateBank::class;
    }
}
