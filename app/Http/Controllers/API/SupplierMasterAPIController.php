<?php
/**
 * =============================================
 * -- File Name : SupplierMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Master
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Supplier Master
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getSupplierMasterByCompany(),getAssignedCompaniesBySupplier(),
 * -- Date: 06-June 2018 By: Mubashir Description: Modified getSupplierMasterByCompany() to handle filters from local storage
 * -- Date: 25-June 2018 By: Mubashir Description: Added new functions named as getSearchSupplierByCompany()
 * -- Date: 17-July 2018 By: Fayas Description: Added new functions named as getSupplierMasterAudit()
 * -- Date: 18-July 2018 By: Fayas Description: Added new functions named as exportSupplierMaster()
 * -- Date: 04-November 2018 By: Fayas Description: Added new functions named as printSuppliers()
 * -- Date: 17-December 2018 By: Fayas Description: Added new functions named as supplierReferBack()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\NotificationService;
use App\helper\ReopenDocument;
use App\Http\Requests\API\CreateSupplierMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierMasterAPIRequest;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\CountryMaster;
use App\Models\DocumentReferedHistory;
use App\Models\BookInvSuppMaster;
use App\Models\GRVMaster;
use App\Models\ProcumentOrder;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierBusinessCategoryAssign;
use App\Models\SupplierCategoryMaster;
use App\Models\SupplierCategorySub;
use App\Models\SupplierCurrency;
use App\Models\RegisteredSupplierCurrency;
use App\Models\RegisteredBankMemoSupplier;
use App\Models\BankMemoSupplier;
use App\Models\RegisteredSupplierContactDetail;
use App\Models\RegisteredSupplierAttachment;
use App\Models\DocumentApproved;
use App\Models\SupplierMaster;
use App\Models\DocumentMaster;
use App\Models\ChartOfAccount;
use App\Models\ExternalLinkHash;
use App\Models\RegisteredSupplier;
use App\Models\SupplierRegistrationLink;
use App\Models\SupplierSubCategoryAssign;
use App\Models\SystemConfigurationAttributes;
use App\Models\YesNoSelection;
use App\Models\SupplierContactType;
use App\Models\BankMemoTypes;
use App\Models\SupplierMasterRefferedBack;
use App\Repositories\SupplierMasterRepository;
use App\Traits\UserActivityLogger;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Criteria\FilterSupplierMasterByCompanyCriteria;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SupplierRegistrationLinkRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailForQueuing;
use Illuminate\Support\Facades\Hash;
use App\helper\CreateExcel;
use App\helper\email;
use App\Models\DebitNote;
use Illuminate\Http\Request as LaravelRequest;
use App\Models\RegisterSupplierBusinessCategoryAssign;
use App\Models\RegisterSupplierSubcategoryAssign;
use App\Repositories\SupplierBlockRepository;
use App\Models\SupplierBlock;
use App\Traits\AuditLogsTrait;


/**
 * Class SupplierMasterController
 * @package App\Http\Controllers\API
 */
class SupplierMasterAPIController extends AppBaseController
{
    /** @var  SupplierMasterRepository */
    private $supplierMasterRepository;
    private $userRepository;
    private $registrationLinkRepository;
    private $supplierBlockRepository;
    use AuditLogsTrait;

    public function __construct(SupplierBlockRepository $supplierBlockRepo,SupplierMasterRepository $supplierMasterRepo, UserRepository $userRepo, SupplierRegistrationLinkRepository $registrationLinkRepository)
    {
        $this->supplierMasterRepository = $supplierMasterRepo;
        $this->userRepository = $userRepo;
        $this->registrationLinkRepository = $registrationLinkRepository;
        $this->supplierBlockRepository = $supplierBlockRepo;
    }

    /**
     * Display a listing of the SupplierMaster.
     * GET|HEAD /supplierMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //$this->supplierMasterRepository->pushCriteria(new FilterSupplierMasterByCompanyCriteria($request));

        //return $this->supplierMasterRepository->getFieldsSearchable();

        $this->supplierMasterRepository->pushCriteria(new RequestCriteria($request));

        $this->supplierMasterRepository->pushCriteria(new LimitOffsetCriteria($request));

        $supplierMasters = $this->supplierMasterRepository
            //->with(['categoryMaster','employee'])
            ->paginate($request->get('limit'));
        //->all();


        return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');
    }

    /**
     * get supplier master by company.
     * POST /getSupplierMasterByCompany
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getSupplierMasterByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'isCriticalYN', 'isActive', 'supplierConfirmedYN', 'approvedYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $supplierId = 'supplierCodeSytem';
        if ($request['type'] == 'all') {
            $supplierId = 'supplierCodeSystem';
        }

        $search = $request->input('search.value');
        $supplierCountryID = $request['supplierCountryID'];
        $supplierCountryID = (array)$supplierCountryID;
        $supplierCountryID = collect($supplierCountryID)->pluck('id');

        $liabilityAccountSysemID = $request['liabilityAccountSysemID'];
        $liabilityAccountSysemID = (array)$liabilityAccountSysemID;
        $liabilityAccountSysemID = collect($liabilityAccountSysemID)->pluck('id');

        $supplierMasters = $this->getSuppliersByFilterQry($input, $search, $supplierCountryID, $liabilityAccountSysemID);

        return \DataTables::eloquent($supplierMasters)
            ->order(function ($query) use ($input, $supplierId) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy($supplierId, $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    /**
     * export to csv Supplier Master
     * POST /exportSupplierMaster
     *
     * @param Request $request
     *
     * @return Response
     */
    public function exportSupplierMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'supplierNatureID', 'isActive'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $supplierId = 'supplierCodeSytem';
        if ($request['type'] == 'all') {
            $supplierId = 'supplierCodeSystem';
        }

        $supplierCountryID = $request['supplierCountryID'];
        $supplierCountryID = (array)$supplierCountryID;
        $supplierCountryID = collect($supplierCountryID)->pluck('id');

        $liabilityAccountSysemID = $request['liabilityAccountSysemID'];
        $liabilityAccountSysemID = (array)$liabilityAccountSysemID;
        $liabilityAccountSysemID = collect($liabilityAccountSysemID)->pluck('id');

        $supplierMasters = $this->getSuppliersByFilterQry($input, $search, $supplierCountryID, $liabilityAccountSysemID)->orderBy($supplierId, $sort)->get();
        $data = array();
        $x = 0;
        foreach ($supplierMasters as $val) {
            $x++;
            $data[$x]['Supplier Code'] = $val->primarySupplierCode;
            $data[$x]['Supplier Name'] = $val->supplierName;
            $currency = "";
            $country = "";
            if (count($val['supplierCurrency']) > 0) {
                if ($val['supplierCurrency'][0]['currencyMaster']) {
                    $currency = $val['supplierCurrency'][0]['currencyMaster']['CurrencyCode'];
                }
            }

            if ($val['country']) {
                $country = $val['country']['countryName'];
            }

            $data[$x]['Country'] = $country;
            $data[$x]['Category'] = ($val->categoryMaster!=null && isset($val->categoryMaster->categoryDescription))?$val->categoryMaster->categoryDescription:'-';
            $data[$x]['Currency'] = $currency;
            $data[$x]['Address'] = $val->address;
            $data[$x]['Telephone'] = $val->telephone;
            $data[$x]['Fax'] = $val->fax;
            $data[$x]['Email'] = $val->supEmail;
            $data[$x]['Website'] = $val->webAddress;
            $data[$x]['Credit Limit'] = $val->creditLimit;
            $data[$x]['Credit Period'] = $val->creditPeriod;
            $data[$x]['ICV Category'] = ($val->supplierICVCategories!=null && isset($val->supplierICVCategories->categoryDescription))?$val->supplierICVCategories->categoryDescription:'';
            $data[$x]['ICV Sub Category'] = ($val->supplierICVSubCategories!=null && isset($val->supplierICVSubCategories->categoryDescription))?$val->supplierICVSubCategories->categoryDescription:'';
            $data[$x]['Critical Status'] = isset($val->critical->description)?$val->critical->description:'';
            $data[$x]['Liability Account'] = isset($val->liablity_account) ? $val->liablity_account->AccountCode. '-'. $val->liablity_account->AccountDescription : '';
            $data[$x]['Un-billed Account'] = isset($val->unbilled_account) ? $val->unbilled_account->AccountCode. '-'. $val->unbilled_account->AccountDescription : '';
            $data[$x]['LCC'] = ($val->isLCCYN==1)?'Yes':'No';
            $data[$x]['SME'] = ($val->isSMEYN==1)?'Yes':'No';
            $data[$x]['JSRS Number'] = $val->jsrsNo;
            $data[$x]['JSRS Expiry'] = ($val->jsrsExpiry)? \Helper::dateFormat($val->jsrsExpiry):'';
            $data[$x]['VAT Eligible'] = ($val->vatEligible) ? "Yes" : "No";
            $data[$x]['VAT Number'] = $val->vatNumber;
            $data[$x]['VAT Percentage'] = $val->vatPercentage;
        }

        $companyMaster = Company::find(isset($request->companyId)?$request->companyId:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );

