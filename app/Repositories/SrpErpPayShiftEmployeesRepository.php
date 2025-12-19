<?php

namespace App\Repositories;

use App\Models\SrpErpPayShiftEmployees;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrpErpPayShiftEmployeesRepository
 * @package App\Repositories
 * @version February 14, 2022, 9:13 am +04
 *
 * @method SrpErpPayShiftEmployees findWithoutFail($id, $columns = ['*'])
 * @method SrpErpPayShiftEmployees find($id, $columns = ['*'])
 * @method SrpErpPayShiftEmployees first($columns = ['*'])
*/
class SrpErpPayShiftEmployeesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'shiftID',
        'empID',
        'startDate',
        'endDate',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrpErpPayShiftEmployees::class;
    }
}
