<?php

namespace App\Repositories;

use App\Models\VatReturnFillingMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VatReturnFillingMasterRepository
 * @package App\Repositories
 * @version September 9, 2021, 1:08 pm +04
 *
 * @method VatReturnFillingMaster findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingMaster find($id, $columns = ['*'])
 * @method VatReturnFillingMaster first($columns = ['*'])
*/
class VatReturnFillingMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'RollLevForApp_curr'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingMaster::class;
    }

    public function generateFilling($date, $categoryID, )
    {
        switch ($categoryID) {
            case 2:
                
                break;
            case 3:
                
                break;
            
            default:
                # code...
                break;
        }
    }
}
