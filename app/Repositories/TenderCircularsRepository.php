<?php

namespace App\Repositories;

use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\Models\CircularSuppliers;
use App\Models\CircularSuppliersEditLog;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentsEditLog;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use App\Services\SrmDocumentModifyService;
use Illuminate\Container\Container as Application;

/**
 * Class TenderCircularsRepository
 * @package App\Repositories
 * @version July 6, 2022, 12:39 pm +04
 *
 * @method TenderCirculars findWithoutFail($id, $columns = ['*'])
 * @method TenderCirculars find($id, $columns = ['*'])
 * @method TenderCirculars first($columns = ['*'])
 */
class TenderCircularsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'circular_name',
        'description',
        'attachment_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id'
    ];
    protected $srmDocumentModifyService;
    public function __construct(
        Application $app,
        SrmDocumentModifyService $documentModifyService
    ){
        parent::__construct($app);
        $this->srmDocumentModifyService = $documentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderCirculars::class;
    }
    public function getCircularList(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tender_id = $input['tender_id'];
        $versionID = $input['versionID'] ?? 0;

        $tenderMaster = $versionID > 0 ?
            TenderCircularsEditLog::getCircularList($tender_id, $companyId, $versionID) :
            TenderCirculars::getCircularList($tender_id, $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhere('circular_name', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', 'asc');
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
    public function getAttachmentDropCircular(Request $request){
        $input = $request->all();
        $documentSystemID = $input['documentSystemID'] ?? 0;
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;
        $tenderMasterId = $input['tenderMasterId'] ?? 0;
        $circularID = $input['circularId'] ?? 0;

        $attachment = $editOrAmend ?
            CircularAmendmentsEditLog::getCircularAmendment($tenderMasterId, $versionID) :
            CircularAmendments::getCircularAmendmentForAmd($tenderMasterId);

        $attachmentArray = [];
        if(count($attachment) > 0){
            $attachmentArray = $attachment->pluck('amendment_id');
            $attachmentArray = $attachmentArray->filter();
        }

        $attachmentDrop = $editOrAmend ?
            DocumentAttachmentsEditLog::getAttachmentForCirculars($attachmentArray,$documentSystemID, $tenderMasterId, $versionID) :
            DocumentAttachments::getAttachmentForCirculars($attachmentArray,$documentSystemID, $tenderMasterId);
        $i = 0;

        foreach  ($attachmentDrop as $row){
            $attachmentDrop[$i]['menu'] =   $row['attachmentDescription'] . '_' . $row['order_number'];
            $i++;
        }

        $data['attachmentDrop'] = $attachmentDrop;

        if(isset($input['circularId']) && $input['circularId'] > 0){
            $circular = $editOrAmend ? CircularAmendmentsEditLog::getCircularAmendmentByID($circularID, $versionID) :
                CircularAmendments::getCircularAmendmentByID($circularID);

            if(sizeof($circular) > 0){
                $attachmentAmended = $editOrAmend ?
                    DocumentAttachmentsEditLog::getNotUsedAttachmentForCirculars($circular, $versionID) :
                    DocumentAttachments::getNotUsedAttachmentForCirculars($circular);

                $i = 0;
                foreach  ($attachmentAmended as $r){
                    $attachmentAmended[$i]['menu'] =   $r['attachmentDescription'] . '_' . $r['order_number'];
                    $i++;
                }
                $data['amended'] = $attachmentAmended;
            }
        }

        return $data;
    }
    public function deleteTenderCircular($input){
        try {
            return DB::transaction(function () use ($input) {
                $id = (int) $input['id'] ?? 0;
                $amd_id = (int) $input['amd_id'] ?? 0;
                $versionID = $input['versionID'] ?? 0;
                $editOrAmend = $versionID > 0;

                $tenderCircular = $editOrAmend ?
                    TenderCircularsEditLog::find($amd_id) :
                    TenderCirculars::find($id);
                if($editOrAmend){
                    $tenderCircular->is_deleted = 1;
                    $tenderCircular->save();
                } else{
                    $tenderCircular->delete();
                }

                $amdCirculars = $this->deleteCircularAmendments($id, $amd_id, $editOrAmend, $versionID);

                if (!$amdCirculars['success']) {
                    return ['success' => false, 'message' => $amdCirculars['message']];
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];
            });
        } catch (\Exception $e) {

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    private function deleteCircularAmendments($id, $amd_id, $editOrAmend, $versionID){
        try {
            return DB::transaction(function () use ($id, $amd_id, $editOrAmend, $versionID) {
                $editOrAmend ?
                    CircularAmendmentsEditLog::where('circular_id', $amd_id)->where('vesion_id', $versionID)->where('is_deleted', 0)->update(['is_deleted' => 1]) :
                    CircularAmendments::where('circular_id', $amd_id)->delete();

                $editOrAmend ?
                    CircularSuppliersEditLog::where('circular_id', $amd_id)->where('version_id', $versionID)->where('is_deleted', 0)->update(['is_deleted' => 1]) :
                    CircularSuppliers::where('circular_id', $amd_id)->delete();

                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function checkAmendmentIsUsedInCircular($input){
        try{
            $versionID = $input['versionID'] ?? 0;
            $editOrAmend = $versionID > 0;

            $circularAmendments = $editOrAmend ?
                CircularAmendmentsEditLog::checkAmendmentIsUsedInCircular($input['amendmentId'], $input['tenderMasterId']) :
                CircularAmendments::checkAmendmentIsUsedInCircular($input['amendmentId'], $input['tenderMasterId']);

            if($circularAmendments != 0) {
                if($input['action'] == 'U'){
                    return ['success' => false, 'message' => trans('srm_tender_rfx.amendment_assigned_to_circular_cannot_update')];
                } elseif ($input['action'] == 'D'){
                    return ['success' => false, 'message' => trans('srm_tender_rfx.amendment_assigned_to_circular_cannot_delete')];
                }
            }
            return ['success' => true, 'message' => trans('srm_tender_rfx.amendment_not_assigned_to_any_circular_can_delete')];
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $ex->getMessage()])];
        }
    }
}
