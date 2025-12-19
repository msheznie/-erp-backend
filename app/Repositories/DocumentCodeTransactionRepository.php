<?php

namespace App\Repositories;

use App\Models\DocumentCodeTransaction;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodeTransactionRepository
 * @package App\Repositories
 * @version January 30, 2025, 9:10 am +04
 *
 * @method DocumentCodeTransaction findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodeTransaction find($id, $columns = ['*'])
 * @method DocumentCodeTransaction first($columns = ['*'])
*/
class DocumentCodeTransactionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'module_id',
        'transaction_name',
        'master_prefix',
        'is_active',
        'isGettingEdited',
        'isTypeEnable'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodeTransaction::class;
    }
}
