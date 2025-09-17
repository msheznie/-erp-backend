<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeReportDetailValues;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeReportDetailValuesRepository
 * @package App\Repositories
 * @version August 24, 2025, 7:52 pm +04
 *
 * @method FinalReturnIncomeReportDetailValues findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeReportDetailValues find($id, $columns = ['*'])
 * @method FinalReturnIncomeReportDetailValues first($columns = ['*'])
*/
class FinalReturnIncomeReportDetailValuesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'report_detail_id',
        'column_id',
        'amount',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeReportDetailValues::class;
    }
}
