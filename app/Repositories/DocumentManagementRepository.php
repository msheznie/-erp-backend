<?php

namespace App\Repositories;

use App\Models\DocumentManagement;
use App\Repositories\BaseRepository;

/**
 * Class DocumentManagementRepository
 * @package App\Repositories
 * @version September 11, 2019, 4:04 pm +04
 *
 * @method DocumentManagement findWithoutFail($id, $columns = ['*'])
 * @method DocumentManagement find($id, $columns = ['*'])
 * @method DocumentManagement first($columns = ['*'])
*/
class DocumentManagementRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'bigginingSerialNumber',
        'year',
        'companyFinanceYearID',
        'financeYearBigginingDate',
        'financeYearEndDate',
        'numberOfSerialNoDigits',
        'docRefNo',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentManagement::class;
    }
}
