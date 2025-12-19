<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeReportDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeReportDetailsRepository
 * @package App\Repositories
 * @version August 24, 2025, 7:52 pm +04
 *
 * @method FinalReturnIncomeReportDetails findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeReportDetails find($id, $columns = ['*'])
 * @method FinalReturnIncomeReportDetails first($columns = ['*'])
*/
class FinalReturnIncomeReportDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'report_id',
        'template_detail_id',
        'amount',
        'is_manual',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeReportDetails::class;
    }
}
