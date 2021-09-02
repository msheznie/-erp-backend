<?php

namespace App\Repositories;

use App\Models\PdcLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PdcLogRepository
 * @package App\Repositories
 * @version September 2, 2021, 2:44 pm +04
 *
 * @method PdcLog findWithoutFail($id, $columns = ['*'])
 * @method PdcLog find($id, $columns = ['*'])
 * @method PdcLog first($columns = ['*'])
*/
class PdcLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentmasterAutoID',
        'paymentBankID',
        'companySystemID',
        'currencyID',
        'chequeRegisterAutoID',
        'chequeNo',
        'chequeDate',
        'chequeStatus',
        'amount',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PdcLog::class;
    }
}
