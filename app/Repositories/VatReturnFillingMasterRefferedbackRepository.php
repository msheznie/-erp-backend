<?php

namespace App\Repositories;

use App\Models\VatReturnFillingMasterRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VatReturnFillingMasterRefferedbackRepository
 * @package App\Repositories
 * @version September 15, 2021, 12:57 pm +04
 *
 * @method VatReturnFillingMasterRefferedback findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingMasterRefferedback find($id, $columns = ['*'])
 * @method VatReturnFillingMasterRefferedback first($columns = ['*'])
*/
class VatReturnFillingMasterRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'returnFillingID',
        'returnFillingCode',
        'companySystemID',
        'documentSystemID',
        'date',
        'comment',
        'confirmedYN',
        'confirmedDate',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'approvedYN',
        'approvedDate',
        'approvedByUserSystemID',
        'approvedEmpID',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'serialNo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingMasterRefferedback::class;
    }
}
