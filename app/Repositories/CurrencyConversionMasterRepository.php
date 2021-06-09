<?php

namespace App\Repositories;

use App\Models\CurrencyConversionMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CurrencyConversionMasterRepository
 * @package App\Repositories
 * @version June 8, 2021, 2:40 pm +04
 *
 * @method CurrencyConversionMaster findWithoutFail($id, $columns = ['*'])
 * @method CurrencyConversionMaster find($id, $columns = ['*'])
 * @method CurrencyConversionMaster first($columns = ['*'])
*/
class CurrencyConversionMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'conversionCode',
        'conversionDate',
        'createdBy',
        'description',
        'confirmedYN',
        'confirmedEmpName',
        'ConfirmedBy',
        'ConfirmedBySystemID',
        'confirmedDate',
        'approvedYN',
        'approvedby',
        'approvedEmpSystemID',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CurrencyConversionMaster::class;
    }
}
