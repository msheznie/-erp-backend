<?php

namespace App\Repositories;

use App\Models\DocCodeSetupCommon;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocCodeSetupCommonRepository
 * @package App\Repositories
 * @version January 30, 2025, 10:31 am +04
 *
 * @method DocCodeSetupCommon findWithoutFail($id, $columns = ['*'])
 * @method DocCodeSetupCommon find($id, $columns = ['*'])
 * @method DocCodeSetupCommon first($columns = ['*'])
*/
class DocCodeSetupCommonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'master_id',
        'document_transaction_id',
        'format1',
        'format2',
        'format3',
        'format4',
        'format5',
        'format6',
        'format7',
        'format8',
        'format9',
        'format10',
        'format11',
        'format12',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocCodeSetupCommon::class;
    }
}
