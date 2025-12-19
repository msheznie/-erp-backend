<?php

namespace App\Repositories;

use App\Models\UserActivityLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserActivityLogRepository
 * @package App\Repositories
 * @version November 5, 2019, 7:35 am +04
 *
 * @method UserActivityLog findWithoutFail($id, $columns = ['*'])
 * @method UserActivityLog find($id, $columns = ['*'])
 * @method UserActivityLog first($columns = ['*'])
*/
class UserActivityLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'document_id',
        'description',
        'previous_value',
        'current_value',
        'activity_at',
        'user_pc'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserActivityLog::class;
    }

    public function showColumnByDocumentSystemID($documentSystemID){

        switch ($documentSystemID){
            case 22:
                $showColumn = ['departmentID','serviceLineCode','assetDescription','MANUFACTURE','COMMENTS','LOCATION','lastVerifiedDate','faCatID','faSubCatID','faSubCatID2','faSubCatID3','AUDITCATOGARY','COSTGLCODE','ACCDEPGLCODE','DEPGLCODE','DISPOGLCODE'];
                break;
            default:
                $showColumn = [];
        }

        return $showColumn;
    }
}
