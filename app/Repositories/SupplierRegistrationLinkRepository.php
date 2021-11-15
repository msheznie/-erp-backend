<?php

namespace App\Repositories;

use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function save(Request $request, $token): bool
    {
        $supplierRegistrationLink = new SupplierRegistrationLink();
        $supplierRegistrationLink->name = $request->input('name');
        $supplierRegistrationLink->email = $request->input('email');
        $supplierRegistrationLink->registration_number = $request->input('registration_number');
        $supplierRegistrationLink->company_id = $request->input('company_id');
        $supplierRegistrationLink->token = $token;
        $supplierRegistrationLink->token_expiry_date_time = Carbon::now()->addHours(48);
        $supplierRegistrationLink->tenant_id = 1;
        $supplierRegistrationLink->api_key = "fow0lrRWCKxVIB4fW3lR";
        $supplierRegistrationLink->created_by = Auth::id();
        $supplierRegistrationLink->updated_by = '';

        return $supplierRegistrationLink->save();
    }
}
