<?php

namespace App\Repositories;

use App\Models\DocumentEmailNotificationMaster;
use App\Repositories\BaseRepository;

/**
 * Class DocumentEmailNotificationMasterRepository
 * @package App\Repositories
 * @version January 10, 2019, 3:38 pm +04
 *
 * @method DocumentEmailNotificationMaster findWithoutFail($id, $columns = ['*'])
 * @method DocumentEmailNotificationMaster find($id, $columns = ['*'])
 * @method DocumentEmailNotificationMaster first($columns = ['*'])
*/
class DocumentEmailNotificationMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentEmailNotificationMaster::class;
    }
}
