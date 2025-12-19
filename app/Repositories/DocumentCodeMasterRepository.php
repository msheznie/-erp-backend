<?php

namespace App\Repositories;

use App\Models\DocumentCodeMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodeMasterRepository
 * @package App\Repositories
 * @version January 30, 2025, 9:48 am +04
 *
 * @method DocumentCodeMaster findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodeMaster find($id, $columns = ['*'])
 * @method DocumentCodeMaster first($columns = ['*'])
*/
class DocumentCodeMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'module_id',
        'document_transaction_id',
        'numbering_sequence_id',
        'last_serial',
        'serialization',
        'formatCount',
        'serial_length'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodeMaster::class;
    }
}
