<?php

namespace App\Repositories;

use App\Models\CashFlowReportDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CashFlowReportDetailRepository
 * @package App\Repositories
 * @version June 29, 2022, 4:08 pm +04
 *
 * @method CashFlowReportDetail findWithoutFail($id, $columns = ['*'])
 * @method CashFlowReportDetail find($id, $columns = ['*'])
 * @method CashFlowReportDetail first($columns = ['*'])
*/
class CashFlowReportDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cashFlowTemplateDetailID',
        'amount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CashFlowReportDetail::class;
    }
}
