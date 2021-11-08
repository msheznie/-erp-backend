<?php

namespace App\Repositories;

use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Str;

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

    public function save(Request $request, $timeToken): bool
    {
        $supplierRegistrationLink = new SupplierRegistrationLink();
        $supplierRegistrationLink->name = $request->input('name');
        $supplierRegistrationLink->email = $request->input('email');
        $supplierRegistrationLink->registration_number = $request->input('registration_number');
        $supplierRegistrationLink->company_id = $request->input('company_id');
        $supplierRegistrationLink->token = $timeToken;
        return $supplierRegistrationLink->save();
    }
}
