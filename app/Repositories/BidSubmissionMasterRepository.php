<?php

namespace App\Repositories;

use App\Models\BidSubmissionMaster;
use App\Models\DocumentAttachments;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
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

         return DocumentAttachments::select('attachmentID')
        ->where('companySystemID',$companySystemID)   
        ->where('documentSystemCode',$tenderMasterId)
        ->whereIn('documentSystemID',[108,113])
        ->where('attachmentType',2)
        ->where('envelopType',3)
        ->count();   
    }
}
