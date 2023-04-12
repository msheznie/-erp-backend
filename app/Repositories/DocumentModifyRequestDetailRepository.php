<?php

namespace App\Repositories;

use App\Models\DocumentModifyRequestDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentModifyRequestDetailRepository
 * @package App\Repositories
 * @version April 4, 2023, 11:15 am +04
 *
 * @method DocumentModifyRequestDetail findWithoutFail($id, $columns = ['*'])
 * @method DocumentModifyRequestDetail find($id, $columns = ['*'])
 * @method DocumentModifyRequestDetail first($columns = ['*'])
*/
class DocumentModifyRequestDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'attribute',
        'new_value',
        'old_value',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentModifyRequestDetail::class;
    }
}
