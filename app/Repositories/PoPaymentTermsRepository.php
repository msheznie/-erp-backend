<?php

namespace App\Repositories;

use App\Models\PoPaymentTerms;
use App\Repositories\BaseRepository;

/**
 * Class PoPaymentTermsRepository
 * @package App\Repositories
 * @version April 10, 2018, 11:05 am UTC
 *
 * @method PoPaymentTerms findWithoutFail($id, $columns = ['*'])
 * @method PoPaymentTerms find($id, $columns = ['*'])
 * @method PoPaymentTerms first($columns = ['*'])
*/
class PoPaymentTermsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paymentTermsCategory',
        'poID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoPaymentTerms::class;
    }
}
