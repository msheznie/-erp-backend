<?php

namespace App\Repositories;

use App\Models\SMEOertimeGroupMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMEOertimeGroupMasterRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:48 am +04
 *
 * @method SMEOertimeGroupMaster findWithoutFail($id, $columns = ['*'])
 * @method SMEOertimeGroupMaster find($id, $columns = ['*'])
 * @method SMEOertimeGroupMaster first($columns = ['*'])
*/
class SMEOertimeGroupMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMEOertimeGroupMaster::class;
    }
}
