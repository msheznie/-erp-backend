<?php

namespace App\Repositories;

use App\Models\DocumentEmailNotificationDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentEmailNotificationDetailRepository
 * @package App\Repositories
 * @version January 10, 2019, 3:45 pm +04
 *
 * @method DocumentEmailNotificationDetail findWithoutFail($id, $columns = ['*'])
 * @method DocumentEmailNotificationDetail find($id, $columns = ['*'])
 * @method DocumentEmailNotificationDetail first($columns = ['*'])
*/
class DocumentEmailNotificationDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'employeeSystemID',
        'empID',
        'sendYN',
        'emailNotificationID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentEmailNotificationDetail::class;
    }
}
