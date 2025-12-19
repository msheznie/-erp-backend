<?php

namespace App\Repositories;

use App\Models\BidSubmissionDetail;
use App\Models\BidSubmissionMaster;
use App\Models\DocumentAttachments;
use App\Models\TenderMaster;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/**
 * Class BidSubmissionMasterRepository
 * @package App\Repositories
 * @version June 15, 2022, 9:00 am +04
 *
 * @method BidSubmissionMaster findWithoutFail($id, $columns = ['*'])
 * @method BidSubmissionMaster find($id, $columns = ['*'])
 * @method BidSubmissionMaster first($columns = ['*'])
*/
class BidSubmissionMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'uuid',
        'tender_id',
        'supplier_registration_id',
        'bid_sequence',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidSubmissionMaster::class;
    }

    public function getIsExistCommonAttachment(Request $request) {
        $input = $request->all();
        $tenderMasterId = $input['tenderMasterId'];
        $companySystemID = $input['companySystemID'];

         $result = DocumentAttachments::select('attachmentID')
        ->where('companySystemID',$companySystemID)   
        ->where('documentSystemCode',$tenderMasterId)
        ->whereIn('documentSystemID',[108,113])
        ->where('attachmentType',2)
        ->where('envelopType',3)
        ->count();   

        $tenderMaster = TenderMaster::select('id')
        ->where('company_id',$companySystemID)
        ->where('id',$tenderMasterId)
        ->where('doc_verifiy_status',$tenderMasterId)
        ->count();

        if($result == 0 && $tenderMaster!= 1){ 
           $tenderUpdate = $this->updateDocVerifyTender($tenderMasterId,$companySystemID);
            if(!$tenderUpdate['status']){ 
                return $tenderUpdate;
            }

        }

        return  ['status' => true, 'message' => 'Attachment Details retrieved successfully.','data'=>$result]; 
    }

    public function updateDocVerifyTender($tenderMasterId,$companySystemID){ 
        DB::beginTransaction();
        try {
        
            $data = [
                'doc_verifiy_status' => 1,
                'doc_verifiy_comment' => '',
            ];
            TenderMaster::where('id', $tenderMasterId)->update($data);
            DB::commit(); 
            return ['status' => true, 'message' => 'Tender updated successfully.'];  
        }
        catch (\Exception $exception) {
            DB::rollBack(); 
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    public function identifyDuplicateBids($tenderId, $id) {
        try {
        $detailResult = BidSubmissionDetail::getBidSubmissionDetails($tenderId, $id);

        if($detailResult){
          return BidSubmissionDetail::hasExistingEvaluatedRecord($tenderId, $id, $detailResult->evaluation_detail_id);
        }
            return false;
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }
}
