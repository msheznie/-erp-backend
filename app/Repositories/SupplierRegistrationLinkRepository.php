<?php

namespace App\Repositories;

use App\Models\SupplierRegistrationLink;
use Illuminate\Http\Request;
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

    public function save(Request $request)
    {
        $input = $request->all();
        $supplierRegistrationLink = new SupplierRegistrationLink();
        $supplierRegistrationLink->name = $input['name'];
        $supplierRegistrationLink->email = $input['email'];
        $supplierRegistrationLink->registration_number = $input['registration_number'];
        $supplierRegistrationLink->token = "2424";
        $status = $supplierRegistrationLink->save();
    }
}
