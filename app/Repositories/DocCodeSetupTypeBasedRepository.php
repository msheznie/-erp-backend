<?php

namespace App\Repositories;

use App\Models\DocCodeSetupTypeBased;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocCodeSetupTypeBasedRepository
 * @package App\Repositories
 * @version January 30, 2025, 10:28 am +04
 *
 * @method DocCodeSetupTypeBased findWithoutFail($id, $columns = ['*'])
 * @method DocCodeSetupTypeBased find($id, $columns = ['*'])
 * @method DocCodeSetupTypeBased first($columns = ['*'])
*/
class DocCodeSetupTypeBasedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'master_id',
        'type_id',
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
        return DocCodeSetupTypeBased::class;
    }
}
