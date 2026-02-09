<?php

namespace App\Repositories;

use App\Models\PoPaymentTermsRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class PoPaymentTermsRefferedbackRepository
 * @package App\Repositories
 * @version July 23, 2018, 12:24 pm UTC
 *
 * @method PoPaymentTermsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method PoPaymentTermsRefferedback find($id, $columns = ['*'])
 * @method PoPaymentTermsRefferedback first($columns = ['*'])
*/
class PoPaymentTermsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paymentTermID',
        'paymentTermsCategory',
        'poID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'isRequested',
        'timesReferred',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoPaymentTermsRefferedback::class;
    }
}
