<?php

namespace App\Repositories;

use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierRegistrationLinkRepository
 * @package App\Repositories
 */
class SupplierRegistrationLinkRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'email',
        'registration_number',
        'token',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierRegistrationLink::class;
    }

    public function save(Request $request, $token)
    {
        $supplierCodeSystem = $request->input('supplierCodeSystem');
        $supplierRegistrationLink = new SupplierRegistrationLink();
        $supplierRegistrationLink->name = $request->input('name');
        $supplierRegistrationLink->email = $request->input('email');
        $supplierRegistrationLink->registration_number = $request->input('registration_number');
        $supplierRegistrationLink->company_id = $request->input('company_id');
        $supplierRegistrationLink->token = $token;
        $supplierRegistrationLink->token_expiry_date_time = Carbon::now()->addHours(96);
        $supplierRegistrationLink->created_by = \Helper::getEmployeeSystemID();
        $supplierRegistrationLink->updated_by = '';
        $supplierRegistrationLink->is_bid_tender =  ($request->input('is_bid_tender') == true ? 1:0);
        $supplierRegistrationLink->created_via =  1;
        $supplierRegistrationLink->is_existing_erp_supplier = 0;
        if (isset($supplierCodeSystem) && $supplierCodeSystem != null) {
            $supplierRegistrationLink->supplier_master_id = $request->input('supplierCodeSystem');
            $supplierRegistrationLink->is_existing_erp_supplier = 1;
        }
        $supplierRegistrationLink->sub_domain = $request->input('domain');
        $result = $supplierRegistrationLink->save();
        if($result){ 
            return ['status' => true,'id' =>$supplierRegistrationLink->id];
        }else { 
            return ['status' => false];
        }
    }
}
