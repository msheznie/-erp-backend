<?php

namespace App\Repositories;

use App\Models\POSSourcePaymentGlConfig;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourcePaymentGlConfigRepository
 * @package App\Repositories
 * @version July 21, 2022, 12:28 pm +04
 *
 * @method POSSourcePaymentGlConfig findWithoutFail($id, $columns = ['*'])
 * @method POSSourcePaymentGlConfig find($id, $columns = ['*'])
 * @method POSSourcePaymentGlConfig first($columns = ['*'])
*/
class POSSourcePaymentGlConfigRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'glAccountType',
        'queryString',
        'image',
        'isActive',
        'sortOrder',
        'selectBoxName',
        'timesstamp',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSourcePaymentGlConfig::class;
    }
}
