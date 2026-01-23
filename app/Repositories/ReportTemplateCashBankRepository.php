<?php

namespace App\Repositories;

use App\Models\ReportTemplateCashBank;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateCashBankRepository
 * @package App\Repositories
 * @version January 18, 2019, 1:58 pm +04
 *
 * @method ReportTemplateCashBank findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateCashBank find($id, $columns = ['*'])
 * @method ReportTemplateCashBank first($columns = ['*'])
*/
class ReportTemplateCashBankRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDescription',
        'isActive',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateCashBank::class;
    }
}