        $fileName = 'supplier_master';
        $path = 'system/supplier_master/excel/';
        $type = 'xls';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }



    }

    public function printSuppliers(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'supplierNatureID', 'isActive'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $supplierId = 'supplierCodeSytem';
        if ($request['type'] == 'all') {
            $supplierId = 'supplierCodeSystem';
        }
        $supplierMasters = $this->getSuppliersByFilterQry($input, $search)->orderBy($supplierId, $sort)->get();

        $company = Company::find($request['companyId']);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $docRefNo = \Helper::getCompanyDocRefNo($request['companyId'], 56);

        $array = array('entities' => $supplierMasters, 'docRefNo' => $docRefNo, 'company' => $company);
        $time = strtotime("now");
        $fileName = 'suppliers_' . $time . '.pdf';
        $html = view('print.suppliers', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }

    public function getSuppliersByFilterQry($request, $search, $supplierCountryID, $liabilityAccountSysemID)
    {

        $input = $request;

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }
        if ($request['type'] == 'all') {
            $supplierMasters = SupplierMaster::with(['liablity_account', 'unbilled_account','categoryMaster', 'critical', 'country','supplierICVCategories','supplierICVSubCategories', 'supplierCurrency' => function ($query) {
                $query->where('isDefault', -1)
                    ->with(['currencyMaster']);
            }]);
        } else {
            //by_company
            $supplierMasters = SupplierAssigned::with(['liablity_account', 'unbilled_account', 'categoryMaster', 'critical', 'country','supplierICVCategories','supplierICVSubCategories', 'master', 'supplierCurrency' => function ($query) {
                $query->where('isDefault', -1)
                    ->with(['currencyMaster']);
            }])->whereIn('CompanySystemID', $childCompanies)->where('isAssigned', -1);

        }

        if (array_key_exists('supplierCountryID', $input)) {
            if ($input['supplierCountryID'] && !is_null($input['supplierCountryID'])) {
                $supplierMasters->whereIn('supplierCountryID', $supplierCountryID);
            }
        }

        if (array_key_exists('liabilityAccountSysemID', $input)) {
            if ($input['liabilityAccountSysemID'] && !is_null($input['liabilityAccountSysemID'])) {
                $supplierMasters->whereIn('liabilityAccountSysemID', $liabilityAccountSysemID);
            }
        }

        if (array_key_exists('isCriticalYN', $input)) {
            if ($input['isCriticalYN'] && !is_null($input['isCriticalYN'])) {
                $supplierMasters->where('isCriticalYN', '=', $input['isCriticalYN']);
            }
        }

        if (array_key_exists('supplierConfirmedYN', $input)) {
            if (($input['supplierConfirmedYN'] == 0 || $input['supplierConfirmedYN'] == 1) && !is_null($input['supplierConfirmedYN'])) {
                $supplierMasters->where('supplierConfirmedYN', '=', $input['supplierConfirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == 1) && !is_null($input['approvedYN'])) {
                $supplierMasters->where('approvedYN', '=', $input['approvedYN']);
            }
        }

        if (array_key_exists('supplierNatureID', $input)) {
            if ($input['supplierNatureID'] && !is_null($input['supplierNatureID'])) {
                $supplierMasters->where('supplierNatureID', '=', $input['supplierNatureID']);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $supplierMasters->where('isActive', '=', $input['isActive']);
            }
        }

        if ($search) {
            $supplierMasters = $supplierMasters->where(function ($query) use ($search) {
                $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return $supplierMasters;

    }


    /**
     * get supplier master approval by company.
     * POST /getAllSupplierMasterApproval
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAllSupplierMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $supplierMasters = DB::table('erp_documentapproved')
            ->select('suppliermaster.*', 'erp_documentapproved.documentApprovedID',
                'rollLevelOrder', 'currencymaster.CurrencyCode',
                'supplier_categories.category', 'approvalLevelID', 'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyID, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')->
                on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')->
                on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                    ->where('employeesdepartments.documentSystemID', 56)
                    ->whereIn('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('suppliermaster', function ($query) use ($companyID, $empID, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'supplierCodeSystem')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->whereIn('primaryCompanySystemID', $companyID)
                    ->where('suppliermaster.approvedYN', 0)
                    ->where('suppliermaster.supplierConfirmedYN', 1)
                    ->when($search != "", function ($q) use ($search) {
                        $q->where(function ($query) use ($search) {
                            $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                                ->orWhere('supplierName', 'LIKE', "%{$search}%");
                        });
                    });
            })
            ->leftJoin('supplier_categories', 'supplier_categories.id', '=', 'suppliermaster.supplier_category_id')
            ->leftJoin('currencymaster', 'suppliermaster.currency', '=', 'currencymaster.currencyID')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 56)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $supplierMasters = [];
        }

        return \DataTables::of($supplierMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);

    }
    public function getRetentionPercentage(Request $request)
    {
        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem', '=', $supplierId)
            ->first();
        return $this->sendResponse($supplier->retentionPercentage, 'Supplier Retention Percentage retrieved successfully');

    }

    /**
     * get business categories by supplier.
     * POST /getBusinessCategoriesBySupplier
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAssignBusinessCategoriesBySupplier(Request $request){
        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem',$supplierId)->first();
        $data = [];
        if ($supplier) {
            $supplierBusinessCategories = DB::table('supplierbusinesscategoryassign')
                ->select('suppliercategorymaster.supCategoryMasterID','suppliercategorymaster.categoryName','supplierbusinesscategoryassign.supplierBusinessCategoryAssignID')
                ->leftJoin('suppliercategorymaster','supplierbusinesscategoryassign.supCategoryMasterID','=','suppliercategorymaster.supCategoryMasterID')
                ->where('supplierbusinesscategoryassign.supplierID', $supplierId)->get();
            foreach ($supplierBusinessCategories as $supplierBusinessCategory){
                $supplierBusinessSubCategories = DB::table('suppliersubcategoryassign')
                    ->select('suppliersubcategoryassign.supplierSubCategoryAssignID','suppliercategorysub.categoryName')
                    ->leftJoin('suppliercategorysub','suppliersubcategoryassign.supSubCategoryID','=','suppliercategorysub.supCategorySubID')
                    ->where('suppliersubcategoryassign.supplierID', $supplierId)
                    ->where('suppliercategorysub.supMasterCategoryID', $supplierBusinessCategory->supCategoryMasterID)->get();
                if(count($supplierBusinessSubCategories) > 0){
                    foreach ($supplierBusinessSubCategories as $supplierBusinessSubCategory) {
                        $temp = [
                            "businessCategoryAssignID" => $supplierBusinessCategory->supplierBusinessCategoryAssignID,
                            "businessCategoryName" => $supplierBusinessCategory->categoryName,
                            "businessSubCategoryAssignID" => $supplierBusinessSubCategory->supplierSubCategoryAssignID,
                            "businessSubCategoryName" => $supplierBusinessSubCategory->categoryName
                        ];
                        $data[] = $temp;
                    }
                }
                else{
                    $temp = [
                        "businessCategoryAssignID" => $supplierBusinessCategory->supplierBusinessCategoryAssignID,
                        "businessCategoryName" => $supplierBusinessCategory->categoryName,
                        "businessSubCategoryAssignID" => 0,
                        "businessSubCategoryName" => null
                    ];
                    $data[] = $temp;
                }
            }
        }
        return $this->sendResponse($data, 'Supplier business category retrieved successfully');
    }

    public function getBusinessCategoriesBySupplier(){
        $businessCategories = SupplierCategoryMaster::where('isActive',1)->get();
        return $this->sendResponse($businessCategories, 'Supplier business category retrieved successfully');
    }

    public function createSupplierSubCategoryAssignRecord($supplierID,$supSubCategoryID){
        $businessSubCategoryAssign = new SupplierSubCategoryAssign();
        $businessSubCategoryAssign->supplierID = $supplierID;
        $businessSubCategoryAssign->supSubCategoryID = $supSubCategoryID;
        $businessSubCategoryAssign->save();
    }

    public function addBusinessCategoryToSupplier(Request $request){
        $businessCategoryID = $request['businessCategoryID'];
        $businessSubCategoryIDS = $request['businessSubCategoryID'];
        $supplierID = $request['supplierID'];
        
        $businessCategoryAssignCheck = SupplierBusinessCategoryAssign::where('supplierID',$supplierID)->where('supCategoryMasterID',$businessCategoryID)->first();
        if(!$businessCategoryAssignCheck){
            $businessCategoryAssign = new SupplierBusinessCategoryAssign();
            $businessCategoryAssign->supplierID = $supplierID;
            $businessCategoryAssign->supCategoryMasterID = $businessCategoryID;
            $businessCategoryAssign->save();
        }
        else{
            if(empty($businessSubCategoryIDS)){
                return $this->sendError('This main category has already been added',500);
            }
        }

        if (!empty($businessSubCategoryIDS)) {
            $ids = collect($businessSubCategoryIDS)->pluck('id');
            if($ids->count() > 1){
                $businessSubCategoryAssignIDs = SupplierSubCategoryAssign::where('supplierID',$supplierID)->whereIn('supSubCategoryID',$ids)->pluck('supSubCategoryID');
                $emptyIDs = array_diff($ids->toArray(),$businessSubCategoryAssignIDs->toArray());
                if(count($emptyIDs) > 0){
                    foreach ($emptyIDs as $id) {
                        $this->createSupplierSubCategoryAssignRecord($supplierID,$id);
                    }
                }
                else{
                    return $this->sendError('These subcategories have already been added',500);
                }
            }
            else{
                $businessSubCategoryAssignCheck = SupplierSubCategoryAssign::where('supplierID',$supplierID)->where('supSubCategoryID',$ids->first())->first();
                if(!$businessSubCategoryAssignCheck){
                    $this->createSupplierSubCategoryAssignRecord($supplierID,$ids->first());
                }
                else{
                    return $this->sendError('This subcategory has already been added',500);
                }
            }
        }
        return $this->sendResponse([], 'Supplier business category added successfully');
    }

    /**
     * Store a newly created SupplierMaster in storage.
     * POST /supplierMasters
     *
     * @param CreateSupplierMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierMasterAPIRequest $request)
    {
        $input = $this->convertArrayToValue($request->all());
        $employee = \Helper::getEmployeeInfo();
     
        if($input['UnbilledGRVAccountSystemID'] == $input['liabilityAccountSysemID'] ){
            return $this->sendError('Liability account and unbilled account cannot be same. Please select different chart of accounts.');
        }

        $validatorResult = \Helper::checkCompanyForMasters($input['primaryCompanySystemID']);
        if (!$validatorResult['success']) {
            return $this->sendError($validatorResult['message']);
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $input['uniqueTextcode'] = 'S';

        if (array_key_exists('supplierCountryID', $input)) {
            $input['countryID'] = $input['supplierCountryID'];
        }

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        $input['primaryCompanyID'] = $company->CompanyID;

        $document = DocumentMaster::where('documentID', 'SUPM')->first();

        $input['documentSystemID'] = $document->documentSystemID;
        $input['documentID'] = $document->documentID;

        $input['isActive'] = 1;
        //$input['isCriticalYN'] = 1;

        $liabilityAccountSysemID = ChartOfAccount::where('chartOfAccountSystemID', $input['liabilityAccountSysemID'])->first();
        $unbilledGRVAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $input['UnbilledGRVAccountSystemID'])->first();
        $advanceAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $input['advanceAccountSystemID'])->first();

        $input['liabilityAccount'] = $liabilityAccountSysemID['AccountCode'];
        $input['UnbilledGRVAccount'] = $unbilledGRVAccountSystemID['AccountCode'];
        $input['AdvanceAccount'] = $advanceAccountSystemID['AccountCode'];

        if (isset($input['linkCustomerYN']) && isset($input['linkCustomerID']) && $input['linkCustomerYN'] == 1) {
            $checkLinkCustomer = SupplierMaster::where('primaryCompanySystemID', $input['primaryCompanySystemID'])->where('linkCustomerID',$input['linkCustomerID'])->where('linkCustomerYN',1)->first();
                if ($checkLinkCustomer) {
                    return $this->sendError('The selected customer is already assigned');
                }
        }

        if (isset($input['interCompanyYN']) && $input['interCompanyYN']) {
            if (!isset($input['companyLinkedToSystemID'])) {
                return $this->sendError('Linked company is required',500);
            }

            $checkCustomerForInterCompany = SupplierMaster::where('companyLinkedToSystemID', $input['companyLinkedToSystemID'])
                                                           ->when(array_key_exists('supplierCodeSystem', $input), function($query) use ($input) {
                                                                $query->where('supplierCodeSystem', '!=', $input['supplierCodeSystem']);
                                                           })
                                                           ->first();
            
            if ($checkCustomerForInterCompany) {
                return $this->sendError('The selected company is already assigned to ' .$checkCustomerForInterCompany->supplierName,500);
            }


            $linkedCompany = Company::find($input['companyLinkedToSystemID']);

            $input['companyLinkedTo'] = ($linkedCompany) ? $linkedCompany->CompanyID : null; 
        } else {
            $input['companyLinkedTo'] = null;
            $input['companyLinkedToSystemID'] = null;
        }


        $supplierMasters = $this->supplierMasterRepository->create($input);


        $updateSupplierMasters = SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])->first();
        $updateSupplierMasters->primarySupplierCode = 'S0' . strval($supplierMasters->supplierCodeSystem);

        $updateSupplierMasters->save();

        if (isset($input['currency']) && $input['currency'] > 0) {
            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
            $empId = $user->employee['empID'];
            $empName = $user->employee['empName'];

            $supplierCurrency = new SupplierCurrency();
            $supplierCurrency->supplierCodeSystem = $supplierMasters->supplierCodeSystem;
            $supplierCurrency->currencyID = $input['currency'];
            $supplierCurrency->isAssigned = -1;
            $supplierCurrency->isDefault = -1;
            $supplierCurrency->save();

            $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();

            foreach ($companyDefaultBankMemos as $value) {
                $temBankMemo = new BankMemoSupplier();
                $temBankMemo->memoHeader = $value['bankMemoHeader'];
                $temBankMemo->bankMemoTypeID = $value['bankMemoTypeID'];
                $temBankMemo->memoDetail = '';
                $temBankMemo->supplierCodeSystem = $supplierMasters->supplierCodeSystem;
                $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
                $temBankMemo->updatedByUserID = $empId;
                $temBankMemo->updatedByUserName = $empName;
                $temBankMemo->save();
            }
        }

        return $this->sendResponse($supplierMasters->toArray(), 'Supplier Master saved successfully');
    }


    public function updateSupplierMaster(Request $request)
    {
        $input = $this->convertArrayToValue(array_except($request->all(),['company', 'final_approved_by', 'blocked_by']));

        if($input['liabilityAccountSysemID'] == $input['UnbilledGRVAccountSystemID'] ){
            return $this->sendError('Liability account and unbilled account cannot be same. Please select different chart of accounts.');
        }

        $id = $input['supplierCodeSystem'];

        $input = array_except($input, ['supplierConfirmedEmpID', 'supplierConfirmedEmpSystemID',
            'supplierConfirmedEmpName', 'supplierConfirmedDate', 'final_approved_by', 'blocked_by','companySystemID']);
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $input['isLCCYN'] = isset($input['isLCCYN'])?$input['isLCCYN']:null;
        $input['isSMEYN'] = isset($input['isSMEYN'])?$input['isSMEYN']:null;

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }else{
            return $this->sendError('Company not found');
        }

        $isConfirm = $input['supplierConfirmedYN'];
        //unset($input['companySystemID']);

        if (array_key_exists('supplierCountryID', $input)) {
            $input['countryID'] = $input['supplierCountryID'];
        }

        if (isset($input['linkCustomerYN']) && isset($input['linkCustomerID']) && $input['linkCustomerYN'] == 1) {
            $checkLinkCustomer = SupplierMaster::where('primaryCompanySystemID', $input['primaryCompanySystemID'])->where('linkCustomerID',$input['linkCustomerID'])->where('linkCustomerYN',1)->first();
            $alreadyLinkedCustomer = SupplierMaster::where('primaryCompanySystemID', $input['primaryCompanySystemID'])->where('supplierCodeSystem',$input['supplierCodeSystem'])->where('linkCustomerYN',1)->first();
            if($alreadyLinkedCustomer) {
                if (isset($alreadyLinkedCustomer->linkCustomerID)) {
                    if ($checkLinkCustomer && $alreadyLinkedCustomer->linkCustomerID != $input['linkCustomerID']) {
                        return $this->sendError('The selected customer is already assigned');
                    }
                }
            }
            else{
                if ($checkLinkCustomer) {
                    return $this->sendError('The selected customer is already assigned');
                }
            }
        }
        if (isset($input['interCompanyYN']) && $input['interCompanyYN']) {
            if (!isset($input['companyLinkedToSystemID'])) {
                return $this->sendError('Linked company is required',500);
            }

            $checkCustomerForInterCompany = SupplierMaster::where('companyLinkedToSystemID', $input['companyLinkedToSystemID'])
                                                           ->when(array_key_exists('supplierCodeSystem', $input), function($query) use ($input) {
                                                                $query->where('supplierCodeSystem', '!=', $input['supplierCodeSystem']);
                                                           })
                                                           ->first();

            if ($checkCustomerForInterCompany) {
                return $this->sendError('The selected company is already assigned to ' .$checkCustomerForInterCompany->supplierName,500);
            }


            $linkedCompany = Company::find($input['companyLinkedToSystemID']);

            $input['companyLinkedTo'] = ($linkedCompany) ? $linkedCompany->CompanyID : null; 
        }

        $liabilityAccountSysemID = ChartOfAccount::where('chartOfAccountSystemID', $input['liabilityAccountSysemID'])->first();
        $unbilledGRVAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $input['UnbilledGRVAccountSystemID'])->first();
        $advanceAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $input['advanceAccountSystemID'])->first();

        $input['liabilityAccount'] = $liabilityAccountSysemID['AccountCode'];
        $input['UnbilledGRVAccount'] = $unbilledGRVAccountSystemID['AccountCode'];
        $input['AdvanceAccount'] = $advanceAccountSystemID['AccountCode'];

        $supplierMaster = SupplierMaster::where('supplierCodeSystem', $id)->first();
        $supplierMasterOld = $supplierMaster->toArray();
        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        if(isset($input['retentionPercentage'])){
            if($input['retentionPercentage'] > 100){
                return $this->sendError('Retention Percentage cannot be greater than 100%');
            }
        }

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';

        if(isset($input['tenant_uuid']) ){
            unset($input['tenant_uuid']);
        }

        if(isset($input['db']) ){
            unset($input['db']);
        }

        if($supplierMaster->approvedYN){

            //check policy 3

            if ($input['nameOnPaymentCheque'] != $supplierMaster->nameOnPaymentCheque && ($input['supplierName'] == $supplierMaster->supplierName)) {
                $supplierMaster = $this->supplierMasterRepository->update(array_only($input,['nameOnPaymentCheque']), $id);

                return $this->sendResponse($supplierMaster->toArray(), 'SupplierMaster updated successfully');
            }

            $policy = Helper::checkRestrictionByPolicy($input['primaryCompanySystemID'],3);

            if($policy){

                if($company->companyCountry == $input['supplierCountryID']){
                    $validorMessages = [
                        'isLCCYN.required' => 'LCC field is required.',
                        'isSMEYN.required' => 'SME field is required.'
                    ];

                    $validator = \Validator::make($input, [
                        'isLCCYN' => 'required|numeric',
                        'isSMEYN' => 'required|numeric',
                    ],$validorMessages);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                }

                if ($supplierMaster->isBlocked != $input['isBlocked'] && $input['isBlocked'] == 1) {
                    $validorMessages = [
                        'blockedReason.required' => 'Blocked Comment is required.',
                    ];

                    $validator = \Validator::make($input, [
                        'blockedReason' => 'required',
                    ],$validorMessages);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }

                    $input['blockedBy'] = $employee->employeeSystemID;
                    $input['blockedDate'] = Carbon::now();
                } else if ($supplierMaster->isBlocked != $input['isBlocked'] && $input['isBlocked'] == 0) {
                    $input['blockedBy'] = null;
                    $input['blockedDate'] = null;
                    $input['blockedReason'] = null;
                }

                $previosValue = $supplierMaster->toArray();
                $newValue = $input;

                $previosValue['isLCCYN'] = isset($previosValue['isLCCYN'])?$previosValue['isLCCYN']:-1;
                $previosValue['isSMEYN'] = isset($previosValue['isSMEYN'])?$previosValue['isSMEYN']:-1;
                $newValue['isLCCYN'] = isset($input['isLCCYN'])?$input['isLCCYN']:-1;
                $newValue['isSMEYN'] = isset($input['isSMEYN'])?$input['isSMEYN']:-1;

                $supplierMaster = $this->supplierMasterRepository->update(array_only($input,['isLCCYN','isSMEYN','supCategoryICVMasterID','supCategorySubICVID','address','fax','registrationNumber','supEmail','webAddress','telephone','creditLimit','creditPeriod','vatEligible','vatNumber','vatPercentage','retentionPercentage','supplierImportanceID','supplierNatureID','supplierTypeID','supplier_category_id','supplier_group_id','jsrsNo','jsrsExpiry', 'isBlocked', 'blockedReason', 'blockedBy', 'blockedDate','advanceAccountSystemID','AdvanceAccount', 'liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount', 'isActive', 'supplierName', 'linkCustomerYN', 'linkCustomerID', 'nameOnPaymentCheque', 'registrationExprity','supplierCountryID']), $id);
                SupplierAssigned::where('supplierCodeSytem',$id)->update(array_only($input,['isLCCYN','supCategoryICVMasterID','supCategorySubICVID','address','fax','registrationNumber','supEmail','webAddress','telephone','creditLimit','creditPeriod','vatEligible','vatNumber','vatPercentage','supplierImportanceID','supplierNatureID','supplierTypeID','supplier_category_id','supplier_group_id','jsrsNo','jsrsExpiry', 'isBlocked', 'blockedReason', 'blockedBy', 'blockedDate','advanceAccountSystemID','AdvanceAccount', 'liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount', 'isActive', 'supplierName', 'nameOnPaymentCheque', 'registrationExprity','supplierCountryID']));
                // user activity log table
                if($supplierMaster){
                    $old_array = array_only($supplierMasterOld,['isLCCYN','isSMEYN','supCategoryICVMasterID','supCategorySubICVID','address','fax','registrationNumber','supEmail','webAddress','telephone','creditLimit','creditPeriod','vatEligible','vatNumber','vatPercentage','supplierImportanceID','supplierNatureID','supplierTypeID','jsrsNo','jsrsExpiry', 'isBlocked', 'blockedReason', 'blockedBy', 'blockedDate','advanceAccountSystemID','AdvanceAccount', 'liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount', 'isActive', 'supplierName', 'linkCustomerYN', 'linkCustomerID', 'nameOnPaymentCheque', 'registrationExprity','supplierCountryID']);
                    $modified_array = array_only($input,['isLCCYN','isSMEYN','supCategoryICVMasterID','supCategorySubICVID','address','fax','registrationNumber','supEmail','webAddress','telephone','creditLimit','creditPeriod','vatEligible','vatNumber','vatPercentage','supplierImportanceID','supplierNatureID','supplierTypeID','jsrsNo','jsrsExpiry', 'isBlocked', 'blockedReason', 'blockedBy', 'blockedDate','advanceAccountSystemID','AdvanceAccount', 'liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount', 'isActive', 'supplierName', 'linkCustomerYN', 'linkCustomerID', 'nameOnPaymentCheque', 'registrationExprity','supplierCountryID']);

                    // update in to user log table
                    foreach ($old_array as $key => $old){
                        if($old != $modified_array[$key]){
                            $description = $employee->empName." Updated supplier (".$supplierMaster->supplierCodeSystem.") from ".$old." To ".$modified_array[$key]."";
                            UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$supplierMaster->documentSystemID,$supplierMaster->primaryCompanySystemID,$supplierMaster->supplierCodeSystem,$description,$modified_array[$key],$old,$key);
                        }
                    }
                }

                $this->auditLog($db, $input['supplierCodeSystem'],$uuid, "suppliermaster", $input['primarySupplierCode']." has updated", "U", $newValue, $previosValue);

                return $this->sendResponse($supplierMaster->toArray(), 'SupplierMaster updated successfully');
            }

            return $this->sendError('Supplier Master is already approved , You cannot update.',500);
        }


        if ($isConfirm && $supplierMaster->supplierConfirmedYN == 0) {


            //check is supplier is local

            if($company->companyCountry == $input['supplierCountryID']){
                $validorMessages = [
                    'isLCCYN.required' => 'LCC field is required.',
                    'isSMEYN.required' => 'SME field is required.'
                ];

                $validator = \Validator::make($input, [
                    'isLCCYN' => 'required|numeric',
                    'isSMEYN' => 'required|numeric',
                ], $validorMessages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
            }

            $checkDefaultCurrency = SupplierCurrency::where('supplierCodeSystem', $id)
                ->where('isDefault', -1)
                ->count();

            if ($checkDefaultCurrency == 0) {
                return $this->sendError("Default currency is not assigned. Currency can be set up in currency", 500);
            }


            $params = array('autoID' => $id, 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }
        }

        $supplierMaster = $this->supplierMasterRepository->update($input, $id);

        if ($supplierMasterOld['currency'] != $input['currency']) {
            $checkSupplierCurrency = SupplierCurrency::where('supplierCodeSystem', $id)->get();

            if (count($checkSupplierCurrency) == 1) {
                SupplierCurrency::where('supplierCodeSystem', $id)->delete();
                BankMemoSupplier::where('supplierCurrencyID', collect($checkSupplierCurrency)->first()->supplierCurrencyID)->delete();


                $userID = Auth::id();
                $user = $this->userRepository->with(['employee'])->findWithoutFail($userID);
                $empId = $user->employee['empID'];
                $empName = $user->employee['empName'];

                $supplierCurrency = new SupplierCurrency();
                $supplierCurrency->supplierCodeSystem = $id;
                $supplierCurrency->currencyID = $input['currency'];
                $supplierCurrency->isAssigned = -1;
                $supplierCurrency->isDefault = -1;
                $supplierCurrency->save();

                $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();

                foreach ($companyDefaultBankMemos as $value) {
                    $temBankMemo = new BankMemoSupplier();
                    $temBankMemo->memoHeader = $value['bankMemoHeader'];
                    $temBankMemo->bankMemoTypeID = $value['bankMemoTypeID'];
                    $temBankMemo->memoDetail = '';
                    $temBankMemo->supplierCodeSystem = $id;
                    $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
                    $temBankMemo->updatedByUserID = $empId;
                    $temBankMemo->updatedByUserName = $empName;
                    $temBankMemo->save();
                }
            }
        }


        return $this->sendResponse($supplierMaster->toArray(), 'SupplierMaster updated successfully');
    }


    public function getAssignedCompaniesBySupplier(Request $request)
    {
        $supplierId = $request['supplierId'];

        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', '=', $supplierId)
            ->first();
        if ($supplier) {
            $supplierCompanies = DB::table('supplierassigned')
                ->leftJoin('supplierimportance', 'supplierassigned.supplierImportanceID', '=', 'supplierimportance.supplierImportanceID')
                ->leftJoin('suppliernature', 'supplierassigned.supplierNatureID', '=', 'suppliernature.supplierNatureID')
                ->leftJoin('suppliertype', 'supplierassigned.supplierTypeID', '=', 'suppliertype.supplierTypeID')
                ->leftJoin('suppliercritical', 'supplierassigned.isCriticalYN', '=', 'suppliercritical.suppliercriticalID')
                ->leftJoin('yesnoselection', 'supplierassigned.isActive', '=', 'yesnoselection.idyesNoselection')
                ->where('supplierCodeSytem', $supplierId)
                ->whereIn("companySystemID",$subCompanies)
                ->orderBy('supplierAssignedID', 'DESC')
                ->get();
        } else {
            $supplierCompanies = [];
        }

        return $this->sendResponse($supplierCompanies, 'Supplier Category Subs retrieved successfully');
    }

    /**
     * Display the specified SupplierMaster.
     * GET|HEAD /supplierMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id,Request $request)
    {

        $companyId = $request->get('company_id');



        /** @var SupplierMaster $supplierMaster */
        $supplierMaster = $this->supplierMasterRepository->with(['finalApprovedBy', 'blocked_by','company'])->findWithoutFail($id);

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        if($companyId){
            $supplierMaster->isLocalSupplier = Helper::isLocalSupplier($id,$companyId);
        }else{
            $supplierMaster->isLocalSupplier = false;
        }

        return $this->sendResponse($supplierMaster->toArray(), 'Supplier Master retrieved successfully');
    }

    public function supplierUsage(Request $request){
        $input = $request->all();
        $supplierCodeSystem = $input['supplierCodeSystem'];

        $PO = ProcumentOrder::where('supplierID',$supplierCodeSystem)->where('approved',0)->get();
        $GRV = GRVMaster::where('supplierID',$supplierCodeSystem)->where('approved',0)->get();
        $supplierInvoice = BookInvSuppMaster::where('supplierID',$supplierCodeSystem)->where('approved',0)->get();
        $paymentVoucher = PaySupplierInvoiceMaster::where('BPVsupplierID',$supplierCodeSystem)->where('approved',0)->get();
        $debitNote = DebitNote::where('supplierID',$supplierCodeSystem)->where('approved',0)->get();

        $supplierUsageData=[];

        foreach ($PO as $order) {
            $supplierUsageData[] = $order->purchaseOrderCode;
        }
        
        foreach ($GRV as $grv) {
            $supplierUsageData[] = $grv->grvPrimaryCode;
        }
        
        foreach ($supplierInvoice as $invoice) {
            $supplierUsageData[] = $invoice->bookingInvCode;
        }
        
        foreach ($paymentVoucher as $voucher) {
            $supplierUsageData[] = $voucher->BPVcode;
        }
        
        foreach ($debitNote as $note) {
            $supplierUsageData[] = $note->debitNoteCode;
        }

        return $this->sendResponse($supplierUsageData, 'Supplier usage retrieved successfully');

    }

    public function getSupplierMaster(Request $request)
    {
         $input = $request->all();
         $supplierId = $input['autoID'];
        
         $supplier = SupplierMaster::with('currency','company','confirmed_by','approved_by','supplier_group','supplier_category','country','importance','nature','type','critical')
                                                ->where('supplierCodeSystem', $supplierId)->first();

         if (empty($supplier)) {
             return $this->sendError('Supplier Master not found');
         }
         $data = [];
         if ($supplier) {
             $supplierBusinessCategories = DB::table('supplierbusinesscategoryassign')
                 ->select('suppliercategorymaster.supCategoryMasterID','suppliercategorymaster.categoryName','supplierbusinesscategoryassign.supplierBusinessCategoryAssignID')
                 ->leftJoin('suppliercategorymaster','supplierbusinesscategoryassign.supCategoryMasterID','=','suppliercategorymaster.supCategoryMasterID')
                 ->where('supplierbusinesscategoryassign.supplierID', $supplierId)->get();
             foreach ($supplierBusinessCategories as $supplierBusinessCategory){
                 $supplierBusinessSubCategories = DB::table('suppliersubcategoryassign')
                     ->select('suppliersubcategoryassign.supplierSubCategoryAssignID','suppliercategorysub.categoryName')
                     ->leftJoin('suppliercategorysub','suppliersubcategoryassign.supSubCategoryID','=','suppliercategorysub.supCategorySubID')
                     ->where('suppliersubcategoryassign.supplierID', $supplierId)
                     ->where('suppliercategorysub.supMasterCategoryID', $supplierBusinessCategory->supCategoryMasterID)->get();
                 if(count($supplierBusinessSubCategories) > 0){
                     foreach ($supplierBusinessSubCategories as $supplierBusinessSubCategory) {
                         $temp = [
                             "businessCategoryAssignID" => $supplierBusinessCategory->supplierBusinessCategoryAssignID,
                             "businessCategoryName" => $supplierBusinessCategory->categoryName,
                             "businessSubCategoryAssignID" => $supplierBusinessSubCategory->supplierSubCategoryAssignID,
                             "businessSubCategoryName" => $supplierBusinessSubCategory->categoryName
                         ];
                         $data[] = $temp;
                     }
                 }
                 else{
                     $temp = [
                         "businessCategoryAssignID" => $supplierBusinessCategory->supplierBusinessCategoryAssignID,
                         "businessCategoryName" => $supplierBusinessCategory->categoryName,
                         "businessSubCategoryAssignID" => 0,
                         "businessSubCategoryName" => null
                     ];
                     $data[] = $temp;
                 }
             }
         }
         $supplierData = [
            'supplierMaster'=> $supplier,
            'supplierBusinessData' => $data
         ];

         return $this->sendResponse($supplierData, 'Supplier Master retrieved successfully');
    }
    
    /**
     * Update the specified SupplierMaster in storage.
     * PUT/PATCH /supplierMasters/{id}
     *
     * @param  int $id
     * @param UpdateSupplierMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierMaster $supplierMaster */
        $supplierMaster = $this->supplierMasterRepository->findWithoutFail($id);

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        $supplierMaster = $this->supplierMasterRepository->update($input, $id);

        return $this->sendResponse($supplierMaster->toArray(), 'SupplierMaster updated successfully');
    }

    /**
     * Remove the specified SupplierMaster from storage.
     * DELETE /supplierMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierMaster $supplierMaster */
        $supplierMaster = $this->supplierMasterRepository->findWithoutFail($id);

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        $supplierMaster->delete();

        return $this->sendResponse($id, 'Supplier Master deleted successfully');
    }


    public function approveSupplier(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectSupplier(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getSuppliersByCompany(Request $request)
    {
        $supplierMaster = SupplierAssigned::where('companySystemID', $request->selectedCompanyId)->get();
        return $this->sendResponse($supplierMaster, 'Supplier Master retrieved successfully');
    }

    public function getPOSuppliers(Request $request)
    {
        $companyId = $request->selectedCompanyId;
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $filterSuppliers = ProcumentOrder::whereIN('companySystemID', $companyID)
            ->select('supplierID')
            ->groupBy('supplierID')
            ->pluck('supplierID');
        $supplierMaster = SupplierAssigned::whereIN('companySystemID', $companyID)->whereIN('supplierCodeSytem', $filterSuppliers)->groupBy('supplierCodeSytem')->get();

        $controlAccount = SupplierMaster::groupBy('liabilityAccountSysemID')->pluck('liabilityAccountSysemID');
        $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $controlAccount)->get();

        $segment = SegmentMaster::ofCompany($companyID)->get();
        $output = array(
            'controlAccount' => $controlAccount,
            'suppliers' => $supplierMaster,
            'segment' => $segment
        );
        return $this->sendResponse($output, 'Supplier Master retrieved successfully');
    }


    /**
     *  Search Supplier By Company
     * GET /getSearchSupplierByCompany
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getSearchSupplierByCompany(Request $request)
    {

        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $suppliers = SupplierAssigned::whereIn('companySystemID', $companies)
            ->select(['supplierCodeSytem', 'supplierName', 'primarySupplierCode'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                        ->orWhere('supplierName', 'LIKE', "%{$search}%");
                });
            })
            ->take(20)
            ->get();


        return $this->sendResponse($suppliers->toArray(), 'Data retrieved successfully');
    }

    /**
     * Display the specified Supplier Master Audit.
     * GET|HEAD /getSupplierMasterAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getSupplierMasterAudit(Request $request)
    {
        $id = $request->get('id');

        $supplierMaster = $this->supplierMasterRepository
            ->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 56);
            }])
            ->findWithoutFail($id);

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        return $this->sendResponse($supplierMaster->toArray(), 'Materiel Issue retrieved successfully');
    }

    public function supplierReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $supplier = $this->supplierMasterRepository->find($id);
        if (empty($supplier)) {
            return $this->sendError('Supplier Master not found');
        }

        if ($supplier->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this supplier');
        }

        $supplierArray = $supplier->toArray();
        $supplierArray = array_except($supplierArray,['isSUPDAmendAccess']);
        $storeHistory = SupplierMasterRefferedBack::insert($supplierArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $supplier->primaryCompanySystemID)
            ->where('documentSystemID', $supplier->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $supplier->timesReferred;
            }
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $supplier->primaryCompanySystemID)
            ->where('documentSystemID', $supplier->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,
                'supplierConfirmedYN' => 0,
                'supplierConfirmedEmpSystemID' => null,
                'supplierConfirmedEmpID' => null,
                'supplierConfirmedEmpName' => null,
                'supplierConfirmedDate' => null,
                'RollLevForApp_curr' => 1];

            $this->supplierMasterRepository->update($updateArray, $id);
        }

        return $this->sendResponse($supplier->toArray(), 'Supplier Master Amend successfully');
    }

    public function generateSupplierExternalLink(Request $request)
    {
        $input = $request->all();
        $bytes = random_bytes(20);
        $hashKey = bin2hex($bytes);
        $empID = \Helper::getEmployeeSystemID();

        $expiredDays = $input['expiryPeriod'];

        $insertData = [
            'hashKey' => $hashKey,
            'generatedBy' => $empID,
            'genratedDate' => Carbon::now(),
            'expiredIn' => Carbon::now()->addDays($expiredDays),
            'isUsed' => 0,
            'companySystemID' => $input['companySystemID']
        ];

        $resData = ExternalLinkHash::create($insertData);

        return $this->sendResponse($hashKey, 'External link generated successfully');
    }

    public function validateSupplierRegistrationLink(Request $request)
    {
        $input = $request->all();

        if (!isset($input['uid'])) {
            return $this->sendError("Hash not found");
        }

        $checkHash = ExternalLinkHash::where('hashKey', $input['uid'])->first();

        if (!$checkHash) {
            return $this->sendError("Hash not found");
        }

        if (Carbon::parse($checkHash->expiredIn)->startOfDay() < Carbon::now()->startOfDay()) {

            return $this->sendError('This link for Supplier Registration has expired. Obtain an active link to proceed with the registration');

        }

        return $this->sendResponse([], 'External link validated successfully');
    }

    public function getSupplierRegisterFormData(Request $request)
    {
        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')
            ->get();

        $currencyMaster = CurrencyMaster::orderBy('CurrencyName', 'asc')
                                          ->get();
        $companyDefaultBankMemos = BankMemoTypes::whereIn('bankMemoTypeID', [1, 2, 3, 4, 6, 7, 9])
                                                ->orderBy('sortOrder', 'asc')->get();

        $contactTypes = SupplierContactType::all();
        $yesNoSelection = YesNoSelection::all();

        $businessCategories = SupplierCategoryMaster::where('isActive',1)->get();

        $output = [
                'country' => $country,
                'currencyMaster' => $currencyMaster,
                'contactTypes' => $contactTypes,
                'yesNoSelection' => $yesNoSelection,
                'companyDefaultBankMemos' => $companyDefaultBankMemos,
                'businessCategories' => $businessCategories,
            ];
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function registerSupplier(Request $request)
    {
        $input = $request->all();

        if (!isset($input['uid'])) {
            return $this->sendError("Hash not found");
        }

        $checkHash = ExternalLinkHash::where('hashKey', $input['uid'])->first();

        if (!$checkHash) {
            return $this->sendError("Hash not found");
        }

        if (Carbon::parse($checkHash->expiredIn) < Carbon::now()) {
             return $this->sendError("Hash expired");
        }


        $validorMessages = [
            'supplierName.required' => 'Name is required.',
            'telephone.required' => 'Telephone Number is required.',
            'supEmail.required' => 'Email is required.',
            'supplierCountryID.required' => 'Country is required.',
            'registrationExprity.required' => 'Registartion Expire date is required.',
            'currency.required' => 'Currency is required.',
            'nameOnPaymentCheque.required' => 'Name on payment cheque is required.',
            'address.required' => 'Address is required.',
            'registrationNumber.required' => 'Registartion Number is required.',
        ];

        $validator = \Validator::make($input, [
            'supplierName' => 'required',
            'telephone' => 'required',
            'supEmail' => 'required',
            'supplierCountryID' => 'required',
            'registrationExprity' => 'required',
            'currency' => 'required',
            'nameOnPaymentCheque' => 'required',
            'address' => 'required',
            'registrationNumber' => 'required',
        ], $validorMessages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $masterData = [
            'supplierName' => $input['supplierName'],
            'telephone' => $input['telephone'],
            'supEmail' => $input['supEmail'],
            'supplierCountryID' => $input['supplierCountryID'],
            'registrationExprity' => Carbon::parse($input['registrationExprity']),
            'currency' => $input['currency'],
            'nameOnPaymentCheque' => $input['nameOnPaymentCheque'],
            'address' => $input['address'],
            'fax' => isset($input['fax']) ? $input['fax'] : null,
            'webAddress' => isset($input['webAddress']) ? $input['webAddress'] : null,
            'registrationNumber' => $input['registrationNumber']
        ];
        
        DB::beginTransaction();
        try {
            $resMaster = RegisteredSupplier::create($masterData);
            if ($resMaster) {
                $supplierCurrencyData = [
                    'registeredSupplierID' => $resMaster->id,
                    'currencyID' => $input['currency'],
                    'isAssigned' => -1,
                    'isDefault' => -1,
                ]; 

                $resSupplierCurrency = RegisteredSupplierCurrency::create($supplierCurrencyData);

                if ($resSupplierCurrency) {
                    foreach ($input['companyDefaultBankMemos'] as $key => $value) {
                        $memoData = [
                            'memoHeader' => $value['bankMemoHeader'],
                            'memoDetail' => isset($value['memoDetail']) ? $value['memoDetail'] : null,
                            'registeredSupplierID' => $resMaster->id,
                            'supplierCurrencyID' => $resSupplierCurrency->id,
                            'bankMemoTypeID' => $value['bankMemoTypeID']
                        ];

                        $resBankMemo = RegisteredBankMemoSupplier::create($memoData);
                    }
                }

                foreach ($input['contactDetails'] as $key => $value) {
                    if ($value['contractType'] != null) {
                        $contactData = [
                            'registeredSupplierID' => $resMaster->id,
                            'contactTypeID' => $value['contractType'],
                            'contactPersonName' => $value['contractPersonName'],
                            'contactPersonTelephone' => $value['contractTelephone'],
                            'contactPersonFax' => $value['contarctFax'],
                            'contactPersonEmail' => $value['contractEmail'],
                            'isDefault' => ($value['isDefault'] == 1) ? -1 : 0
                        ];

                        $resContact = RegisteredSupplierContactDetail::create($contactData);
                    }
                }

                // foreach ($input['businessCategoryID'] as $key => $value) {
                //     $masterCategory = [
                //         'supplierID' => $resMaster->id,
                //         'supCategoryMasterID' => $value['id'],
                //     ];
                //     $resContact = RegisterSupplierBusinessCategoryAssign::create($masterCategory);
                //   }
                  

                  foreach ($input['businessSubCategoryID'] as $key => $value) {

                    $mainCategory = SupplierCategorySub::where('supCategorySubID',$value['id'])->select('supCategorySubID','supMasterCategoryID')->first();

                    RegisterSupplierBusinessCategoryAssign::updateOrCreate(
                        ['supplierID' => $resMaster->id,'supCategoryMasterID' => $mainCategory->supMasterCategoryID],
                        ['supplierID' => $resMaster->id,'supCategoryMasterID' => $mainCategory->supMasterCategoryID]
                    );

                    $subCategory = [
                        'supplierID' => $resMaster->id,
                        'supSubCategoryID' => $value['id'],
                    ];
                    $resContact = RegisterSupplierSubcategoryAssign::create($subCategory);
                  }


            }

            $checkHash->save();

            foreach ($input['attachments'] as $key => $value) {
                if (isset($value['description']) && $value['description'] != "") {
                    $attachemntDescription = isset($value['description']) ? $value['description'] : "";

                    $fileData = $value['file'];

                    $extension = $fileData['fileType'];

                    $blockExtensions = ['ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
                        'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
                        'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
                        'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
                         'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
                          'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
                          'xbap', 'xnk','php'];

                    if (in_array($extension, $blockExtensions))
                    {
                        DB::rollback();
                        return $this->sendError('This type of file not allow to upload.',500);
                    }


                    if(isset($fileData['size'])){
                        if ($fileData['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                            DB::rollback();
                            return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than ".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')),500);
                        }
                    }

                    $file = $fileData['file'];

                    $fileData = $this->convertArrayToValue($fileData);

                    $attachmentData = [
                        'resgisteredSupplierID' => $resMaster->id,
                        'attachmentDescription' => $attachemntDescription,
                        'originalFileName' => isset($fileData['originalFileName']) ? $fileData['originalFileName'] : null,
                        'sizeInKbs' => isset($fileData['sizeInKbs']) ? $fileData['sizeInKbs'] : null,
                    ];

                    $companyMaster = Company::where('companySystemID', $checkHash->companySystemID)->first();
                    if ($companyMaster) {
                        $companyID = $companyMaster->CompanyID;
                    }

                    $documentAttachments = RegisteredSupplierAttachment::create($attachmentData);

                    $decodeFile = base64_decode($file);

                    $updateData['myFileName'] = $resMaster->id . '_' . $documentAttachments->id . '.' . $extension;

                    $disk = Helper::policyWiseDisk($checkHash->companySystemID, 'public');

                    if (Helper::checkPolicy($checkHash->companySystemID, 50)) {
                        $path = $companyID.'/G_ERP/registered-supplier/'.$documentAttachments->id . '/' . $updateData['myFileName'];
                    } else {
                        $path = 'registered-supplier/'.$documentAttachments->id . '/' . $updateData['myFileName'];
                    }

                    Storage::disk($disk)->put($path, $decodeFile);

                    $updateData['isUploaded'] = 1;
                    $updateData['path'] = $path;

                    $updateRes = RegisteredSupplierAttachment::where('id', $documentAttachments->id)
                                                             ->update($updateData);
                }
            }

            $companMaster = Company::find($checkHash->companySystemID);


            $emails = [
                        'companySystemID' => $checkHash->companySystemID,
                        'alertMessage' => "Supplier Registartion",
                        'empEmail' => $input['supEmail'],
                        'emailAlertMessage' => 'Thank you for registering with '.$companMaster->CompanyName.'. Your registration will be reviewed and notified'
                      ];

            $sendEmail = \Email::sendEmailErp($emails);

            DB::commit();
            return $this->sendResponse([], 'Thank you for registering with '.$companMaster->CompanyName.'. Your registration will be reviewed and notified via email');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage().$e->getLine());
        }
    }


     public function notApprovedRegisteredSuppliers(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $input['approvedYN'] = 0;

        $search = $request->input('search.value');
        $supplierMasters = $this->getRegisteredSuppliersByFilterQry($input, $search);

        return \DataTables::eloquent($supplierMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

     public function approvedRegisteredSuppliers(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $input['approvedYN'] = 1;
        
        $search = $request->input('search.value');
        $supplierMasters = $this->getRegisteredSuppliersByFilterQry($input, $search);

        return \DataTables::eloquent($supplierMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getRegisteredSuppliersByFilterQry($request, $search)
    {
        $input = $request;

        $supplierMasters = RegisteredSupplier::with(['country']);

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == 1) && !is_null($input['approvedYN'])) {
                $supplierMasters->where('approvedYN', '=', $input['approvedYN']);
            }
        }

        if ($search) {
            $supplierMasters = $supplierMasters->where(function ($query) use ($search) {
                $query->where('supplierName', 'LIKE', "%{$search}%")
                    ->orWhere('telephone', 'LIKE', "%{$search}%")
                    ->orWhere('supEmail', 'LIKE', "%{$search}%");
            });
        }

        return $supplierMasters;

    }

    public function getRegisteredSupplierData(Request $request)
    {
        $input = $request->all();

        $data['data'] = RegisteredSupplier::with(['supplier_currency' => function($query) {
                                        $query->with(['currency_master']);
                                    }, 'supplier_contact_details' => function($query) {
                                        $query->with(['contact_type']);
                                    }, 'supplier_attachments', 'final_approved_by'])
                                  ->where('id', $input['supplierID'])
                                  ->first();     

        $categories = [];                           
        $supplierBusinessCategories = DB::table('registersupplierbusinesscategoryassign')
                ->select('suppliercategorymaster.supCategoryMasterID','suppliercategorymaster.categoryName','registersupplierbusinesscategoryassign.id')
                ->leftJoin('suppliercategorymaster','registersupplierbusinesscategoryassign.supCategoryMasterID','=','suppliercategorymaster.supCategoryMasterID')
                ->where('registersupplierbusinesscategoryassign.supplierID', $input['supplierID'])->get();
                foreach ($supplierBusinessCategories as $supplierBusinessCategory){
                    $supplierBusinessSubCategories = DB::table('registersuppliersubcategoryassign')
                        ->select('registersuppliersubcategoryassign.id','suppliercategorysub.categoryName')
                        ->leftJoin('suppliercategorysub','registersuppliersubcategoryassign.supSubCategoryID','=','suppliercategorysub.supCategorySubID')
                        ->where('registersuppliersubcategoryassign.supplierID', $input['supplierID'])
                        ->where('suppliercategorysub.supMasterCategoryID', $supplierBusinessCategory->supCategoryMasterID)->get();
                    if(count($supplierBusinessSubCategories) > 0){
                        foreach ($supplierBusinessSubCategories as $supplierBusinessSubCategory) {
                            $temp = [
                                "businessCategoryAssignID" => $supplierBusinessCategory->id,
                                "businessCategoryName" => $supplierBusinessCategory->categoryName,
                                "businessSubCategoryAssignID" => $supplierBusinessSubCategory->id,
                                "businessSubCategoryName" => $supplierBusinessSubCategory->categoryName
                            ];
                            $categories[] = $temp;
                        }
                    }
                    else{
                        $temp = [
                            "businessCategoryAssignID" => $supplierBusinessCategory->id,
                            "businessCategoryName" => $supplierBusinessCategory->categoryName,
                            "businessSubCategoryAssignID" => 0,
                            "businessSubCategoryName" => null
                        ];
                        $categories[] = $temp;
                    }
                 }                          
        
        $data['categories'] = $categories;
        return $this->sendResponse($data, 'Supplier data retrived successfully');   
    }

    public function bankMemosByRegisteredSupplierCurrency(Request $request)
    {
        $input = $request->all();

        $data = RegisteredBankMemoSupplier::where('supplierCurrencyID', $input['supplierCurrencyID'])
                                          ->where('registeredSupplierID', $input['registeredSupplierID'])
                                          ->get();

        return $this->sendResponse($data, 'Supplier data retrived successfully');  
    }

    public function updateRegisteredSupplierAttachment(Request $request)
    {
        $input = $request->all();
        $update = RegisteredSupplierAttachment::where('id', $input['id'])
                                              ->update($input);


        return $this->sendResponse([], 'Updated successfully');  
    }

   public function updateRegisteredSupplierCurrency(Request $request)
    {
        $input = $request->all();
        unset($input['currency_master']);
        $input['isAssigned'] = ($input['isAssigned'] == 1 || $input['isAssigned'] == -1) ? -1 : 0;
        $input['isDefault'] = ($input['isDefault'] == 1 || $input['isDefault'] == -1) ? -1 : 0;

        $update = RegisteredSupplierCurrency::where('id', $input['id'])
                                              ->update($input);


        return $this->sendResponse([], 'Updated successfully');  
    }

    public function updateRegisteredSupplierBankMemo(Request $request)
    {
        $input = $request->all();
        $update = RegisteredBankMemoSupplier::where('id', $input['id'])
                                              ->update($input);


        return $this->sendResponse([], 'Updated successfully');  
    }

    public function updateRegisteredSupplierMaster(Request $request)
    {
        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        unset($input['supplier_attachments']);
        unset($input['final_approved_by']);
        unset($input['supplier_contact_details']);
        unset($input['supplier_currency']);

        $supplierConfirmedYN = $input['supplierConfirmedYN'];

        $input = $this->convertArrayToValue($input);

        DB::beginTransaction();
        try {
            unset($input['supplierConfirmedYN']);
            $update = RegisteredSupplier::where('id', $input['id'])
                                        ->update($input);

            if ($supplierConfirmedYN == 1) {
                $params = array('autoID' => $input['id'], 'company' => $companySystemID, 'document' => 86);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"]);
                }
            }

            DB::commit();
            return $this->sendResponse([], 'Supplier updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage().$e->getLine());
        }

        return $this->sendResponse([], 'Updated successfully');  
    }

    public function downloadSupplierAttachmentFile(Request $request)
    {

        $input = $request->all();
        $documentAttachments = RegisteredSupplierAttachment::find($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Attachments not found');
        }

        $supplierData = RegisteredSupplier::find($documentAttachments->resgisteredSupplierID);
        if (!$supplierData) {
            return $this->sendError('Supplier Data not found');
        }

        $disk = Helper::policyWiseDisk($supplierData->companySystemID, 'public');

        if(!is_null($documentAttachments->path)) {
            if ($exists = Storage::disk($disk)->exists($documentAttachments->path)) {
                return Storage::disk($disk)->download($documentAttachments->path, $documentAttachments->myFileName);
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        }else{
            return $this->sendError('Attachment is not attached', 404);
        }
    }


    public function getAllRegisteredSupplierApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $registeredSupplier = DB::table('erp_documentapproved')
            ->select('registeredsupplier.*', 'erp_documentapproved.documentApprovedID',
                'rollLevelOrder', 'currencymaster.CurrencyCode',
                'approvalLevelID', 'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyID, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')->
                on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')->
                on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                    ->where('employeesdepartments.documentSystemID', 86)
                    ->whereIn('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('registeredsupplier', function ($query) use ($companyID, $empID, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('registeredsupplier.approvedYN', 0)
                    ->where('registeredsupplier.supplierConfirmedYN', 1)
                    ->when($search != "", function ($q) use ($search) {
                        $q->where(function ($query) use ($search) {
                            $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                                ->orWhere('supplierName', 'LIKE', "%{$search}%");
                        });
                    });
            })
            ->leftJoin('currencymaster', 'registeredsupplier.currency', '=', 'currencymaster.currencyID')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 86)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $registeredSupplier = [];
        }

        return \DataTables::of($registeredSupplier)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);

    }

    public function approveRegisteredSupplier(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectRegisteredSupplier(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }


    public function supplierReOpen(Request $request)
    {
        $reopen = ReopenDocument::reopenDocument($request);
        if (!$reopen["success"]) {
            return $this->sendError($reopen["message"]);
        } else {
            return $this->sendResponse(array(), $reopen["message"]);
        }
    }

    public function srmRegistrationLink(Request $request)
    {
        $companyName = "";
        $company = Company::find($request->input('company_id'));
        if(isset($company->CompanyName)){
           $companyName =  $company->CompanyName;
        }
        $data['domain'] =  Helper::getDomainForSrmDocuments($request);
        $request->merge($data);
        $logo = $company->getLogoUrlAttribute();

        // Generate Hash Token for the current timestamp
        $token = md5(Carbon::now()->format('YmdHisu'));
        $apiKey = $request->input('api_key');

        $validateEmail =  $this->validateEmailExist($request);

        if(!$validateEmail['status']){
            return $this->sendError($validateEmail['message'],402);
        }

        $isExist = SupplierRegistrationLink::select('id', 'STATUS', 'token')
            ->where('company_id', $request->input('company_id'))
            ->where('email', $request->input('email'))
            ->where('registration_number', $request->input('registration_number'))
            ->orderBy("id", "desc")
            ->first();

        $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

        $file = array();

        $email = email::emailAddressFormat($request->input('email'));

        if (!empty($isExist)) {
            if($isExist['STATUS'] === 1){
                return $this->sendError('Supplier Registration Details Already Exist',402);
            } else if ($isExist['STATUS'] === 0){
                $loginUrl = env('SRM_LINK') . $isExist['token'] . '/' . $apiKey;
                $updateRec['token_expiry_date_time'] = Carbon::now()->addHours(96);
                $updateRec['sub_domain'] =  Helper::getDomainForSrmDocuments($request);
                $isUpdated = SupplierRegistrationLink::where('id', $isExist['id'])->update($updateRec);
                if ($isUpdated) {
                    Mail::to($email)->send(new EmailForQueuing("Registration Link", "Dear Supplier,"."<br /><br />"." Please find the below link to register at ". $companyName ." supplier portal. It will expire in 96 hours. "."<br /><br />"."Click Here: "."</b><a href='".$loginUrl."'>".$loginUrl."</a><br /><br />"." Thank You"."<br /><br /><b>",null, $file,"#C23C32","GEARS","$fromName"));
                    return $this->sendResponse($loginUrl, 'Supplier Registration Link Generated successfully');
                } else{
                    return $this->sendError('Supplier Registration Link Generation Failed',500);
                }
            }
        } else {
            $loginUrl = env('SRM_LINK').$token.'/'.$apiKey;
            $isCreated = $this->registrationLinkRepository->save($request, $token);
            if ($isCreated['status'] == true) {
                Mail::to($email)->send(new EmailForQueuing("Registration Link", "Dear Supplier,"."<br /><br />"." Please find the below link to register at ". $companyName ." supplier portal. It will expire in 96 hours. "."<br /><br />"."Click Here: "."</b><a href='".$loginUrl."'>".$loginUrl."</a><br /><br />"." Thank You"."<br /><br /><b>",null, $file,"#C23C32","GEARS","$fromName"));

                return $this->sendResponse($loginUrl, 'Supplier Registration Link Generated successfully');
            } else {
                return $this->sendError('Supplier Registration Link Generation Failed',500);
            }
        }
    }

    public function reSendSupplierRegistrationsLink(Request $request)
    {
        $companyName = "";
        $company = Company::find($request->companySystemId);
        if(isset($company->CompanyName)){
            $companyName =  $company->CompanyName;
        }
        $data['domain'] =  Helper::getDomainForSrmDocuments($request);
        $request->merge($data);
        $logo = $company->getLogoUrlAttribute();

        // Generate Hash Token for the current timestamp
        $token = md5(Carbon::now()->format('YmdHisu'));
        $apiKey = $request->input('api_key');

        $supplierdata = SupplierRegistrationLink::where('id',$request->id)
            ->where('company_id', $request->companySystemId)
            ->first();

        $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

        $file = array();

        $email = email::emailAddressFormat($supplierdata['email']);

        if (!empty($supplierdata)) {
            if ($supplierdata['STATUS'] === 0){
                $loginUrl = env('SRM_LINK') . $supplierdata['token'] . '/' . $apiKey;
                $updateRec['token_expiry_date_time'] = Carbon::now()->addHours(96);
                $isUpdated = SupplierRegistrationLink::where('id', $supplierdata['id'])->update($updateRec);
                if ($isUpdated) {
                    Mail::to($email)->send(new EmailForQueuing("Registration Link", "Dear Supplier,"."<br /><br />"." Please find the below link to register at ". $companyName ." supplier portal. It will expire in 96 hours. "."<br /><br />"."Click Here: "."</b><a href='".$loginUrl."'>".$loginUrl."</a><br /><br />"." Thank You"."<br /><br /><b>",null, $file,"#C23C32","GEARS","$fromName"));
                    return $this->sendResponse($loginUrl, 'Supplier Registration Link re-sent successfully');
                } else{
                    return $this->sendError(' Failed to re-send Supplier Registration Link',500);
                }
            }
        }
    }

    public function srmRegistrationLinkHistoryView(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $registrationLinkDetails = SupplierRegistrationLink::with(['supplier', 'created_by'])
            ->where('company_id',  $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $registrationLinkDetails = $registrationLinkDetails->where(function ($query) use ($search) {
                $query->where('registration_number', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");

                $query->orWhereHas('supplier', function ($query1) use ($search) {
                    $query1->where('supplierName', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('created_by', function ($query1) use ($search) {
                    $query1->where('empFullName', 'LIKE', "%{$search}%");
                });
            });
        }

        return \DataTables::of($registrationLinkDetails)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getSearchSupplierByCompanySRM(Request $request)
    {
        $isExist = SupplierRegistrationLink::select('supplier_master_id')->whereNotNull('supplier_master_id')->get()->pluck('supplier_master_id');    
        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $suppliers = SupplierAssigned::whereIn('companySystemID', $companies)
            ->select(['supplierCodeSytem', 'supplierName', 'primarySupplierCode'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                        ->orWhere('supplierName', 'LIKE', "%{$search}%");
                       
                });
            })
            ->whereNotIn('supplierCodeSytem', $isExist)
            ->take(20)
            ->get();


        return $this->sendResponse($suppliers->toArray(),'Data retrieved successfully');
    }

    public function validateSupplierAmend(Request $request)
    {
        $input = $request->all();

        $supplierMaster = SupplierMaster::find($input['supplierID']);

        if (!$supplierMaster) {
            return $this->sendError('Supplier Data not found');
        }

        $errorMessages = [];
        $successMessages = [];
        $amendable = [];

        $grvMaster = GRVMaster::where('supplierID', $input['supplierID'])->where('UnbilledGRVAccountSystemID', $supplierMaster->UnbilledGRVAccountSystemID)->first();

        if ($grvMaster) {
            $errorMessages[] = "Unbilled Account cannot be amended. Since, it has been used in good receipt voucher";
            $amendable['unbilledAmendable'] = false;
        } else {
            $successMessages[] = "Use of Unbilled Account checking is done in good receipt voucher";
            $amendable['unbilledAmendable'] = true;
        }

        $suppInvUn = BookInvSuppMaster::where('supplierID', $input['supplierID'])
                                      ->where('UnbilledGRVAccountSystemID', $supplierMaster->UnbilledGRVAccountSystemID)
                                      ->whereIn('documentType', [0,2])
                                      ->first();

        if ($suppInvUn) {
            $errorMessages[] = "Unbilled Account cannot be amended. Since, it has been used in supplier invoice";
            $amendable['unbilledAmendable'] = false;
        } else {
            $successMessages[] = "Use of Unbilled Account checking is done in supplier invoice";
            $amendable['unbilledAmendable'] = (!$amendable['unbilledAmendable']) ? false : true;
        }

        $suppInv = BookInvSuppMaster::where('supplierID', $input['supplierID'])->where('supplierGLCodeSystemID', $supplierMaster->liabilityAccountSysemID)->first();

        if ($suppInv) {
            $errorMessages[] = "Liability Account cannot be amended. Since, it has been used in supplier invoice";
            $amendable['liablityAmendable'] = false;
        } else {
            $successMessages[] = "Use of Liability Account checking is done in supplier invoice";
            $amendable['liablityAmendable'] = true;
        }

        $paySupp = PaySupplierInvoiceMaster::where('BPVsupplierID', $input['supplierID'])->where('advanceAccountSystemID', $supplierMaster->advanceAccountSystemID)->first();

        if ($paySupp) {
            $errorMessages[] = "Advance Account cannot be amended. Since, it has been used in payment voucher";
            $amendable['advanceAmendable'] = false;
        } else {
            $successMessages[] = "Use of Advance Account checking is done in payment voucher";
            $amendable['advanceAmendable'] = true;
        }

        return $this->sendResponse(['errorMessages' => $errorMessages, 'successMessages' => $successMessages, 'amendable'=> $amendable], "validated successfully");
    }

    public function updateSupplierBlocker(Request $request)
    {
        
        $input = $request->all();      
        $input = $this->convertArrayToSelectedValue($input, array('blockType'));
        $id = $input['id'];
        $isDelete = $input['isDelete'];
        $isEdit = $input['isEdit'];
        $input = array_except($input, ['isDelete', 'id','isEdit']);
        $PO = [];
        $SI = [];
        $PV= [];
        $GRV = [];
        if($isDelete)
        {
            $supplierBlock = $this->supplierBlockRepository->findWithoutFail($id);

            if (empty($supplierBlock)) {
                return $this->sendError('Supplier Block not found');
            }
            $supplierBlock->delete();
            return $this->sendResponse(true,'Supplier Block deleted successfully');

        }

        if($isEdit == 2)
        {
            $blockID = $input['blockId'];
            $updated['blockReason'] = $input['blockReason'];
            $supplierBlock = $this->supplierBlockRepository->update($updated, $blockID);
            return $this->sendResponse(true,'Supplier Block updated successfully');
        }

        $input['supplierCodeSytem'] = $id;
        $isPermenentExist = SupplierBlock::where('supplierCodeSytem',$id)->where('blockType',1)->first();

        if($isPermenentExist)
        {
            return $this->sendError('you cant add permenent type !already have a perment type',422);
        }
        
        $isPeriodExist = SupplierBlock::where('supplierCodeSytem',$id)->where('blockType',2);
        if($input['blockType'] == 2)
        {

            $from =  Carbon::parse($input['blockFrom'])->format('Y-m-d');
            $to =  Carbon::parse($input['blockTo'])->format('Y-m-d');
            
            $input['blockFrom'] = $from;
            $input['blockTo'] = $to;
            
            if(($isPeriodExist->count()) > 0)
            {
                $overlapRecord = $isPeriodExist->where(function ($query) use ($from, $to) {
                    $query->where('blockFrom', '>=', $from)
                        ->where('blockFrom', '<=', $to)
                        ->orWhere(function ($query) use ($from, $to) {
                            $query->where('blockTo', '>=', $from)
                                ->where('blockTo', '<=', $to);
                        })
                        ->orWhere(function ($query) use ($from, $to) {
                            $query->where('blockFrom', '<=', $from)
                                ->where('blockTo', '>=', $to);
                        });
                })
                ->exists();

                if($overlapRecord)
                {
                    return $this->sendError('The selected period already has a block',422);
                }
            }


        }


        $purchaseOrder = ProcumentOrder::where('supplierID',$id)->where('approved',0)->where('poTypeID',2);
        if($purchaseOrder->count() > 0)
        {
           $PO =  $purchaseOrder->pluck('purchaseOrderCode')->toArray();
        }

        $Invoice = BookInvSuppMaster::where('supplierID',$id)->where('approved',0)
                            ->where(function($q)
                        {
                            $q->where('documentType',1)->orWhere('documentType',3);
                        });
        if($Invoice->count() > 0)
        {
           $SI =  $Invoice->pluck('bookingInvCode')->toArray();
        }


        $paymountVOcuher = PaySupplierInvoiceMaster::where('BPVsupplierID',$id)->where('approved',0)
        ->where(function($q)
            {
                $q->where('invoiceType',5)->orWhere('invoiceType',3);
            });

        if($paymountVOcuher->count() > 0)
        {
            $PV =  $paymountVOcuher->pluck('BPVcode')->toArray();
        }


        $grv = GRVMaster::where('supplierID',$id)->where('approved',0)->where('grvTypeID',1);

        if($grv->count() > 0)
        {
            $GRV =  $grv->pluck('grvPrimaryCode')->toArray();
        }
    
    
        if($isEdit == 1)
        {
            $mergedArray = array_merge($PO, $SI, $PV,$GRV);
            if(count($mergedArray) > 0)
            {
                return $this->sendError('The supplier cannot be blocked as the supplier selected in the following documents.',500,['type' => 'blockSupplier','data' =>$mergedArray]);
    
            }
        }


        if($isDelete == false)
        {
            $validator = \Validator::make($request->all(), [
                'blockType' => 'required',
                'blockFrom' => 'required_if:blockType,2|nullable|date',
                'blockReason' => 'required',
                'blockTo' => 'required_if:blockType,2|nullable|date|after_or_equal:blockFrom',
            ],[ 'blockTo.required_if' => 'From Date must be grater than less than or equal to TO date' ]);
    
            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422, ['type' => 'validation']);
            }
        }

         $supplierBlock = $this->supplierBlockRepository->create($input);
         return $this->sendResponse($supplierBlock,'Data updated successfully');

    }

    public function validateSupplier(Request $request)
    {
        $input = $request->all();
        $supplier_id = $input['supplierId'];
        $supplierMaster = $this->supplierMasterRepository->findWithoutFail($supplier_id);

        $date = isset($input['date'])?$input['date']:null;
        $validatorResult = \Helper::checkBlockSuppliers($date,$supplier_id);
        if (!$validatorResult['success']) {
            return $this->sendError($validatorResult['message']);
        }
     
        return $this->sendResponse(true,$validatorResult['message']);

    }

    public function validateEmailExist($request)
    {
        $email = $request->input('email');
        $regNo = $request->input('registration_number');
        $companyId = $request->input('company_id');

        $supplierRegLink = SupplierRegistrationLink::select('id','email','registration_number')
            ->where('company_id',$companyId)
            ->where('STATUS',1)
            ->get();

        $emails = $supplierRegLink->pluck('email')->toArray();
        $registrationNumbers = $supplierRegLink->pluck('registration_number')->toArray();

        if (in_array($email, $emails)) {
            return ['status' => false, 'message' => 'Email already exists'];
        }

        if (in_array($regNo, $registrationNumbers)) {
            return ['status' => false, 'message' => 'Registration number already exists'];
        }

        return ['status' => true, 'message' => 'Success'];
    }


    public function getSupplierBlock(Request $request)
    {
        $supplierId = $request['supplierId'];
        $data['supplierBlocks'] = $this->supplierBlockRepository->where('supplierCodeSytem',$supplierId)->get();
        $data['isPermentExists'] = $this->supplierBlockRepository->where('supplierCodeSytem',$supplierId)->where('blockType',1)->exists();

        return $this->sendResponse($data, 'Supplier Category Subs retrieved successfully');
    }
}
