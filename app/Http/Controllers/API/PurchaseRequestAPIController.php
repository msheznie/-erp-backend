<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Request
 * -- Author : Mohamed Fayas
 * -- Create date : 26 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Request
 * -- REVISION HISTORY
 * -- Date: 26-March 2018 By: Fayas Description: Added new functions named as getPurchaseRequestByDocumentType()
 * -- Date: 27-March 2018 By: Fayas Description: Added new functions named as getPurchaseRequestFormData()
 * -- Date: 11-April 2018 By: Fayas Description: Added new functions named as reportPrToGrv()
 * -- Date: 17-April 2018 By: Fayas Description: Added new functions named as reportPrToGrvFilterOptions()
 * -- Date: 18-April 2018 By: Fayas Description: Added new functions named as getApprovedDetails()
 * -- Date: 20-April 2018 By: Fayas Description: Added new functions named as getPurchaseRequestApprovalByUser()
 * -- Date: 23-April 2018 By: Fayas Description: Added new functions named as approvePurchaseRequest(),rejectPurchaseRequest
 * -- Date: 26-April 2018 By: Fayas Description: Added new functions named as cancelPurchaseRequest(),returnPurchaseRequest
 * -- Date: 04-May 2018 By: Fayas Description: Added new functions named as manualClosePurchaseRequest()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreatePurchaseRequestAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\FinanceItemCategoryMaster;
use App\Models\ItemAssigned;
use App\Models\Location;
use App\Models\Months;
use App\Models\Priority;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\ProcumentOrder;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Class PurchaseRequestController
 * @package App\Http\Controllers\API
 */
class PurchaseRequestAPIController extends AppBaseController
{
    /** @var  PurchaseRequestRepository */
    private $purchaseRequestRepository;
    private $userRepository;

    public function __construct(PurchaseRequestRepository $purchaseRequestRepo, UserRepository $userRepo)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the PurchaseRequest.
     * GET|HEAD /purchaseRequests
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseRequests = $this->purchaseRequestRepository->all();

