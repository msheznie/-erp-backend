<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Models\SupplierRegistrationLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Services\SupplierRegistrationService;

class SupplierRegistrationController extends Controller
{
    private $supplierRegistrationService;

    public function __construct(SupplierRegistrationService $supplierRegistrationService)
    {
        $this->supplierRegistrationService = $supplierRegistrationService;
    }

    /**
     * get KYC list
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request) {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = Helper::getEmployeeSystemID();

        $suppliersDetail = SupplierRegistrationLink::select('*');

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $suppliersDetail = $suppliersDetail->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($suppliersDetail)
            ->order(function ($query) use ($input) {
                $query->orderBy('created_at', 'DESC');
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * link KYC with supplier
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function linkKYCWithSupplier(Request $request){
        try {
            $selectedSupplier = $request->input('selectedSupplier');
            $selectedKYC = $request->input('selectedKYC');
            $supplierKyc = $request->input('supplierKyc');
            $companyID = $request->input('companyID');

            throw_unless($selectedSupplier, trans('srm_supplier_management.selected_supplier_must_be_passed'));
            throw_unless($supplierKyc, trans('srm_supplier_management.selected_kyc_details_must_be_passed'));

            $linkSuppliers = $this->supplierRegistrationService->linkKYCWithSupplier(
                $selectedSupplier, $supplierKyc, $selectedKYC, $companyID
            );
            if(!$linkSuppliers['success']){
                return $this->sendError($linkSuppliers['message']);
            }
            return $this->sendSuccessResponse(trans('srm_supplier_management.supplier_has_been_attached_to_the_kyc'));
        } catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }
    public function checkSupplierMatchWithKyc(Request $request){
        try{
            $input = $request->all();
            $matchingData = $this->supplierRegistrationService->compareSupplierData($input);
            if(!$matchingData['success']){
                return $this->sendError($matchingData['message']);
            }
            return $this->sendSuccessResponse($matchingData['message'], $matchingData['data']);
        } catch (\Exception $ex){
            return $this->sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }

    protected function sendError($message = ''){
        return [
            'success' => false,
            'message' => $message,
            'data' => []
        ];
    }
    protected function sendSuccessResponse($message='', $data=[]){
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }
}
