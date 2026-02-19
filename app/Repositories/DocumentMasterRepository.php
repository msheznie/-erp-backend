<?php

namespace App\Repositories;

use App\Models\DocumentMaster;
use App\Repositories\BaseRepository;

/**
 * Class DocumentMasterRepository
 * @package App\Repositories
 * @version March 6, 2018, 5:34 am UTC
 *
 * @method DocumentMaster findWithoutFail($id, $columns = ['*'])
 * @method DocumentMaster find($id, $columns = ['*'])
 * @method DocumentMaster first($columns = ['*'])
*/
class DocumentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'documentDescription',
        'departmentID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentMaster::class;
    }
}
