<?php

namespace App\Repositories;

use App\Models\SupplierTenderNegotiation;
use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierTenderNegotiationRepository
 * @package App\Repositories
 * @version April 24, 2023, 11:03 am +04
 *
 * @method SupplierTenderNegotiation findWithoutFail($id, $columns = ['*'])
 * @method SupplierTenderNegotiation find($id, $columns = ['*'])
 * @method SupplierTenderNegotiation first($columns = ['*'])
*/
class SupplierTenderNegotiationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_negotiation_id',
        'suppliermaster_id',
        'srm_bid_submission_master_id',
        'bidSubmissionCode'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierTenderNegotiation::class;
    }

    
    public function checkSupplierAlreadyInserted($data){
        $data = $this->model->select('id')->where('tender_negotiation_id', $data['tender_negotiation_id'])->where('suppliermaster_id', $data['suppliermaster_id'])->where('srm_bid_submission_master_id',$data['srm_bid_submission_master_id']);
        return $data->get();
    }

    public function deleteSuppliersOfNegotiation($input) {
        $deleteRecord = $this->model->where('tender_negotiation_id',$input['tenderNegotiationID'])->delete();
        return ($deleteRecord) ? true : false;
    }

    public function getSupplierList($negotiationId, $tenderId)
    {
        $tenderData = TenderMaster::getTenderByUuid($tenderId);

        if(!$tenderData){
            return [
                "success" => false,
                "data" => 'Not a Valid Tender UUID'
            ];
        }

        return SupplierTenderNegotiation::getSupplierList($negotiationId, $tenderData['id']);
    }
}
