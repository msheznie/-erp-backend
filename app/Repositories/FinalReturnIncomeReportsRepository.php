<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeReports;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeReportsRepository
 * @package App\Repositories
 * @version August 24, 2025, 9:58 am +04
 *
 * @method FinalReturnIncomeReports findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeReports find($id, $columns = ['*'])
 * @method FinalReturnIncomeReports first($columns = ['*'])
*/
class FinalReturnIncomeReportsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'report_name',
        'template_id',
        'financialyear_id',
        'companySystemID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'submittedYN',
        'submittedByEmpSystemID',
        'submittedByEmpID',
        'submittedByName',
        'submittedDate',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeReports::class;
    }
}
