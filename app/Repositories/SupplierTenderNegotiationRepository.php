<?php

namespace App\Repositories;

use App\Models\SupplierTenderNegotiation;
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
        'suppliermaster_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierTenderNegotiation::class;
    }

    
    public function checkSupplierAlreadyInserted($data){
        $data = $this->model->where('tender_negotiation_id', $data['tender_negotiation_id'])->where('tender_negotiation_id', $data['suppliermaster_id']);
        return $data->get();
    }

    public function deleteSuppliersOfNegotiation($id) {
        $this->model->where('tender_negotiation_id',$id)->delete();
        return true;
    }
}
