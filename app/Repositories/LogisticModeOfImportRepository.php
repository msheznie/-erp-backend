<?php

namespace App\Repositories;

use App\Models\LogisticModeOfImport;
use App\Repositories\BaseRepository;

/**
 * Class LogisticModeOfImportRepository
 * @package App\Repositories
 * @version September 12, 2018, 5:07 am UTC
 *
 * @method LogisticModeOfImport findWithoutFail($id, $columns = ['*'])
 * @method LogisticModeOfImport find($id, $columns = ['*'])
 * @method LogisticModeOfImport first($columns = ['*'])
*/
class LogisticModeOfImportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'modeImportDescription',
        'createdUserID',
        'createdPCID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticModeOfImport::class;
    }
}
