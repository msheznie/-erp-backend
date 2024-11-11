<?php

namespace App\Repositories;

use App\Models\SRMPublicLink;
use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
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

    public function saveExternalLinkData($request)
    {

        $input = $request->all();
        DB::beginTransaction();
        try
        {
                $inputData = $input['extra'];
                $supplierLink = $this->model->newInstance();
                $supplierLink->name = $inputData['name'];
                $supplierLink->email = $inputData['email'];
                $supplierLink->registration_number =  $inputData['registration_number'];
                $supplierLink->company_id = $inputData['company_id'];
                $supplierLink->token = $inputData['token'];
                $supplierLink->created_by = -1;
                $supplierLink->updated_by = '';
                $supplierLink->STATUS = ($inputData['status']) ?? 0;
                $supplierLink->uuid = ($inputData['tenantUuid']) ?? null;
                $supplierLink->is_bid_tender =  $inputData['is_bid_tender'];
                $supplierLink->created_via =  3;
                $supplierLink->is_existing_erp_supplier = 0;
                $supplierLink->sub_domain = ' ';
                $supplierLink->save();
                DB::commit();
                return ['success' => true, 'message' => 'Successfully Saved', 'data' => true];

        }
        catch (\Exception $exception)
        {
            DB::rollback();
            return ['success' => false, 'message' => $exception->getMessage(), 'data' => false];
        }

    }
}
