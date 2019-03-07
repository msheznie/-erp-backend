<?php

namespace App\Repositories;

use App\Models\ConsoleJVMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ConsoleJVMasterRepository
 * @package App\Repositories
 * @version March 6, 2019, 3:27 pm +04
 *
 * @method ConsoleJVMaster findWithoutFail($id, $columns = ['*'])
 * @method ConsoleJVMaster find($id, $columns = ['*'])
 * @method ConsoleJVMaster first($columns = ['*'])
*/
class ConsoleJVMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'serialNo',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'consoleJVcode',
        'consoleJVdate',
        'consoleJVNarration',
        'currencyID',
        'currencyER',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'localCurrencyID',
        'localCurrencyER',
        'rptCurrencyID',
        'rptCurrencyER',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ConsoleJVMaster::class;
    }
}
