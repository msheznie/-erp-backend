<?php

namespace App\Repositories;

use App\Models\Taxdetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxdetailRepository
 * @package App\Repositories
 * @version August 10, 2018, 10:21 am UTC
 *
 * @method Taxdetail findWithoutFail($id, $columns = ['*'])
 * @method Taxdetail find($id, $columns = ['*'])
 * @method Taxdetail first($columns = ['*'])
*/
class TaxdetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'taxMasterAutoID',
        'companyID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'taxShortCode',
        'taxDescription',
        'taxPercent',
        'payeeSystemCode',
        'payeeCode',
        'payeeName',
        'currency',
        'currencyER',
        'amount',
        'payeeDefaultCurrencyID',
        'payeeDefaultCurrencyER',
        'payeeDefaultAmount',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Taxdetail::class;
    }
}
