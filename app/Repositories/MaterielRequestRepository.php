<?php

namespace App\Repositories;

use App\Models\MaterielRequest;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

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
        'timeStamp',
        'isFromPortal' => 'integer',
        'cancelledYN' => 'integer',
        'cancelledByEmpSystemID' => 'integer',
        'cancelledByEmpID' => 'string',
        'cancelledByEmpName' => 'string',
        'cancelledComments' => 'string',
        'cancelledDate' => 'string',
        'test'  => "string",
        'isSelectedToPR' => 'integer'
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

    public function materialrequestsListQuery($request, $input, $search = '', $serviceLineSystemID) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $materielRequests = MaterielRequest::whereIn('companySystemID', $subCompanies)
                                    ->with(['created_by', 'priority_by', 'warehouse_by','segment_by']);

        if (array_key_exists('ConfirmedYN', $input)) {

            if(($input['ConfirmedYN'] == 0 || $input['ConfirmedYN'] == 1)  && !is_null($input['ConfirmedYN'])) {
                $materielRequests->where('ConfirmedYN', $input['ConfirmedYN']);
            }
        }


        if (array_key_exists('approved', $input)) {
            if(($input['approved'] == 0 || $input['approved'] == -1 ) && !is_null($input['approved'])) {
                $materielRequests->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $materielRequests->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('cancelledYN', $input)) {
            if(($input['cancelledYN'] == 0 || $input['cancelledYN'] == -1) && !is_null($input['cancelledYN'])) {
                $materielRequests->where('cancelledYN', $input['cancelledYN']);
            }
        }

        if (array_key_exists('statusNot', $input)) {
           $details = $materielRequests->with('details');
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('RequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }
        
        return $materielRequests;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.request_code')] = $val->RequestCode;
                $data[$x][trans('custom.comments')] = $val->comments;
                $data[$x][trans('custom.segment')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.location')] = $val->warehouse_by? $val->warehouse_by->wareHouseDescription : '';
                $data[$x][trans('custom.requested_date')] = \Helper::dateFormat($val->RequestedDate);
                $data[$x][trans('custom.priority')] = $val->priority_by? $val->priority_by->priorityDescription : '';
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->cancelledYN, NULL, $val->ConfirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

}
