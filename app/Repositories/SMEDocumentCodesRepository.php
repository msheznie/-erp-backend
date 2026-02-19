<?php

namespace App\Repositories;

use App\Models\SMEDocumentCodes;
use App\Repositories\BaseRepository;

/**
 * Class SMEDocumentCodesRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:46 am +04
 *
 * @method SMEDocumentCodes findWithoutFail($id, $columns = ['*'])
 * @method SMEDocumentCodes find($id, $columns = ['*'])
 * @method SMEDocumentCodes first($columns = ['*'])
*/
class SMEDocumentCodesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'document',
        'isApprovalDocument',
        'isFinance',
        'moduleID',
        'icon',
        'documentTable'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMEDocumentCodes::class;
    }
}
