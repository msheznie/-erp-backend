<?php

namespace App\Repositories;

use App\Models\TenderSupplierAssignee;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderSupplierAssigneeRepository
 * @package App\Repositories
 * @version June 2, 2022, 12:07 pm +04
 *
 * @method TenderSupplierAssignee findWithoutFail($id, $columns = ['*'])
 * @method TenderSupplierAssignee find($id, $columns = ['*'])
 * @method TenderSupplierAssignee first($columns = ['*'])
*/
class TenderSupplierAssigneeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'registration_link_id',
        'supplier_assigned_id',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'updated_by',
        'mail_sent'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSupplierAssignee::class;
    }

    public function deleteAllAssignedSuppliers($input) {

        $data = TenderSupplierAssignee::where('tender_master_id',$input['tenderId'])->where('company_id',$input['companySystemId'])->where('mail_sent',0)->whereNotIn('supplier_assigned_id', $input['removedSupplierAssignedIds'])->delete(); 

        if($data) 
            return true;

        return false;
    }

    
    public function deleteAllSelectedSuppliers($input) {

        $data = TenderSupplierAssignee::where('tender_master_id',$input['tenderId'])->where('company_id',$input['companySystemId'])->whereIn('id',$input['deleteList'])->delete();

        if($data) 
            return true;

        return false;
    }
}
