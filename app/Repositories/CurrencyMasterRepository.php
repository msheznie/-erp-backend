<?php

namespace App\Repositories;

use App\Models\CurrencyMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CurrencyMasterRepository
 * @package App\Repositories
 * @version March 2, 2018, 6:25 am UTC
 *
 * @method CurrencyMaster findWithoutFail($id, $columns = ['*'])
 * @method CurrencyMaster find($id, $columns = ['*'])
 * @method CurrencyMaster first($columns = ['*'])
*/
class CurrencyMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'CurrencyName',
        'CurrencyCode',
        'DecimalPlaces',
        'ExchangeRate',
        'isLocal',
        'DateModified',
        'ModifiedBy',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CurrencyMaster::class;
    }
}
