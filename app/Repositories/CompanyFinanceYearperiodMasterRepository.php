<?php

namespace App\Repositories;

use App\Models\CompanyFinanceYearperiodMaster;
use App\Repositories\BaseRepository;

/**
 * Class CompanyFinanceYearperiodMasterRepository
 * @package App\Repositories
 * @version December 28, 2018, 8:47 am UTC
 *
 * @method CompanyFinanceYearperiodMaster findWithoutFail($id, $columns = ['*'])
 * @method CompanyFinanceYearperiodMaster find($id, $columns = ['*'])
 * @method CompanyFinanceYearperiodMaster first($columns = ['*'])
*/
class CompanyFinanceYearperiodMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'companyFinanceYearID',
        'dateFrom',
        'dateTo',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyFinanceYearperiodMaster::class;
    }
}
