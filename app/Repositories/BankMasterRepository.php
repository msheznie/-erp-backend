<?php

namespace App\Repositories;

use App\Models\BankMaster;
use App\Repositories\BaseRepository;

/**
 * Class BankMasterRepository
 * @package App\Repositories
 * @version March 21, 2018, 5:24 am UTC
 *
 * @method BankMaster findWithoutFail($id, $columns = ['*'])
 * @method BankMaster find($id, $columns = ['*'])
 * @method BankMaster first($columns = ['*'])
*/
class BankMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankShortCode',
        'bankName',
        'createdDateTime',
        'createdByEmpID',
        'TimeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankMaster::class;
    }
}
