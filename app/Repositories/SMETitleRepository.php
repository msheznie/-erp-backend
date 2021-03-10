<?php

namespace App\Repositories;

use App\Models\SMETitle;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMETitleRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:38 am +04
 *
 * @method SMETitle findWithoutFail($id, $columns = ['*'])
 * @method SMETitle find($id, $columns = ['*'])
 * @method SMETitle first($columns = ['*'])
*/
class SMETitleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'TitleDescription',
        'SchMasterId',
        'BranchID',
        'Erp_companyID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMETitle::class;
    }
}
