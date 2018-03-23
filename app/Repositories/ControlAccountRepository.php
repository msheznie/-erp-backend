<?php

namespace App\Repositories;

use App\Models\ControlAccount;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ControlAccountRepository
 * @package App\Repositories
 * @version March 16, 2018, 4:52 am UTC
 *
 * @method ControlAccount findWithoutFail($id, $columns = ['*'])
 * @method ControlAccount find($id, $columns = ['*'])
 * @method ControlAccount first($columns = ['*'])
*/
class ControlAccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'controlAccountCode',
        'description',
        'itemLedgerShymbol',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ControlAccount::class;
    }
}
