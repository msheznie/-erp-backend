<?php

namespace App\Repositories;

use App\Models\ChequeRegisterDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChequeRegisterDetailRepository
 * @package App\Repositories
 * @version September 19, 2019, 3:12 pm +04
 *
 * @method ChequeRegisterDetail findWithoutFail($id, $columns = ['*'])
 * @method ChequeRegisterDetail find($id, $columns = ['*'])
 * @method ChequeRegisterDetail first($columns = ['*'])
*/
class ChequeRegisterDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cheque_register_master_id',
        'cheque_no',
        'description',
        'created_by',
        'created_pc',
        'updated_by',
        'updated_pc',
        'company_id',
        'document_id',
        'document_master_id',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChequeRegisterDetail::class;
    }
}