        return $this->sendResponse($purchaseRequests->toArray(), 'Purchase Requests retrieved successfully');
    }

    /**
     * get Items Option For PurchaseRequest
     * get /getItemsOptionForPurchaseRequest
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getItemsOptionForPurchaseRequest(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];
        $purchaseRequestId = $input['purchaseRequestId'];

        $policy = 1;

        $financeCategoryId = 0;

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;

            if ($policy == 0) {
                $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $purchaseRequestId)->first();

                if ($purchaseRequest) {
                    $financeCategoryId = $purchaseRequest->financeCategory;
                }
            }
        }

        $items = ItemAssigned::where('companySystemID', $companyId);


        if ($policy == 0 && $financeCategoryId != 0) {
            $items = $items->where('financeCategoryMaster', $financeCategoryId);
        }

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }


        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }


    /**
     * get Purchase Request Form Data
     * get /getPurchaseRequestFormData
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getPurchaseRequestFormData(Request $request)
    {

        $input = $request->all();
        $companyId = $input['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);

        if (array_key_exists('isCreate', $input)) {
            if($input['isCreate'] == 1){
                $segments =  $segments->where('isActive',1);
            }
        }

        $segments =  $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();


        $years = PurchaseRequest::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $currencies = CurrencyMaster::all();

        $financeCategories = FinanceItemCategoryMaster::all();

        $locations = Location::all();

        $priorities = Priority::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));


        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0);

        if ($checkBudget) {
            $conditions['checkBudget'] = $checkBudget->isYesNO;
        }

        if ($allowFinanceCategory) {
            $conditions['allowFinanceCategory'] = $allowFinanceCategory->isYesNO;
        }


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'financeCategories' => $financeCategories,
            'locations' => $locations,
            'priorities' => $priorities,
            'financialYears' => $financialYears,
            'conditions' => $conditions
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * report for Pr To Grv
     * get /reportPrToGrv
     *
     * @param Request $request
     *
     * @return Response
     */

    public function reportPrToGrv(Request $request)
    {
        $input = $request->all();

        $itemPrimaryCodes = [];
        $from = "";
        $to = "";
        $documentSearch = "";
        $years = [];

        if (array_key_exists('itemPrimaryCodes', $input)) {
            $itemPrimaryCodes = $input['itemPrimaryCodes'];
        }

        if (array_key_exists('dateRange', $input)) {
            $from = ((new Carbon($input['dateRange'][0]))->addDays(1)->format('Y-m-d'));
            $to = ((new Carbon($input['dateRange'][1]))->addDays(1)->format('Y-m-d'));
        }

        if (array_key_exists('documentSearch', $input)) {
            $documentSearch = str_replace("\\", "\\\\", $input['documentSearch']);
        }

        if (array_key_exists('years', $input)) {
            $years = $input['years'];
        }

        $purchaseRequests = PurchaseRequest::where('companySystemID', $input['companyId'])
            ->where('PRConfirmedYN', 1)
            ->where('cancelledYN', 0)
            ->when(request('date_by') == 'PRRequestedDate', function ($q) use ($from, $to) {
                return $q->whereBetween('PRRequestedDate', [$from, $to]);
            })
            ->when(request('documentId') == 1, function ($q) use ($documentSearch) {
                $q->where('purchaseRequestCode', 'LIKE', "%{$documentSearch}%");
            })
            ->when(request('date_by') == 'all' && count($years) > 0, function ($q) use ($years) {
                $q->whereIn(DB::raw("YEAR(PRRequestedDate)"), $years);
            })
            ->whereHas('details', function ($prd) use ($itemPrimaryCodes, $from, $to, $documentSearch) {
                $prd->with(['podetail' => function ($pod) use ($from, $to, $documentSearch) {
                    $pod->whereHas('order', function ($po) use ($from, $to, $documentSearch) {
                        $po->where('poConfirmedYN', 1)
                            ->when(request('date_by') == 'approvedDate', function ($q) use ($from, $to) {
                                return $q->whereBetween('approvedDate', [$from, $to]);
                            })
                            ->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                                $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                            });
                    })->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                        return $q->whereHas('grv_details', function ($q) use ($from, $to) {
                            $q->whereHas('grv_master', function ($q) use ($from, $to) {
                                $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                    return $q->whereBetween('grvDate', [$from, $to]);
                                });
                            });
                        });
                    })
                        ->when(request('grv') == 'inComplete', function ($q) {
                            $q->whereIn('goodsRecievedYN', [0, 1]);
                        });
                }])->when(request('itemPrimaryCodes', false), function ($q, $itemPrimaryCodes) {
                    return $q->whereIn('itemCode', $itemPrimaryCodes);
                });
            })
            ->with(['confirmed_by', 'details' => function ($prd) use ($itemPrimaryCodes, $from, $to, $documentSearch) {
                $prd->with(['uom', 'podetail' => function ($q) use ($from, $to, $documentSearch) {
                    $q->with(['order' => function ($q) use ($from, $to, $documentSearch) {
                        $q->when(request('date_by') == 'approvedDate', function ($q) use ($from, $to) {
                            return $q->whereBetween('approvedDate', [$from, $to]);
                        })->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                            $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                        });
                    }, 'reporting_currency', 'grv_details' => function ($q) use ($from, $to) {
                        $q->with(['grv_master' => function ($q) use ($from, $to) {
                            $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                return $q->whereBetween('grvDate', [$from, $to]);
                            });
                        }]);
                    }])
                        ->when(request('grv') == 'inComplete', function ($q) {
                            $q->whereIn('goodsRecievedYN', [0, 1]);
                        });
                }])
                    ->when(request('itemPrimaryCodes', false), function ($q, $itemPrimaryCodes) {
                        return $q->whereIn('itemCode', $itemPrimaryCodes);
                    });
            }]);


        return \DataTables::of($purchaseRequests)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseRequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);

        return $this->sendResponse($purchaseRequests, 'Record retrieved successfully');
    }

    /**
     * get Approval Details
     * GET /getApprovedDetails
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getApprovedDetails(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $documentSystemCode = $input['documentSystemCode'];
        $documentSystemID = $input['documentSystemID'];

        $approveDetails = DocumentApproved::where('documentSystemID', $documentSystemID)
            ->where('documentSystemCode', $documentSystemCode)
            ->where('companySystemID', $companySystemID)
            ->with(['approved_by'])
            ->get();

        foreach ($approveDetails as $value) {

            if ($value['approvedYN'] == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return $this->sendError('Policy not found');
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $value['approvalGroupID'])
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID);
                //->get();

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $value['serviceLineSystemID']);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();
                $value['approval_list'] = $approvalList;
            }
        }

        return $this->sendResponse($approveDetails, 'Record retrieved successfully');
    }

    /**
     * get filter options for Pr To Grv report
     * GET /reportPrToGrvFilterOptions
     *
     * @param Request $request
     *
     * @return Response
     */
    public function reportPrToGrvFilterOptions(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $items = ItemAssigned::where('companySystemID', $companyId);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(15)->get();


        $years = PurchaseRequest::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get(['year']);

        $output = array('items' => $items,
            'years' => $years);


        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * get Purchase Request By Document Type.
     * POST /getPurchaseRequestByDocumentType
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getPurchaseRequestByDocumentType(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $purchaseRequests = PurchaseRequest::where('companySystemID', $input['companyId']);


        if (array_key_exists('requestReview', $input)) {
            if ($input['requestReview'] == 1) {
                $purchaseRequests->where('cancelledYN', 0);
                //->where('approved', -1);
            }
        } else {
            $purchaseRequests = $purchaseRequests->where('documentSystemID', $input['documentId']);
        }

        $purchaseRequests = $purchaseRequests->with(['created_by' => function ($query) {
        }, 'priority' => function ($query) {

        }, 'location' => function ($query) {

        }, 'segment' => function ($query) {

        }, 'financeCategory' => function ($query) {

        }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            $purchaseRequests->where('serviceLineSystemID', $input['serviceLineSystemID']);
        }

        if (array_key_exists('cancelledYN', $input)) {
            if ($input['cancelledYN'] == 0 || $input['cancelledYN'] == -1) {
                $purchaseRequests->where('cancelledYN', $input['cancelledYN']);
            }
        }

        if (array_key_exists('PRConfirmedYN', $input)) {
            if ($input['PRConfirmedYN'] == 0 || $input['PRConfirmedYN'] == 1) {
                $purchaseRequests->where('PRConfirmedYN', $input['PRConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if ($input['approved'] == 0 || $input['approved'] == -1) {
                $purchaseRequests->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            $purchaseRequests->whereMonth('createdDateTime', '=', $input['month']);
        }

        if (array_key_exists('year', $input)) {
            $purchaseRequests->whereYear('createdDateTime', '=', $input['year']);
        }

        $purchaseRequests = $purchaseRequests->select(
            ['erp_purchaserequest.purchaseRequestID',
                'erp_purchaserequest.purchaseRequestCode',
                'erp_purchaserequest.createdDateTime',
                'erp_purchaserequest.createdUserSystemID',
                'erp_purchaserequest.comments',
                'erp_purchaserequest.location',
                'erp_purchaserequest.priority',
                'erp_purchaserequest.cancelledYN',
                'erp_purchaserequest.PRConfirmedYN',
                'erp_purchaserequest.approved',
                'erp_purchaserequest.timesReferred',
                'erp_purchaserequest.serviceLineSystemID',
                'erp_purchaserequest.financeCategory',
                'erp_purchaserequest.documentSystemID',
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                ->orWhere('comments', 'LIKE', "%{$search}%");
        }

        return \DataTables::eloquent($purchaseRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseRequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Purchase Request Approval By User.
     * POST /getPurchaseRequestApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getPurchaseRequestApprovalByUser(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();


        $purchaseRequests = DB::table('erp_documentapproved')
            ->select(
                'erp_purchaserequest.*',
                'employees.empName As created_emp',
                'financeitemcategorymaster.categoryDescription As financeCategoryDescription',
                'serviceline.ServiceLineDes As PRServiceLineDes',
                'erp_location.locationName As PRLocationName',
                'erp_priority.priorityDescription As PRPriorityDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 1)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [1, 50, 51])
                    ->where('employeesdepartments.departmentSystemID', 3)
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_purchaserequest', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'purchaseRequestID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_purchaserequest.companySystemID', $companyId)
                    ->where('erp_purchaserequest.approved', 0)
                    ->where('erp_purchaserequest.PRConfirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('financeitemcategorymaster', 'financeCategory', 'financeitemcategorymaster.itemCategoryID')
            ->join('erp_priority', 'priority', 'erp_priority.priorityID')
            ->join('erp_location', 'location', 'erp_location.locationID')
            ->join('serviceline', 'erp_purchaserequest.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [1, 50, 51])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                                                 ->orWhere('comments', 'LIKE', "%{$search}%");
        }

        return \DataTables::of($purchaseRequests)
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

    public function getPurchaseRequestForPO(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyId'];
        $purchaseOrderID = $input['purchaseOrderID'];

        $procumentOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $documentSystemID = $procumentOrder->documentSystemID;
        if($documentSystemID == 2){
            $documentSystemIDChanged = 1 ;
        }else if($documentSystemID == 5){
            $documentSystemIDChanged = 50 ;
        }else if($documentSystemID == 52){
            $documentSystemIDChanged = 51 ;
        }

        $purchaseRequests = PurchaseRequest::where('companySystemID', $companyID)
            ->where('approved', -1)
            ->where('PRConfirmedYN', 1)
            ->where('prClosedYN', 0)
            ->where('cancelledYN', 0)
            ->where('selectedForPO', 0)
            ->where('supplyChainOnGoing', 0)
            ->where('documentSystemID', $documentSystemIDChanged);
        if (isset($procumentOrder->financeCategory)) {
            $purchaseRequests = $purchaseRequests->where('financeCategory', $procumentOrder->financeCategory);
        }
        $purchaseRequests = $purchaseRequests->where('serviceLineSystemID', $procumentOrder->serviceLineSystemID)
            ->orderBy('purchaseRequestID', 'DESC')
            ->get();

        return $this->sendResponse($purchaseRequests->toArray(), 'Purchase Request Details retrieved successfully');
    }

    /**
     * Store a newly created PurchaseRequest in storage.
     * POST /purchaseRequests
     *
     * @param CreatePurchaseRequestAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestAPIRequest $request)
    {

        $input = $this->convertArrayToValue($request->all());

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $input['PRRequestedDate'] = now();

        $input['departmentID'] = 'PROC';

        $lastSerial = PurchaseRequest::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->orderBy('purchaseRequestID', 'desc')
            ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $input['serialNumber'] = $lastSerialNumber;


        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($document) {
            $input['documentID'] = $document->documentID;
        }

        $companyDocumentAttachment = CompanyDocumentAttachment::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->first();

        if ($companyDocumentAttachment) {
            $input['docRefNo'] = $companyDocumentAttachment->docRefNumber;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
        $input['purchaseRequestCode'] = $input['companyID'] . '\\' . $input['departmentID'] . '\\' . $input['serviceLineCode'] . '\\' . $input['documentID'] . $code;

        $purchaseRequests = $this->purchaseRequestRepository->create($input);

        return $this->sendResponse($purchaseRequests->toArray(), 'Purchase Request saved successfully');
    }

    /**
     * Display the specified PurchaseRequest.
     * GET|HEAD /purchaseRequests/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by',
            'priority', 'location', 'details.uom', 'company', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->whereIn('documentSystemID', [1, 50, 51]);
            }
        ])->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request retrieved successfully');
    }

    /**
     * Update the specified PurchaseRequest in storage.
     * PUT/PATCH /purchaseRequests/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseRequestAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestAPIRequest $request)
    {

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmed_by',
            'priority', 'location', 'details', 'company', 'approved_by',
            'PRConfirmedBy', 'PRConfirmedByEmpName',
            'PRConfirmedBySystemID', 'PRConfirmedDate']);
        $input = $this->convertArrayToValue($input);

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        $input['modifiedUserSystemID'] = $user->employee['employeeSystemID'];

        if ($purchaseRequest->PRConfirmedYN == 0 && $input['PRConfirmedYN'] == 1) {


            $checkItems = PurchaseRequestDetails::where('purchaseRequestID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every request should have at least one item', 500);
            }

            $checkQuantity = PurchaseRequestDetails::where('purchaseRequestID', $id)
                ->where('quantityRequested', '<', 1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }


            $amount = PurchaseRequestDetails::where('purchaseRequestID', $id)
                ->sum('totalCost');


            /*$currencyConversion = \Helper::currencyConversion($item->companySystemID,
                                                              $item->wacValueLocalCurrencyID,
                                                               $purchaseRequest->currency,
                                                              $amount);

            $convertedAmount = $currencyConversion['documentAmount'];*/

            $params = array('autoID' => $id,
                'company' => $purchaseRequest->companySystemID,
                'document' => $purchaseRequest->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => $input['financeCategory'],
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }

            /*$input['PRConfirmedBy'] = $user->employee['empID'];;
            $input['PRConfirmedBySystemID'] = $user->employee['employeeSystemID'];
            $input['PRConfirmedDate'] = now();*/
        }

        $purchaseRequest = $this->purchaseRequestRepository->update($input, $id);

        return $this->sendResponse($purchaseRequest->toArray(), 'PurchaseRequest updated successfully');
    }

    /**
     * Remove the specified PurchaseRequest from storage.
     * DELETE /purchaseRequests/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $purchaseRequest->delete();

        return $this->sendResponse($id, 'Purchase Request deleted successfully');
    }

    /**
     * Approve Purchase Request.
     * POST /approvePurchaseRequest
     *
     * @param  $request
     *
     * @return Response
     */
    public function approvePurchaseRequest(Request $request)
    {

        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    /**
     * Reject Purchase Request
     * Post /rejectPurchaseRequest
     *
     * @param $request
     *
     * @return Response
     */
    public function rejectPurchaseRequest(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }


    /**
     * Cancel Purchase Request
     * Post /cancelPurchaseRequest
     *
     * @param $request
     *
     * @return Response
     */
    public function cancelPurchaseRequest(Request $request)
    {

        $input = $request->all();
        $purchaseRequest = PurchaseRequest::find($input['purchaseRequestID']);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $checkPo = PurchaseOrderDetails::where('purchaseRequestID', $input['purchaseRequestID'])->count();

        if ($checkPo > 0) {
            return $this->sendError('Cannot cancel. Order is created for this request');
        }

        $employee = \Helper::getEmployeeInfo();

        $purchaseRequest->cancelledYN = -1;
        $purchaseRequest->cancelledByEmpSystemID = $employee->employeeSystemID;
        $purchaseRequest->cancelledByEmpID = $employee->empID;
        $purchaseRequest->cancelledByEmpName = $employee->empName;
        $purchaseRequest->cancelledComments = $input['cancelledComments'];
        $purchaseRequest->cancelledDate = now();
        $purchaseRequest->save();

        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseRequest->purchaseRequestCode;

        $body = '<p>' . $cancelDocNameBody . ' is cancelled due to below reason.</p><p>Comment : ' . $input['cancelledComments'] . '</p>';
        $subject = $cancelDocNameSubject . ' is cancelled';

        if ($purchaseRequest->PRConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseRequest->PRConfirmedBySystemID,
                'companySystemID' => $purchaseRequest->companySystemID,
                'docSystemID' => $purchaseRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseRequest->purchaseRequestID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
                                                ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
                                                ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                                ->where('approvedYN', -1)
                                                ->get();

        foreach ($documentApproval as $da) {
            $emails[] = array('empSystemID' => $da->employeeSystemID,
                'companySystemID' => $purchaseRequest->companySystemID,
                'docSystemID' => $purchaseRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseRequest->purchaseRequestID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"],500);
        }

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully canceled');

    }

    /**
     * Return to amend Purchase Request
     * Post /returnPurchaseRequest
     *
     * @param $request
     *
     * @return Response
     */
    public function returnPurchaseRequest(Request $request)
    {

        $input = $request->all();
        $purchaseRequest = PurchaseRequest::with(['confirmed_by'])->find($input['purchaseRequestID']);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $checkPo = PurchaseOrderDetails::where('purchaseRequestID', $input['purchaseRequestID'])->count();

        if ($checkPo > 0) {
            return $this->sendError('Cannot cancel. Order is created for this request');
        }

        $employee = \Helper::getEmployeeInfo();

        $emails = array();
        $ids_to_delete = array();

        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseRequest->purchaseRequestCode;

        $body = '<p>' . $cancelDocNameBody . ' is return back to amend due to below reason.</p><p>Comment : ' . $input['ammendComments'] . '</p>';
        $subject = $cancelDocNameSubject . ' is return back to amend';

        if ($purchaseRequest->PRConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseRequest->PRConfirmedBySystemID,
                'companySystemID' => $purchaseRequest->companySystemID,
                'docSystemID' => $purchaseRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseRequest->purchaseRequestID);
        }

        $purchaseRequest->PRConfirmedYN = 0;
        $purchaseRequest->PRConfirmedBy = '';
        $purchaseRequest->PRConfirmedByEmpName = '';
        $purchaseRequest->PRConfirmedBySystemID = '';
        $purchaseRequest->PRConfirmedDate = '';
        $purchaseRequest->approved = 0;
        $purchaseRequest->approvedDate = '';
        $purchaseRequest->approvedByUserID = '';
        $purchaseRequest->approvedByUserSystemID = '';
        $purchaseRequest->RollLevForApp_curr = 1;
        $purchaseRequest->save();

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
                                            ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
                                            ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                            //->where('approvedYN', -1)
                                            ->get();

        foreach ($documentApproval as $da) {

            if($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $purchaseRequest->companySystemID,
                    'docSystemID' => $purchaseRequest->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $purchaseRequest->purchaseRequestID);
            }

            array_push($ids_to_delete, $da->documentApprovedID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"],500);
        }

        DocumentApproved::destroy($ids_to_delete);

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully return back to amend');
    }
    /**
     * manual Close Purchase Request
     * Post /manualClosePurchaseRequest
     *
     * @param $request
     *
     * @return Response
     */
    public function manualClosePurchaseRequest(Request $request)
    {

        $input = $request->all();
        $purchaseRequest = PurchaseRequest::with(['confirmed_by','details'])->find($input['purchaseRequestID']);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if($purchaseRequest->manuallyClosed == 1){
            return $this->sendError('This request already closed');
        }

        if($purchaseRequest->selectedForPO != 0 || $purchaseRequest->supplyChainOnGoing != 0 || $purchaseRequest->prClosedYN != 0 ){
            return $this->sendError('You can not close this, request is currently processing');
        }

        if($purchaseRequest->approved != -1 || $purchaseRequest->cancelledYN == -1){
            return $this->sendError('You can only close approved request');
        }

        /*$checkPo = PurchaseOrderDetails::where('purchaseRequestID', $input['purchaseRequestID'])->count();

        if ($checkPo > 0) {
            return $this->sendError('Cannot cancel. Order is created for this request');
        }*/

        $employee = \Helper::getEmployeeInfo();

        $emails = array();
        $ids_to_delete = array();

        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseRequest->purchaseRequestCode;

        $body = '<p>' . $cancelDocNameBody . ' is manually closed due to below reason.</p><p>Comment : ' . $input['manuallyClosedComment'] . '</p>';
        $subject = $cancelDocNameSubject . ' is closed';

        if ($purchaseRequest->PRConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseRequest->PRConfirmedBySystemID,
                'companySystemID' => $purchaseRequest->companySystemID,
                'docSystemID' => $purchaseRequest->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseRequest->purchaseRequestID);
        }

        $purchaseRequest->manuallyClosed = 1;
        $purchaseRequest->manuallyClosedByEmpSystemID = $employee->employeeSystemID;
        $purchaseRequest->manuallyClosedByEmpID = $employee->empID;
        $purchaseRequest->manuallyClosedByEmpName = $employee->empName;
        $purchaseRequest->manuallyClosedComment = $input['manuallyClosedComment'];
        $purchaseRequest->manuallyClosedDate = now();
        $purchaseRequest->save();

        $purchaseDetails = PurchaseRequestDetails::where('purchaseRequestID',$purchaseRequest->purchaseRequestID)
                                                   ->where('selectedForPO',0)
                                                   ->where('fullyOrdered','!=',2)
                                                   ->get();

        foreach ($purchaseDetails as $det){

            $detail = PurchaseRequestDetails::where('purchaseRequestDetailsID',$det['purchaseRequestDetailsID'])->first();

            if($detail){
                if($detail->selectedForPO == 0 and $detail->fullyOrdered != 2 ){
                    $detail->manuallyClosed = 1;
                    $detail->manuallyClosedByEmpSystemID = $employee->employeeSystemID;
                    $detail->manuallyClosedByEmpID = $employee->empID;
                    $detail->manuallyClosedByEmpName = $employee->empName;
                    $detail->manuallyClosedComment = $input['manuallyClosedComment'];
                    $detail->manuallyClosedDate = now();
                    $detail->save();
                }
            }
        }


        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
                                            ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
                                            ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                            ->get();

        foreach ($documentApproval as $da) {
            if($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $purchaseRequest->companySystemID,
                    'docSystemID' => $purchaseRequest->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $purchaseRequest->purchaseRequestID);
            }

          //  array_push($ids_to_delete, $da->documentApprovedID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"],500);
        }

       // DocumentApproved::destroy($ids_to_delete);

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully closed');
    }

    /**
     * Display the specified PurchaseRequest print.
     * GET|HEAD /printPurchaseRequest
     *
     * @param  int $request
     *
     * @return Response
     */
    public function printPurchaseRequest(Request $request)
    {
        $id = $request->get('id');
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by',
            'priority', 'location', 'details.uom', 'company', 'approved_by' => function ($query) {
                $query->with('employee')
                      ->whereIn('documentSystemID', [1, 50, 51]);
            }
        ])->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $array = array('request' => $purchaseRequest);

        return view('home',$array);

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request retrieved successfully');
    }

}
