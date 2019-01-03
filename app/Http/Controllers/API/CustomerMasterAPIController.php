<?php
/**
 * =============================================
 * -- File Name : CustomerMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Master
 * -- Author : Mohamed Fayas
 * -- Create date : 19 - March 2018
 * -- Description : This file contains the all CRUD for Customer Master
 * -- REVISION HISTORY
 * -- Date: 19-March 2018 By: Fayas Description: Added new functions named as getAllCustomers()
 * -- Date: 20-March 2018 By: Fayas Description: Added new functions named as getCustomerFormData(),getAssignedCompaniesByCustomer()
 * -- Date: 21-June 2018 By: Fayas Description: Added new functions named as getSearchCustomerByCompany()
 * -- Date: 13-August 2018 By: Fayas Description: Added new functions named as getContractByCustomer()
 * -- Date: 19-November 2018 By: Fayas Description: Added new functions named as getJobsByContractAndCustomer()
 * -- Date: 18-December 2018 By: Fayas Description: Added new functions named as customerReferBack()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerMasterAPIRequest;
use App\Http\Requests\API\UpdateCustomerMasterAPIRequest;
use App\Models\Contract;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CustomerMasterRefferedBack;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\TicketMaster;
use App\Models\YesNoSelection;
use App\Models\CustomerAssigned;
use App\Models\ChartOfAccount;
use App\Repositories\CustomerMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CustomerMasterController
 * @package App\Http\Controllers\API
 */
class CustomerMasterAPIController extends AppBaseController
{
    /** @var  CustomerMasterRepository */
    private $customerMasterRepository;
    private $userRepository;

    public function __construct(CustomerMasterRepository $customerMasterRepo, UserRepository $userRepo)
    {
        $this->customerMasterRepository = $customerMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the CustomerMaster.
     * GET|HEAD /customerMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->customerMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerMasters = $this->customerMasterRepository->all();

        return $this->sendResponse($customerMasters->toArray(), 'Customer Masters retrieved successfully');
    }


    /**
     * Display a listing of the CustomerMaster.
     * GET|HEAD /getAllCustomers
     *
     * @param Request $request
     * @return Response
     */
    public function getAllCustomers(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }
        $customerMasters = CustomerMaster::with(['country'])
            //with(['categoryMaster', 'employee', 'supplierCurrency'])
            //->whereIn('primaryCompanySystemID',$childCompanies)
            ->select('customermaster.*');

