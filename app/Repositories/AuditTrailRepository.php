<?php

namespace App\Repositories;

use App\Models\AuditTrail;
use App\Repositories\BaseRepository;

/**
 * Class AuditTrailRepository
 * @package App\Repositories
 * @version October 22, 2018, 8:31 am UTC
 *
 * @method AuditTrail findWithoutFail($id, $columns = ['*'])
 * @method AuditTrail find($id, $columns = ['*'])
 * @method AuditTrail first($columns = ['*'])
*/
class AuditTrailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'valueFrom',
        'valueTo',
        'valueFromSystemID',
        'valueFromText',
        'valueToSystemID',
        'valueToText',
        'description',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AuditTrail::class;
    }
}
