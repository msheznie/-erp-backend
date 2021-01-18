<?php

namespace App\Repositories;

use App\Models\MaterielRequest;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MaterielRequestRepository
 * @package App\Repositories
 * @version June 12, 2018, 9:35 am UTC
 *
 * @method MaterielRequest findWithoutFail($id, $columns = ['*'])
 * @method MaterielRequest find($id, $columns = ['*'])
 * @method MaterielRequest first($columns = ['*'])
*/
class MaterielRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'ConfirmedDate',
        'isActive',
        'quantityOnOrder',
        'quantityInHand',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'selectedForIssue',
        'approved',
        'ClosedYN',
        'issueTrackID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MaterielRequest::class;
    }

    public function getAudit($id)
    {
        return $this ->with(['created_by','confirmed_by','warehouse_by','modified_by','company', 'details' => function ($q) {
            $q->with('uom_issuing', 'item_by');
        },'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])->where('documentSystemID',9);
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }
}
