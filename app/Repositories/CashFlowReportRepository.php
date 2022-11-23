<?php

namespace App\Repositories;

use App\Models\CashFlowReport;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CashFlowReportRepository
 * @package App\Repositories
 * @version June 29, 2022, 3:38 pm +04
 *
 * @method CashFlowReport findWithoutFail($id, $columns = ['*'])
 * @method CashFlowReport find($id, $columns = ['*'])
 * @method CashFlowReport first($columns = ['*'])
*/
class CashFlowReportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'cashFlowTemplateID',
        'companyFinanceYearID',
        'date',
        'createdPCID',
        'createdUserSystemID',
        'modifiedPCID',
        'modifiedUserSystemID',
        'confirmed_by',
        'confirmed_date',
        'confirmedYN'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CashFlowReport::class;
    }
}