        $search = $request->input('search.value');
        if ($search) {
            $customerMasters = $customerMasters->where(function ($query) use ($search) {
                $query->where('CutomerCode', 'LIKE', "%{$search}%")
                    ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($customerMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('customerCodeSystem', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * get supplier master approval by company.
     * GET|HEAD /getAllCustomerMasterApproval
     *
     * @param Request $request
     * @return Response
     */
    public function getAllCustomerMasterApproval(Request $request)
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

        $customerMasters = DB::table('erp_documentapproved')->select('customermaster.*', 'countrymaster.countryName', 'erp_documentapproved.documentApprovedID', 'rollLevelOrder', 'approvalLevelID', 'documentSystemCode')->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID', 58)
                ->whereIn('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('customermaster', function ($query) use ($companyID, $empID, $search) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'customerCodeSystem')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->whereIn('primaryCompanySystemID', $companyID)
                ->where('customermaster.approvedYN', 0)
                ->where('customermaster.confirmedYN', 1)
                ->when($search != "", function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('CutomerCode', 'LIKE', "%{$search}%")
                            ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                            ->orWhere('CustomerName', 'LIKE', "%{$search}%");
                    });
                });
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('countrymaster', 'customerCountry', '=', 'countryID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 58)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        return \DataTables::of($customerMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    /**
     * get form data for Customer Master.
     * GET /getCustomerFormData
     *
     * @param Request $request
     * @return Response
     */

    public function getCustomerFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];

        $masterCompany = Company::where("companySystemID", $selectedCompanyId)->first();

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            //$subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            $subCompanies = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
            /**  Companies by group  Drop Down */
            $allCompanies = Company::whereIn("companySystemID", $subCompanies)->get();
        } else {
            $allCompanies = Company::where("companySystemID", $selectedCompanyId)->get();
        }

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /**Chart of Account Drop Down */
        $chartOfAccounts = ChartOfAccount::where('controllAccountYN', '=', 1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')->get();

        $output = array(
            'allCompanies' => $allCompanies,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccounts' => $chartOfAccounts,
            'country' => $country
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * get all Assigned Companies for Customer
     * GET /getAssignedCompaniesByCustomer
     *
     * @param Request $request
     * @return Response
     */
    public function getAssignedCompaniesByCustomer(Request $request)
    {

        $customerId = $request['customerId'];
        $customer = CustomerMaster::where('customerCodeSystem', '=', $customerId)->first();
        if ($customer) {
            $customerCompanies = CustomerAssigned::where('customerCodeSystem', $customerId)
                ->with(['company'])
                ->orderBy('customerAssignedID', 'DESC')
                ->get();
        } else {
            $customerCompanies = [];
        }

        return $this->sendResponse($customerCompanies, 'customer companies retrieved successfully');
    }

    /**
     * Store a newly created CustomerMaster in storage.
     * POST /customerMasters
     *
     * @param CreateCustomerMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerMasterAPIRequest $request)
    {

        $input = $request->all();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = $input[$key][0];
                } else {
                    $input[$key] = 0;
                }
            }
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }


        if (array_key_exists('custGLAccountSystemID', $input)) {
            $financePL = ChartOfAccount::where('chartOfAccountSystemID', $input['custGLAccountSystemID'])->first();
            if ($financePL) {
                $input['custGLaccount'] = $financePL->AccountCode;
            }
        }


        if (array_key_exists('customerCodeSystem', $input)) {

            $customerMasters = CustomerMaster::where('customerCodeSystem', $input['customerCodeSystem'])->first();

            if (empty($customerMasters)) {
                return $this->sendError('customer not found');
            }

            if ($customerMasters->confirmedYN == 0 && $input['confirmedYN'] == 1) {
                $params = array('autoID' => $input['customerCodeSystem'], 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500);
                }
            }

            foreach ($input as $key => $value) {
                $customerMasters->$key = $value;
            }

            $customerMasters->modifiedPc = gethostname();
            $customerMasters->modifiedUser = $empId;
            $customerMasters->save();
        } else {

            $document = DocumentMaster::where('documentID', 'CUSTM')->first();
            $input['documentSystemID'] = $document->documentSystemID;
            $input['documentID'] = $document->documentID;

            $lastCustomer = CustomerMaster::orderBy('customerCodeSystem', 'DESC')->first();
            $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
            $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);

            $input['lastSerialOrder'] = $lastSerialOrder;
            $input['CutomerCode'] = $customerCode;
            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = $empId;
            $input['isCustomerActive'] = 1;
            $customerMasters = $this->customerMasterRepository->create($input);
        }

        return $this->sendResponse($customerMasters->toArray(), 'Customer Master saved successfully');
    }


    /**
     * Display the specified CustomerMaster.
     * GET|HEAD /customerMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CustomerMaster $customerMaster */
        $customerMaster = $this->customerMasterRepository->with(['finalApprovedBy'])->findWithoutFail($id);
        // $customerMasters = CustomerMaster::where('customerCodeSystem', $id)->first();
        if (empty($customerMaster)) {
            return $this->sendError('Customer Master not found');
        }

        return $this->sendResponse($customerMaster->toArray(), 'Customer Master retrieved successfully');
    }

    /**
     * Update the specified CustomerMaster in storage.
     * PUT/PATCH /customerMasters/{id}
     *
     * @param  int $id
     * @param UpdateCustomerMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = array_except($input, ['final_approved_by']);

        /** @var CustomerMaster $customerMaster */
        $customerMaster = $this->customerMasterRepository->findWithoutFail($id);

        if (empty($customerMaster)) {
            return $this->sendError('Customer Master not found');
        }

        $customerMaster = $this->customerMasterRepository->update($input, $id);

        return $this->sendResponse($customerMaster->toArray(), 'CustomerMaster updated successfully');
    }

    /**
     * Remove the specified CustomerMaster from storage.
     * DELETE /customerMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CustomerMaster $customerMaster */
        $customerMaster = $this->customerMasterRepository->findWithoutFail($id);

        if (empty($customerMaster)) {
            return $this->sendError('Customer Master not found');
        }

        $customerMaster->delete();

        return $this->sendResponse($id, 'Customer Master deleted successfully');
    }

    public function approveCustomer(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectCustomer(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    /**
     *  Search Customer By Company
     * GET /getSearchCustomerByCompany
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getSearchCustomerByCompany(Request $request)
    {

        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $customers = CustomerAssigned::whereIn('companySystemID', $companies)
            ->select(['customerCodeSystem', 'CustomerName', 'CutomerCode'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('CutomerCode', 'LIKE', "%{$search}%")
                        ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                        ->orWhere('CustomerName', 'LIKE', "%{$search}%");
                });
            })
            //->take(20)
            ->get();


        return $this->sendResponse($customers->toArray(), 'Customer Master retrieved successfully');
    }

    public function getJobsByContractAndCustomer(Request $request)
    {
        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $jobs = TicketMaster::whereIn('companySystemID', $companies)
            ->where('clientSystemID', $input['customer_id'])
            ->where('contractUID', $input['contractUIID'])
            ->where('jobStartedYNBM', 1)
            ->where('jobEndYNSup', '!=', 1)
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('ticketNo', 'LIKE', "%{$search}%");
                });
            })
            ->get(['ticketidAtuto', 'ticketNo']);


        return $this->sendResponse($jobs->toArray(), 'Jobs by Customer retrieved successfully');
    }

    public function getContractByCustomer(Request $request)
    {

        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $contract = Contract::whereIn('companySystemID', $companies)
            ->where('clientID', $input['customer_id'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('ContractNumber', 'LIKE', "%{$search}%");
                });
            })
            ->get(['ContractNumber', 'contractUID']);


        return $this->sendResponse($contract->toArray(), 'Contracts by Customer retrieved successfully');
    }

    public function getCustomerByCompany(Request $request)
    {

        $companySystemID = $request['companySystemID'];

        $customerCompanies = CustomerAssigned::where('companySystemID', $companySystemID)
            ->with(['company', 'customer_master' => function ($query) {
                $query->select('customerCodeSystem', 'companyLinkedToSystemID');
            }])
            ->orderBy('customerAssignedID', 'DESC')
            ->get();


        return $this->sendResponse($customerCompanies->toArray(), 'customer companies retrieved successfully');
    }

    public function customerReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $customer = $this->customerMasterRepository->find($id);
        if (empty($customer)) {
            return $this->sendError('Customer Master not found');
        }

        if ($customer->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this customer');
        }

        $customerArray = $customer->toArray();

        $storeHistory = CustomerMasterRefferedBack::insert($customerArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $customer->primaryCompanySystemID)
            ->where('documentSystemID', $customer->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $customer->timesReferred;
            }
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $customer->primaryCompanySystemID)
            ->where('documentSystemID', $customer->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,
                'confirmedYN' => 0,
                'confirmedEmpSystemID' => null,
                'confirmedEmpID' => null,
                'confirmedEmpName' => null,
                'confirmedDate' => null,
                'RollLevForApp_curr' => 1];

            $this->customerMasterRepository->update($updateArray, $id);
        }

        return $this->sendResponse($customer->toArray(), 'Customer Master Amend successfully');
    }

    public function exportCustomerMaster(Request $request)
    {
        $input = $request->all();
        $type = $input['type'];

        $customerMasters = CustomerMaster::with(['country'])
            ->get();

        if ($customerMasters) {
            $x = 0;
            $data = array();
            foreach ($customerMasters as $val) {
                $data[$x]['Primary Code'] = $val->CutomerCode;
                $data[$x]['Secondary Code'] = $val->customerShortCode;
                $data[$x]['Customer Name'] = $val->CustomerName;
                $data[$x]['City'] = $val->customerCity;
                $data[$x]['Country'] = $val->CustomerName;
                if ($val->country) {
                    $data[$x]['Country'] = $val->country->countryName;
                } else {
                    $data[$x]['Country'] = '';
                }
                $data[$x]['Credit Period'] = $val->creditDays;
                $data[$x]['Credit Limit'] = $val->creditLimit;
                $x++;
            }
        } else {
            $data = array();
        }

        $csv = \Excel::create('customer_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }
}
