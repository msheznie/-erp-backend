<?php

namespace App\Repositories;

use App\Models\ChequeRegister;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChequeRegisterRepository
 * @package App\Repositories
 * @version September 19, 2019, 3:10 pm +04
 *
 * @method ChequeRegister findWithoutFail($id, $columns = ['*'])
 * @method ChequeRegister find($id, $columns = ['*'])
 * @method ChequeRegister first($columns = ['*'])
*/
class ChequeRegisterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'master_description',
        'bank_id',
        'bank_account_id',
        'no_of_cheques',
        'started_cheque_no',
        'ended_cheque_no',
        'company_id',
        'document_id',
        'created_by',
        'created_pc',
        'updated_by',
        'updated_pc'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChequeRegister::class;
    }
}
