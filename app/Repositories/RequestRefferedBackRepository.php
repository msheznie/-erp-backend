<?php

namespace App\Repositories;

use App\Models\RequestRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RequestRefferedBackRepository
 * @package App\Repositories
 * @version December 6, 2018, 11:09 am UTC
 *
 * @method RequestRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method RequestRefferedBack find($id, $columns = ['*'])
 * @method RequestRefferedBack first($columns = ['*'])
*/
class RequestRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'RequestID',
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyJobID',
        'jobDescription',
        'serialNumber',
        'RequestCode',
        'comments',
        'location',
        'priority',
        'deliveryLocation',
        'RequestedDate',
        'ConfirmedYN',
        'ConfirmedBySystemID',
        'ConfirmedBy',
        'confirmedEmpName',
        'ConfirmedDate',
        'isActive',
        'quantityOnOrder',
        'quantityInHand',
        'selectedForIssue',
        'approved',
        'ClosedYN',
        'issueTrackID',
        'timeStamp',
        'RollLevForApp_curr',
        'approvedDate',
        'approvedByUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RequestRefferedBack::class;
    }
}
