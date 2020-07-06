<?php

namespace App\Repositories;

use App\Models\TaxMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxMasterRepository
 * @package App\Repositories
 * @version July 6, 2020, 10:11 am +04
 *
 * @method TaxMaster findWithoutFail($id, $columns = ['*'])
 * @method TaxMaster find($id, $columns = ['*'])
 * @method TaxMaster first($columns = ['*'])
*/
class TaxMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'taxShortCode',
        'taxDescription',
        'taxPercent',
        'payeeSystemCode',
        'taxType',
        'selectForPayment',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxMaster::class;
    }
}
