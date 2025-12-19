<?php

namespace App\Repositories;

use App\Models\SoPaymentTerms;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SoPaymentTermsRepository
 * @package App\Repositories
 * @version January 13, 2021, 2:58 pm +04
 *
 * @method SoPaymentTerms findWithoutFail($id, $columns = ['*'])
 * @method SoPaymentTerms find($id, $columns = ['*'])
 * @method SoPaymentTerms first($columns = ['*'])
*/
class SoPaymentTermsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paymentTermsCategory',
        'soID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'isRequested',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SoPaymentTerms::class;
    }
}
