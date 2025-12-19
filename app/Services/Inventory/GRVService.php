<?php

namespace App\Services\Inventory;

use App\Models\ChartOfAccountsAssigned;
use App\Models\GRVMaster;
use App\Models\WarehouseMaster;
use App\Repositories\UserRepository;
use App\Services\Validation\CommonValidationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GRVService
{
    private $grv;
    private $userRepository;
    private $commonValidationService;

    public function __construct(UserRepository $userRepository,CommonValidationService $commonValidationService)
    {
        $this->grv = new GRVMaster();
        $this->userRepository = $userRepository;
        $this->commonValidationService = $commonValidationService;
    }

    public function validateGRV($input)
    {

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $this->commonValidationService->validateCompany($input);

        $this->commonValidationService->validateFinanicalYear($input);

        $input = $this->commonValidationService->validateFinancialPeriod(collect($input)->merge(['departmentSystemID'=>10])->toArray());

        if(!isset($input['grvDate']))
            throw new \Exception("GRV date not found");

        if(!isset($input['stampDate']))
            throw new \Exception("Stamp date not found");

        if(!isset($input['grvLocation']))
            throw new \Exception("Location not found");


        $warehouse = WarehouseMaster::where("wareHouseSystemCode", $input['grvLocation'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if(empty($warehouse))
            throw new \Exception("Location not found");


        if ($warehouse->manufacturingYN == 1) {
            if (is_null($warehouse->WIPGLCode)) {
                throw new \Exception('Please assigned WIP GLCode for this warehouse');
            } else {
                $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companySystemID']);
                if (empty($checkGLIsAssigned)) {
                    throw new \Exception('Assigned WIP GL Code is not assigned to this company!');
                }
            }
        }

        $input['grvDate'] = new Carbon($input['grvDate']);
        $input['stampDate'] = new Carbon(($input['stampDate']));

        if($input['grvDate']->greaterThan(Carbon::now()))
            throw new \Exception("GRV date can not be greater than current date");

        if($input['stampDate']->greaterThan(Carbon::now()))
            throw new \Exception("Stamp date can not be greater than current date");


    }
}
