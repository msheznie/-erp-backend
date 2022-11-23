<?php

namespace App\Repositories;

use App\Models\POSStagPaymentGlConfig;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSStagPaymentGlConfigRepository
 * @package App\Repositories
 * @version July 21, 2022, 12:27 pm +04
 *
 * @method POSStagPaymentGlConfig findWithoutFail($id, $columns = ['*'])
 * @method POSStagPaymentGlConfig find($id, $columns = ['*'])
 * @method POSStagPaymentGlConfig first($columns = ['*'])
*/
class POSStagPaymentGlConfigRepository extends BaseRepository
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
        return POSStagPaymentGlConfig::class;
    }
}
