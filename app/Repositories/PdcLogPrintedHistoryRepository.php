<?php

namespace App\Repositories;

use App\Models\PdcLogPrintedHistory;
use App\Repositories\BaseRepository;

/**
 * Class PdcLogPrintedHistoryRepository
 * @package App\Repositories
 * @version January 9, 2023, 4:06 pm +04
 *
 * @method PdcLogPrintedHistory findWithoutFail($id, $columns = ['*'])
 * @method PdcLogPrintedHistory find($id, $columns = ['*'])
 * @method PdcLogPrintedHistory first($columns = ['*'])
*/
class PdcLogPrintedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'pdcLogID',
        'chequePrintedBy',
        'chequePrintedDate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PdcLogPrintedHistory::class;
    }
}
