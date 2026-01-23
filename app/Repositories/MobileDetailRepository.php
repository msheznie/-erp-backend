<?php

namespace App\Repositories;

use App\Models\MobileDetail;
use App\Repositories\BaseRepository;

/**
 * Class MobileDetailRepository
 * @package App\Repositories
 * @version July 12, 2020, 12:40 pm +04
 *
 * @method MobileDetail findWithoutFail($id, $columns = ['*'])
 * @method MobileDetail find($id, $columns = ['*'])
 * @method MobileDetail first($columns = ['*'])
*/
class MobileDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mobilebillMasterID',
        'billPeriod',
        'startDate',
        'EndDate',
        'myNumber',
        'DestCountry',
        'DestNumber',
        'duration',
        'callDate',
        'cost',
        'currency',
        'Narration',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'isOfficial',
        'isIDD',
        'type',
        'userComments',
        'createDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MobileDetail::class;
    }
}
