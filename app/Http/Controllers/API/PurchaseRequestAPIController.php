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
 * -- Date: 11-May 2018 By: Fayas Description: Added new functions named as getPurchaseRequestApprovedByUser()
 * -- Date: 15-May 2018 By: Fayas Description: Added new functions named as purchaseRequestsPOHistory()
 * -- Date: 18-May 2018 By: Fayas Description: Added new functions named as manualClosePurchaseRequestPreCheck()
 * -- Date: 21-May 2018 By: Fayas Description: Added new functions named as returnPurchaseRequestPreCheck(),cancelPurchaseRequestPreCheck()
 * -- Date: 23-May 2018 By: Fayas Description: Added new functions named as purchaseRequestAudit()
 * -- Date: 06-June 2018 By: Mubashir Description: Modified getPurchaseRequestByDocumentType() to handle filters from local storage
 * -- Date: 11-June 2018 By: Fayas Description: Added new functions named as getReportOpenRequest(),exportReportOpenRequest()
 * -- Date: 31-July 2018 By: Nazir Description: Added new functions named as getPurchaseRequestReopen()
 * -- Date: 01-August 2018 By: Nazir Description: Added new functions named as getPurchaseRequestReferBack()
 * * -- Date: 28-Oct 2019 By: Rilwan Description: Added new functions named as getCancelledDetails(),getClosedDetails
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Requests\API\CreatePurchaseRequestAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\MaterielRequest;
use App\Models\DocumentReferedHistory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Models\EmployeesDepartment;
use App\Models\FinanceItemCategoryMaster;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrderDetail;
use App\Models\Location;
use App\Models\Months;
use App\Models\PulledItemFromMR;
use App\Models\PrDetailsReferedHistory;
use App\Models\Priority;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\ItemCategoryTypeMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequestReferred;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\Unit;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\MaterielRequestRepository;
use App\Repositories\UserRepository;
use App\helper\PurcahseRequestDetail;
use App\Traits\AuditTrial;
use App\helper\CancelDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\WarehouseMaster;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SegmentAllocatedItemRepository;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateMaterialRequestItem;
use App\Models\MaterielRequestDetails;
use App\helper\CreateExcel;
use App\Models\DocCodeSetupCommon;
use App\Models\DocumentCodeMaster;
use App\Models\DocumentCodeTransaction;
use App\Repositories\PurchaseRequestDetailsRepository;
use App\Models\DocumentModifyRequest;
use App\Repositories\DocumentApprovedRepository;
use App\Repositories\DocumentModifyRequestRepository;
use App\Services\DocumentCodeConfigurationService;
use App\Jobs\ExportDetailedORList;

/**
 * Class PurchaseRequestController
 * @package App\Http\Controllers\API
 */
class PurchaseRequestAPIController extends AppBaseController
{
    /** @var  PurchaseRequestRepository */
    private $purchaseRequestRepository;
    private $userRepository;
    private $segmentAllocatedItemRepository;
    private $materielRequestRepository;
    private $purchaseRequestDetailsRepository;
    private $documentCodeConfigurationService;

    public function __construct(DocumentCodeConfigurationService $documentCodeConfigurationService , PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo,PurchaseRequestRepository $purchaseRequestRepo, UserRepository $userRepo, SegmentAllocatedItemRepository $segmentAllocatedItemRepo, MaterielRequestRepository $materielRequestRepository)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepo;
        $this->purchaseRequestDetailsRepository = $purchaseRequestDetailsRepo;
        $this->userRepository = $userRepo;
        $this->segmentAllocatedItemRepository = $segmentAllocatedItemRepo;
        $this->materielRequestRepository = $materielRequestRepository;
        $this->documentCodeConfigurationService = $documentCodeConfigurationService;
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

        $items = ItemAssigned::where('companySystemID', $companyId)->where('isActive', 1)->where('isAssigned', -1)
                             ->whereHas('item_category_type', function ($query) {
                                    $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                                });


        if ($policy == 0 && $financeCategoryId != 0) {
            $items = $items->where('financeCategoryMaster', $financeCategoryId);
        }

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }


        $items = $items->take(20)->get();

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

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $segments = SegmentMaster::whereIn("companySystemID", $childCompanies)->approved()->withAssigned($companyId);

        if (array_key_exists('isFilter', $input)) {
            if ($input['isFilter'] != 1) {
                $segments = $segments->where('isActive', 1);
            }
        } else {
            $segments = $segments->where('isActive', 1);
        }

        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();
        $buyers = Employee::where('discharegedYN', '!=', -1)
            ->where('empActive', 1)
            ->whereIn('empCompanySystemID', $childCompanies)
            ->where('isSupportAdmin', '!=', -1)
            ->where('isSuperAdmin', '!=', -1)
            ->get();


        $years = PurchaseRequest::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $buyersEmpId = PurchaseRequest::pluck('buyerEmpSystemID');
        $buyersOnly = Employee::whereIn('employeeSystemID', $buyersEmpId)->get();

        $currencies = CurrencyMaster::all();

        $financeCategories = FinanceItemCategoryMaster::all();

        $locations = Location::where('is_deleted',0)->get();

        $priorities = Priority::all();

        $companyCurrency = Company::find($companyId);


        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                          array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));


        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
            ->where('companySystemID', $companyId)
            ->first();

        $allocateItemToSegment = CompanyPolicyMaster::where('companyPolicyCategoryID', 57)
            ->where('companySystemID', $companyId)
            ->first();

        
        $checkAltUOM = CompanyPolicyMaster::where('companyPolicyCategoryID', 60)
            ->where('companySystemID', $companyId)
            ->first();

        $financeYears = CompanyFinanceYear::selectRaw('DATE_FORMAT(bigginingDate,"%M %d %Y") as bigginingDate, DATE_FORMAT(endingDate,"%M %d %Y") as endingDate, companyFinanceYearID')->orderBy('companyFinanceYearID', 'desc')->where('companySystemID', $companyId)->get();


        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0, 'allowItemToType' => 0, 'allocateItemToSegment' => 0);

        if ($checkBudget) {
            $conditions['checkBudget'] = $checkBudget->isYesNO;
        }

        if ($allowFinanceCategory) {
            $conditions['allowFinanceCategory'] = $allowFinanceCategory->isYesNO;
        }

        if ($allowItemToType) {
            $conditions['allowItemToType'] = $allowItemToType->isYesNO;
        }

        if ($allocateItemToSegment) {
            $conditions['allocateItemToSegment'] = $allocateItemToSegment->isYesNO;
        }

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'financeYears' => $financeYears,
            'currencies' => $currencies,
            'financeCategories' => $financeCategories,
            'locations' => $locations,
            'buyers' => $buyers,
            'buyersOnly' => $buyersOnly,
            'companyFinanceYear' => $companyFinanceYear,
            'priorities' => $priorities,
            'financialYears' => $financialYears,
            'conditions' => $conditions,
            'localCurrency' => (isset($companyCurrency)) ? $companyCurrency->localCurrencyID : 0,
            'altUOM' => (isset($checkAltUOM)) ? (boolean) $checkAltUOM->isYesNO : false
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getEligibleMr(Request $request){
        $input = $request->all();
        $RequestID = $input['RequestID'];
        $purchaseRequest = PurchaseRequest::where('purchaseRequestID' , $RequestID)->first();
        $eligibleMr = PurchaseRequestDetails::where('purchaseRequestID',$RequestID)->where('is_eligible_mr', 1)->get();
        
        $checkPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 65)
        ->where('companySystemID',  $purchaseRequest->companySystemID)
        ->first();

        $data = ['eligibleMr'=>$eligibleMr,
                 'checkPolicy'=>$checkPolicy];
        
        return $this->sendResponse($data, 'Record retrieved successfully');
    }

    public function getWarehouse(Request $request){
        $input = $request->all();
        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $subCompanies = [$companyId];
        }

        $wareHouses = WarehouseMaster::whereIn('companySystemID',$subCompanies);
        $warehouseData = $wareHouses->where('isActive', 1);
        $warehouseData = $warehouseData->get();

        $categoryType = $request->input('categoryType');
        $categoryTypeID = collect($categoryType)->pluck('id')->toArray();

        $item = ErpItemLedger::select('erp_itemledger.companySystemID', 'erp_itemledger.itemSystemCode', 'erp_itemledger.itemPrimaryCode', 'erp_itemledger.itemDescription', 'itemmaster.secondaryItemCode')
            ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
            ->whereIn('erp_itemledger.companySystemID', $subCompanies)
            ->where('itemmaster.financeCategoryMaster', 1)
            ->when(!empty($categoryTypeID), function ($query) use ($categoryTypeID) {
                $query->whereHas('item_master.item_category_type', function ($query) use ($categoryTypeID) {
                    $query->whereIn('categoryTypeID', $categoryTypeID);
                });
            })
            ->groupBy('erp_itemledger.itemSystemCode')
            ->get();
    
            $categoryTypeData = ItemCategoryTypeMaster::all();

            $output = array(
                'item' => $item,
                'warehouseData' => $warehouseData,
                'categoryTypeData' => $categoryTypeData,
            );


        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function createPrMaterialRequest(Request $request){
        
        DB::beginTransaction();
        try {
            $input = $request->all();

            $purchaseRequestID = $input['purchaseRequestID'];
            $wareHouseSystemCode = $input['wareHouseSystemCode'];
            $prDetails = PurchaseRequest::where('purchaseRequestID',$purchaseRequestID)->first();
            $inputData = [  'priority'=>$prDetails->priority,
                            'comments'=>'Generated From PR-'.' ' .$prDetails->purchaseRequestCode,
                            'serviceLineSystemID'=>$prDetails->serviceLineSystemID,
                            'location'=>$wareHouseSystemCode,
                            ];

            $employee = \Helper::getEmployeeInfo();

            $inputData['createdPcID'] = gethostname();
            $inputData['createdUserID'] = $employee->empID;
            $inputData['createdUserSystemID'] = $employee->employeeSystemID;
            $inputData['RequestedDate'] = now();
            $inputData['departmentID'] = 'IM';
            $inputData['departmentSystemID'] = 10;
            $inputData['documentSystemID'] =  9;
            $inputData['ConfirmedYN'] =  0;
            $inputData['RollLevForApp_curr'] = 1;
            $inputData['companySystemID'] = $input['companySystemID'];

            $lastSerial = MaterielRequest::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $inputData['documentSystemID'])
            ->orderBy('serialNumber', 'desc')
            ->lockForUpdate()
            ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
            }

            $inputData['serialNumber'] = $lastSerialNumber;

            $segment = SegmentMaster::where('serviceLineSystemID', $prDetails->serviceLineSystemID)->first();
            if ($segment) {
                $inputData['serviceLineCode'] = $segment->ServiceLineCode;
            }

            $document = DocumentMaster::where('documentSystemID', $inputData['documentSystemID'])->first();
            if ($document) {
                $inputData['documentID'] = $document->documentID;
            }

            $company = Company::where('companySystemID', $inputData['companySystemID'])->first();
            if ($company) {
                $inputData['companyID'] = $company->CompanyID;
            }

            $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
            $inputData['RequestCode'] = $inputData['companyID'] . '\\' . $inputData['departmentID'] . '\\' . $inputData['serviceLineCode'] . '\\' . $inputData['documentID'] . $code;    

            $MaterialRequest = MaterielRequest::create($inputData);
            $MaterialRequestID = $MaterialRequest->RequestID;
            $input['MaterialRequestID'] = $MaterialRequestID;
            
            $db = isset($input['db']) ? $input['db'] : "";
            if(isset($MaterialRequest))
            {   
                $isJobData = ['is_job_run'=>1];
                $isJobUpdate = MaterielRequest::where('RequestID', $MaterialRequestID)->update($isJobData);
                GenerateMaterialRequestItem::dispatch($input,$db);
                DB::commit();
                return $this->sendResponse($MaterialRequest, 'Material request & material items created successfully');

            }
            else
            {
                DB::rollBack();
                return $this->sendError('Unable to create material items', 422);
            }
            
            
            } catch (\Exception $exception) {
                
                return $this->sendError($exception->getMessage(), 500);
            }
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
        $purchaseRequests = $this->getPrToGrvQry($input);
        $data = \DataTables::of($purchaseRequests)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseRequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);

        return $data;
        //return $this->sendResponse($purchaseRequests, 'Record retrieved successfully');
    }

    public function getPrToGrvQry($request)
    {
        $input = $request;
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

        if (!array_key_exists('documentId', $input)) {
            $input['documentId'] = 0;
        }

        $purchaseRequests = PurchaseRequest::where('companySystemID', $input['companyId'])
            ->where('PRConfirmedYN', 1)
           // ->where('cancelledYN', 0)
            ->when(request('date_by') == 'PRRequestedDate', function ($q) use ($from, $to) {
                return $q->whereBetween('PRRequestedDate', [$from, $to]);
            })
            ->when(request('documentId') == 1, function ($q) use ($documentSearch) {
                $q->where('purchaseRequestCode', 'LIKE', "%{$documentSearch}%");
            })
            ->when(request('date_by') == 'all' && count($years) > 0, function ($q) use ($years) {
                $q->whereIn(DB::raw("YEAR(PRRequestedDate)"), $years);
            })
            ->whereHas('details', function ($prd) use ($itemPrimaryCodes, $from, $to, $documentSearch, $input) {

                if ($input['date_by'] == 'approvedDate' ||
                    $input['date_by'] == 'grvDate' ||
                    $input['grv'] == 'inComplete' ||
                    $input['documentId'] == 2 ||
                    count($input['itemPrimaryCodes']) > 0
                ) {

                    $prd->with(['podetail'=> function ($pod) use ($from, $to, $documentSearch) {
                        return $pod->whereHas('order', function ($po) use ($from, $to, $documentSearch) {
                            return $po->where('poConfirmedYN', 1)
                                ->when(request('date_by') == 'approvedDate', function ($q) use ($from, $to) {
                                    return $q->whereBetween('approvedDate', [$from, $to]);
                                })
                                ->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                                    return $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                                });
                        })
                            ->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                return $q->whereHas('grv_details', function ($q) use ($from, $to) {
                                    $q->whereHas('grv_master', function ($q) use ($from, $to) {
                                        $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                            return $q->whereBetween('grvDate', [$from, $to]);
                                        });
                                    });
                                });
                            })
                            ->when(request('grv') == 'inComplete', function ($q) {
                                return $q->whereIn('goodsRecievedYN', [0, 1])
                                         ->where('manuallyClosed',0);
                            });
                    }])
                        ->when(request('itemPrimaryCodes', false), function ($q, $itemPrimaryCodes) {
                            return $q->whereIn('itemCode', $itemPrimaryCodes);
                        });
                } else {
                    $prd->with(['podetail' => function ($pod) use ($from, $to, $documentSearch) {
                        $pod->whereHas('order', function ($po) use ($from, $to, $documentSearch) {
                            $po->where('poConfirmedYN', 1)
                                ->when(request('date_by') == 'approvedDate', function ($q) use ($from, $to) {
                                    return $q->whereBetween('approvedDate', [$from, $to]);
                                })
                                ->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                                    return $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                                });
                        })
                            ->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                return $q->whereHas('grv_details', function ($q) use ($from, $to) {
                                    $q->whereHas('grv_master', function ($q) use ($from, $to) {
                                        $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                            return $q->whereBetween('grvDate', [$from, $to]);
                                        });
                                    });
                                });
                            })
                            ->when(request('grv') == 'inComplete', function ($q) {
                                return $q->whereIn('goodsRecievedYN', [0, 1]);
                            });
                    }])->when(request('itemPrimaryCodes', false), function ($q, $itemPrimaryCodes) {
                        return $q->whereIn('itemCode', $itemPrimaryCodes);
                    });
                }
            })
            ->with(['confirmed_by', 'details' => function ($prd) use ($itemPrimaryCodes, $from, $to, $documentSearch) {
                $prd->when(request('date_by') == 'approvedDate' ||
                    request('date_by') == 'grvDate' ||
                    request('grv') == 'inComplete' ||
                    request('documentId') == 2 ||
                    count(request('itemPrimaryCodes')) > 0, function ($q) use ($from, $to, $documentSearch) {

                    $q->with(['podetail' => function ($q) use ($from, $to, $documentSearch) {

                        $q->when(request('date_by') == 'approvedDate' || request('documentId') == 2, function ($q) use ($from, $to, $documentSearch) {
                                return $q->whereHas('order', function ($q) use ($from, $to, $documentSearch) {
                                    $q->where('poConfirmedYN', 1)
                                        ->when(request('date_by') == 'approvedDate', function ($q) use ($from, $to) {
                                            return $q->whereBetween('approvedDate', [$from, $to]);
                                        })
                                        ->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                                            return $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                                        });
                                });
                            })
                            ->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                $q->whereHas('grv_details', function ($q) use ($from, $to) {
                                    $q->whereHas('grv_master', function ($q) use ($from, $to) {
                                        return $q->whereBetween('grvDate', [$from, $to]);
                                    });
                                });
                            })
                            ->when(request('grv') == 'inComplete', function ($q) {
                                return $q->whereIn('goodsRecievedYN', [0, 1])
                                          ->where('manuallyClosed',0);
                            });
                    }]);

                })
                    ->with(['uom', 'podetail' => function ($q) use ($from, $to, $documentSearch) {
                            $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to, $documentSearch) {
                                $q->whereHas('grv_details', function ($q) use ($from, $to) {
                                    $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                        $q->whereHas('grv_master', function ($q) use ($from, $to) {
                                            $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                                return $q->whereBetween('grvDate', [$from, $to]);
                                            });
                                        });
                                    });
                                });
                            })
                            ->whereHas('order', function ($q) use ($from, $to, $documentSearch) {
                                $q->where('poConfirmedYN', 1)
                                  ->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                                        return $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                                    });
                            })
                            ->with(['order' => function ($q) use ($from, $to, $documentSearch) {
                                $q->where('poConfirmedYN', 1)
                                    ->when(request('date_by') == 'approvedDate', function ($q) use ($from, $to) {
                                        return $q->whereBetween('approvedDate', [$from, $to]);
                                    })
                                    ->when(request('documentId') == 2, function ($q) use ($documentSearch) {
                                        return $q->where('purchaseOrderCode', 'LIKE', "%{$documentSearch}%");
                                    });
                            }, 'reporting_currency', 'grv_details' => function ($q) use ($from, $to) {
                                $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                    $q->whereHas('grv_master', function ($q) use ($from, $to) {
                                        $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                            return $q->whereBetween('grvDate', [$from, $to]);
                                        });
                                    });
                                })
                                    ->with(['grv_master' => function ($q) use ($from, $to) {
                                        $q->when(request('date_by') == 'grvDate', function ($q) use ($from, $to) {
                                            return $q->whereBetween('grvDate', [$from, $to]);
                                        });
                                    }]);
                            }])
                            ->when(request('grv') == 'inComplete', function ($q) {
                                return $q->whereIn('goodsRecievedYN', [0, 1])
                                         ->where('manuallyClosed',0);
                            });
                    }])
                    ->when(request('itemPrimaryCodes', false), function ($q, $itemPrimaryCodes) {
                        return $q->whereIn('itemCode', $itemPrimaryCodes);
                    });
            }]);


        return $purchaseRequests;
    }


    public function exportPrToGrvReport(Request $request)
    {
        $input = $request->all();
        $data = array();
        $output = ($this->getPrToGrvQry($input))->orderBy('purchaseRequestID', 'DES')->get();
        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['Company ID'] = $value->companyID;
                //$data[$x]['Company Name'] = $val->CompanyName;
                $data[$x]['Segment'] = $value->serviceLineCode;
                $data[$x]['PR Number'] = $value->purchaseRequestCode;

                if ($value->confirmed_by) {
                    $data[$x]['Processed By'] = $value->confirmed_by->empName;
                } else {
                    $data[$x]['Processed By'] = '';
                }

                $data[$x]['PR Date'] = \Helper::dateFormat($value->PRRequestedDate);
                $data[$x]['PR Comment'] = $value->comments;

                if ($value->approved == -1) {
                    $data[$x]['PR Approved'] = 'Yes';
                } else {
                    $data[$x]['PR Approved'] = 'No';
                }

                if (count($value->details) > 0) {
                    $itemCount = 0;
                    foreach ($value->details as $item) {

                        if ($itemCount != 0) {
                            $x++;
                            $data[$x]['Company ID'] = '';
                            //$data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Segment'] = '';
                            $data[$x]['PR Number'] = '';
                            $data[$x]['Processed By'] = '';
                            $data[$x]['PR Date'] = '';
                            $data[$x]['PR Comment'] = '';
                            $data[$x]['PR Approved'] = '';
                        }

                        if($value->cancelledYN) {
                            $data[$x]['PR Status'] = 'Cancelled';
                        }elseif ($item->manuallyClosed){
                            $data[$x]['PR Status'] = 'Closed';
                        }else{
                            $data[$x]['PR Status'] = '';
                        }
                        $data[$x]['Item Code'] = $item->itemPrimaryCode;
                        $data[$x]['Item Description'] = $item->itemDescription;
                        $data[$x]['Part No / Ref.Number'] = $item->partNumber;
                        if ($item->uom) {
                            $data[$x]['Unit'] = $item->uom->UnitShortCode;
                        } else {
                            $data[$x]['Unit'] = '';
                        }
                        $data[$x]['PR Qty'] = $item->quantityRequested;

                        if (count($item->podetail) > 0) {
                            $poCount = 0;
                            foreach ($item->podetail as $poDetail) {
                                if ($poCount != 0) {
                                    $x++;
                                    $data[$x]['Company ID'] = '';
                                    //$data[$x]['Company Name'] = $val->CompanyName;
                                    $data[$x]['Segment'] = '';
                                    $data[$x]['PR Number'] = '';
                                    $data[$x]['Processed By'] = '';
                                    $data[$x]['PR Date'] = '';
                                    $data[$x]['PR Comment'] = '';
                                    $data[$x]['PR Approved'] = '';
                                    $data[$x]['PR Status'] = '';
                                    $data[$x]['Item Code'] = '';
                                    $data[$x]['Item Description'] = '';
                                    $data[$x]['Part No / Ref.Number'] = '';
                                    $data[$x]['Unit'] = '';
                                    $data[$x]['PR Qty'] = '';
                                }

                                if ($poDetail->order) {
                                    $data[$x]['PO Number'] = $poDetail->order->purchaseOrderCode;
                                    $data[$x]['ETA'] = \Helper::dateFormat($poDetail->order->expectedDeliveryDate);
                                    $data[$x]['Supplier Code'] = $poDetail->order->supplierPrimaryCode;
                                    $data[$x]['Supplier Name'] = $poDetail->order->supplierName;
                                } else {
                                    $data[$x]['PO Number'] = '';
                                    $data[$x]['ETA'] = '';
                                    $data[$x]['Supplier Code'] = '';
                                    $data[$x]['Supplier Name'] = '';
                                }

                                $data[$x]['PO Qty'] = $poDetail->manuallyClosed == 1 ?  round($poDetail->receivedQty,2) :  round($poDetail->noQty,2);

                                if ($poDetail->reporting_currency) {
                                    $data[$x]['Currency'] = $poDetail->reporting_currency->CurrencyCode;
                                } else {
                                    $data[$x]['Currency'] = '';
                                }


                                $data[$x]['PO Cost'] = round($poDetail->GRVcostPerUnitComRptCur, 2);

                                if ($poDetail->order) {
                                    $data[$x]['PO Confirmed Date'] = \Helper::dateFormat($poDetail->order->poConfirmedDate);
                                } else {
                                    $data[$x]['PO Confirmed Date'] = '';
                                }

                                if ($poDetail->order) {
                                    if ($poDetail->order->approved == -1) {
                                        $data[$x]['PO Approved Status'] = 'Yes';
                                    } else {
                                        $data[$x]['PO Approved Status'] = 'No';
                                    }
                                } else {
                                    $data[$x]['PO Approved Status'] = '';
                                }

                                if ($poDetail->order) {
                                    $data[$x]['Approved Date'] = \Helper::dateFormat($poDetail->order->approvedDate);
                                } else {
                                    $data[$x]['Approved Date'] = '';
                                }

                                if($poDetail->order && $poDetail->order->poCancelledYN) {
                                    $data[$x]['PO Status'] = 'Cancelled';
                                }elseif ($poDetail->manuallyClosed){
                                    $data[$x]['PO Status'] = 'Closed';
                                }else{
                                    $data[$x]['PO Status'] = '';
                                }

                                if (count($poDetail->grv_details) > 0) {
                                    $grvCount = 0;
                                    foreach ($poDetail->grv_details as $grvDetail) {
                                        if ($grvCount != 0) {
                                            $x++;
                                            $data[$x]['Company ID'] = '';
                                            //$data[$x]['Company Name'] = $val->CompanyName;
                                            $data[$x]['Segment'] = '';
                                            $data[$x]['PR Number'] = '';
                                            $data[$x]['Processed By'] = '';
                                            $data[$x]['PR Date'] = '';
                                            $data[$x]['PR Comment'] = '';
                                            $data[$x]['PR Approved'] = '';
                                            $data[$x]['PR Status'] = '';
                                            $data[$x]['Item Code'] = '';
                                            $data[$x]['Item Description'] = '';
                                            $data[$x]['Part No / Ref.Number'] = '';
                                            $data[$x]['Unit'] = '';
                                            $data[$x]['PR Qty'] = '';
                                            $data[$x]['PO Number'] = '';
                                            $data[$x]['ETA'] = '';
                                            $data[$x]['Supplier Code'] = '';
                                            $data[$x]['Supplier Name'] = '';
                                            $data[$x]['PO Qty'] = '';
                                            $data[$x]['Currency'] = '';
                                            $data[$x]['PO Cost'] = '';
                                            $data[$x]['PO Confirmed Date'] = '';
                                            $data[$x]['PO Approved Status'] = '';
                                            $data[$x]['Approved Date'] = '';
                                            $data[$x]['PO Status'] = '';
                                        }

                                        if ($grvDetail->grv_master) {
                                            $data[$x]['Receipt Doc Number'] = $grvDetail->grv_master->grvPrimaryCode;
                                            $data[$x]['Receipt Date'] = \Helper::dateFormat($grvDetail->grv_master->grvDate);
                                        } else {
                                            $data[$x]['Receipt Doc Number'] = '';
                                            $data[$x]['Receipt Date'] = '';
                                        }
                                        $data[$x]['Receipt Qty'] = $grvDetail->noQty;

                                        if($grvDetail->grv_master && $grvDetail->grv_master->grvCancelledYN) {
                                            $data[$x]['GRV Status'] = 'Cancelled';
                                        }else{
                                            $data[$x]['GRV Status'] = '';
                                        }

                                        if($poDetail->manuallyClosed == 1){
                                            $data[$x]['Receipt Status'] = "Fully Received";
                                        }else{
                                            if ($poDetail->goodsRecievedYN == 2) {
                                                $data[$x]['Receipt Status'] = "Fully Received";
                                            } else if ($poDetail->goodsRecievedYN == 0) {
                                                $data[$x]['Receipt Status'] = "Not Received";
                                            } else if ($poDetail->goodsRecievedYN == 1) {
                                                $data[$x]['Receipt Status'] = "Partially Received";
                                            }
                                        }

                                        $grvCount++;
                                    }
                                } else {
                                    $data[$x]['Receipt Doc Number'] = '';
                                    $data[$x]['Receipt Date'] = '';
                                    $data[$x]['Receipt Qty'] = '';
                                    $data[$x]['GRV Status'] = '';
                                    $data[$x]['Receipt Status'] = "Not Received";
                                }
                                $poCount++;
                            }
                        } else {
                            $data[$x]['PO Number'] = '';
                            $data[$x]['ETA'] = '';
                            $data[$x]['Supplier Code'] = '';
                            $data[$x]['Supplier Name'] = '';
                            $data[$x]['PO Qty'] = '';
                            $data[$x]['Currency'] = '';
                            $data[$x]['PO Cost'] = '';
                            $data[$x]['PO Confirmed Date'] = '';
                            $data[$x]['PO Approved Status'] = '';
                            $data[$x]['Approved Date'] = '';
                            $data[$x]['PO Status'] = '';
                            $data[$x]['Receipt Doc Number'] = '';
                            $data[$x]['Receipt Date'] = '';
                            $data[$x]['Receipt Qty'] = '';
                            $data[$x]['GRV Status'] = '';
                            $data[$x]['Receipt Status'] = "Not Received";
                        }
                        $itemCount++;
                    }
                } else {
                    $data[$x]['Item Code'] = 'Item Code';
                    $data[$x]['Item Description'] = 'Item Description';
                    $data[$x]['Part No / Ref.Number'] = '';
                    $data[$x]['Unit'] = '';
                    $data[$x]['PR Qty'] = '';
                    $data[$x]['PO Number'] = '';
                    $data[$x]['ETA'] = '';
                    $data[$x]['Supplier Code'] = '';
                    $data[$x]['Supplier Name'] = '';
                    $data[$x]['PO Qty'] = '';
                    $data[$x]['Currency'] = '';
                    $data[$x]['PO Cost'] = '';
                    $data[$x]['PO Confirmed Date'] = '';
                    $data[$x]['PO Approved Status'] = '';
                    $data[$x]['Approved Date'] = '';
                    $data[$x]['PO Status'] = '';
                    $data[$x]['Receipt Doc Number'] = '';
                    $data[$x]['Receipt Date'] = '';
                    $data[$x]['Receipt Qty'] = '';
                    $data[$x]['GRV Status'] = '';
                    $data[$x]['Receipt Status'] = "Not Received";
                }
                $x++;
            }
        }

        //  \Excel::create('pr_to_grv', function ($excel) use ($data) {
        //     $excel->sheet('sheet name', function ($sheet) use ($data) {
        //         $sheet->fromArray($data, null, 'A1', true);
        //         $sheet->setAutoSize(true);
        //         $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
        //     });
        //     $lastrow = $excel->getActiveSheet()->getHighestRow();
        //     $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        // })->download($type);

        // return $this->sendResponse(array(), 'successfully export');

        $doc_name = 'pr_to_grv';
        $doc_name_path = 'pr_to_grv/';
        $path = 'procurement/report/'.$doc_name_path.'excel/';
        $companyMaster = Company::find(isset($request->companyId)?$request->companyId: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }
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
            $value['delegation'] = false;
            $value['deparmtnet'] = null;
            if ($value['approvedYN'] == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return $this->sendError('Policy not found');
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $value['approvalGroupID'])
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID)
                    ->where('isActive', 1)
                    ->where('removedYN', 0);
                //->get();

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $value['serviceLineSystemID']);
                }

                $approvalList = $approvalList->with(['employee'])
                    ->whereHas('employee', function($q) {
                        $q->where('discharegedYN',0);
                    })
                    ->groupBy('employeeSystemID')
                    ->get();
                $value['approval_list'] = $approvalList;
            }
            else
            {
                $approved_id = $value->employeeSystemID;
                $approved_date = $value->approvedDate;
                $approved_date = Carbon::parse($approved_date)->format('Y-m-d');
                $deparment = EmployeesDepartment::where('employeeSystemID',$approved_id)
                                    ->where('approvalDeligated','!=',0)
                                    //->where('isActive','=',1)
                                    ->where('companySystemID', $companySystemID)
                                    ->where('documentSystemID', $documentSystemID)
                                    ->where('employeeGroupID', $value->approvalGroupID)
                                    //->where('approvalDeligatedFrom', '<=', $approved_date)->where('approvalDeligatedTo', '>=', $approved_date)
                                    ->with(['delegator_employee'=>function($q){
                                        $q->Select('employeeSystemID','empUserName');
                                    }])->select('employeesDepartmentsID','approvalDeligatedFromEmpID')
                                   ->first(); 
                if($deparment)
                {
                    $value['delegation'] = true;
                    $value['deparmtnet'] = $deparment;
                }
       

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
        $input = $this->convertArrayToSelectedValue($input,
        array('serviceLineSystemID', 'cancelledYN', 'PRConfirmedYN', 'approved', 'month', 'year', 'buyerEmpSystemID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $search = $request->input('search.value');
        $serviceLineSystemID = collect((array) $request['serviceLineSystemID'])->pluck('id');
        $buyerEmpSystemId = collect((array) $request['buyerEmpSystemID'])->pluck('id');
        $purchaseRequests = $this->purchaseRequestRepository->purchaseRequestListQuery(
            $request, $input, $search, $serviceLineSystemID, $buyerEmpSystemId);

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
            ->selectRaw(
                'erp_purchaserequest.*,
                employeesdepartments.approvalDeligated,
                employees.empName As created_emp,
                financeitemcategorymaster.categoryDescription As financeCategoryDescription,
                serviceline.ServiceLineDes As PRServiceLineDes,
                erp_location.locationName As PRLocationName,
                erp_priority.priorityDescription As PRPriorityDescription,
                erp_documentapproved.documentApprovedID,
                rollLevelOrder,
                approvalLevelID,
                currencymaster.CurrencyCode,
                currencymaster.DecimalPlaces As DecimalPlaces,
                documentSystemCode, SUM(erp_purchaserequestdetails.totalCost) as totalCost')
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
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_purchaserequest', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'purchaseRequestID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_purchaserequest.companySystemID', $companyId)
                    ->where('erp_purchaserequest.approved', 0)
                    ->where('erp_purchaserequest.cancelledYN', 0)
                    ->where('erp_purchaserequest.PRConfirmedYN', 1);
            })
            ->join('erp_purchaserequestdetails', function ($query) use ($companyId) {
                $query->on('erp_purchaserequest.purchaseRequestID', '=', 'erp_purchaserequestdetails.purchaseRequestID');
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->join('currencymaster', 'erp_purchaserequest.currency', '=', 'currencyID')
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('financeitemcategorymaster', 'financeCategory', 'financeitemcategorymaster.itemCategoryID')
            ->join('erp_priority', 'priority', 'erp_priority.priorityID')
            ->join('erp_location', 'location', 'erp_location.locationID')
            ->join('serviceline', 'erp_purchaserequest.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->groupBy('erp_purchaserequestdetails.purchaseRequestID')
            ->whereIn('erp_documentapproved.documentSystemID', [1, 50, 51])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where(function($query) use ($search) {
                $query->where('erp_purchaserequest.purchaseRequestCode', 'LIKE', "%{$search}%")
                      ->orWhere('erp_purchaserequest.comments', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
        }
        
        return \DataTables::of($purchaseRequests)
            ->filter(function ($instance){  
            })
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

    /**
     * get Purchase Request fully Approved By User.
     * POST /getPurchaseRequestApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getPurchaseRequestApprovedByUser(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
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
            ->join('erp_purchaserequest', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'purchaseRequestID')
                    ->where('erp_purchaserequest.companySystemID', $companyId)
                    ->where('erp_purchaserequest.approved', -1)
                    ->where('erp_purchaserequest.PRConfirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('financeitemcategorymaster', 'financeCategory', 'financeitemcategorymaster.itemCategoryID')
            ->leftJoin('erp_priority', 'priority', 'erp_priority.priorityID')
            ->leftJoin('erp_location', 'location', 'erp_location.locationID')
            ->leftJoin('serviceline', 'erp_purchaserequest.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [1, 50, 51])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $purchaseRequests = $purchaseRequests->when($search != "", function ($q) use ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $q->where(function ($query) use ($search) {
                $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        });

        return \DataTables::of($purchaseRequests)
            ->filter(function ($instance){  
            })
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

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $procumentOrder->serviceLineSystemID)
            ->where('companySystemID', $companyID)
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        $policy = 1;

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyID)
            ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;
        }


        $documentSystemID = $procumentOrder->documentSystemID;
        if ($documentSystemID == 2) {
            $documentSystemIDChanged = 1;
        } else if ($documentSystemID == 5) {
            $documentSystemIDChanged = 50;
        } else if ($documentSystemID == 52) {
            $documentSystemIDChanged = 51;
        }

        $purchaseRequests = PurchaseRequest::where('companySystemID', $companyID)
            ->where('approved', -1)
            ->where('PRConfirmedYN', 1)
            ->where('prClosedYN', 0)
            ->where('cancelledYN', 0)
            ->where('selectedForPO', 0)
            ->where('supplyChainOnGoing', 0)
            ->where('manuallyClosed', 0)
            ->where('documentSystemID', $documentSystemIDChanged);
        if (isset($procumentOrder->financeCategory) && $procumentOrder->financeCategory > 0 && $policy == 0) {
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


        if(!isset($input['requested_by']))
        {
            $input['requested_by'] = $user->employee['employeeSystemID'];
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $input['PRRequestedDate'] = now();

        if (isset($input['budgetYearID']) && $input['budgetYearID'] > 0) {
            $checkCompanyFinanceYear = CompanyFinanceYear::find($input['budgetYearID']);
            if ($checkCompanyFinanceYear) {
                $input['budgetYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                $input['prBelongsYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
            }
        }


        $input['departmentID'] = 'PROC';

        $lastSerial = PurchaseRequest::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->orderBy('purchaseRequestID', 'desc')
            ->lockForUpdate()
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }


        $input['serialNumber'] = $lastSerialNumber;

        $serviceLineSystemID = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : null;
        $segment = SegmentMaster::where('serviceLineSystemID', $serviceLineSystemID)->first();
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

        $allocateItemToSegment = CompanyPolicyMaster::where('companyPolicyCategoryID', 57)
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($allocateItemToSegment && $allocateItemToSegment->isYesNO == 1) {
            $input['allocateItemToSegment'] = 1;
        }

        if(isset($input['isFromMaterielRequest']) && $input['isFromMaterielRequest']){
            $financeCategoryPolicyCheck = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $input['companySystemID'])->first();

            if ($financeCategoryPolicyCheck) {
                if ($financeCategoryPolicyCheck->isYesNO == 0) {
                    $input['financeCategory'] = 1;
                }
            }
        }

        $documentCodeTransaction = DocumentCodeTransaction::where('document_system_id', $input['documentSystemID'])
            ->where('company_id', $input['companySystemID'])
            ->first();

        if ($documentCodeTransaction) {
            $transactionID = $documentCodeTransaction->id;
            $documentCodeMaster = DocumentCodeMaster::where('document_transaction_id', $transactionID)
                ->where('company_id', $input['companySystemID'])
                ->first();

            if ($documentCodeMaster) {
                $documentCodeMasterID = $documentCodeMaster->id;
                $purchaseRequestCode = $this->documentCodeConfigurationService->getDocumentCodeConfiguration($input['documentSystemID'], $input['companySystemID'],$input,$lastSerialNumber,$documentCodeMasterID,$input['serviceLineCode']);
            }
        }
        
        if($purchaseRequestCode && $purchaseRequestCode['status'] == true){
            $input['purchaseRequestCode'] = $purchaseRequestCode['documentCode'];
            $input['serialNumber'] = $purchaseRequestCode['docLastSerialNumber'];
        } else {
            $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
            $input['purchaseRequestCode'] = $input['companyID'] . '\\' . $input['departmentID'] . '\\' . $input['serviceLineCode'] . '\\' . $input['documentID'] . $code;
        }
        

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
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by','currency_by','requestedby',
            'priority_pdf', 'location_pdf', 'details.uom', 'details.altUom' ,'company', 'segment', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('rejectedYN', 0)
                    ->whereIn('documentSystemID', [1, 50, 51]);
            }
        ])->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }
        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request retrieved successfully');
    }


    /**
     * Display the specified PurchaseRequest PO History.
     * GET|HEAD /purchaseRequestsPOHistory
     *
     * @param  int $id
     *
     * @return Response
     */
    public function purchaseRequestsPOHistory(Request $request)
    {
        $id = $request->get('id');
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by',
            'priority', 'location', 'details' => function ($q) {
                $q->with(['uom', 'podetail.order.created_by']);
            }, 'company', 'segment', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->whereIn('documentSystemID', [1, 50, 51]);
            }
        ])->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request retrieved successfully');
    }

    public function createPurchaseAPI(CreatePurchaseRequestAPIRequest $request)
    {

        $input = $this->convertArrayToValue($request->all());

        DB::beginTransaction();
        try {

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['createdPcID'] = gethostname();
        $input['createdUserSystemID'] = $request->employee_id;

        $input['PRRequestedDate'] = now();

        if (isset($input['budgetYearID']) && $input['budgetYearID'] > 0) {
            $checkCompanyFinanceYear = CompanyFinanceYear::find($input['budgetYearID']);
            if ($checkCompanyFinanceYear) {
                $input['budgetYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                $input['prBelongsYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
            }
        }

        $input['departmentID'] = 'PROC';

        $lastSerial = PurchaseRequest::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->orderBy('purchaseRequestID', 'desc')
            ->lockForUpdate()
            ->first();

        $lastSerialNumber = 1;
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

        $allocateItemToSegment = CompanyPolicyMaster::where('companyPolicyCategoryID', 57)
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($allocateItemToSegment && $allocateItemToSegment->isYesNO == 1) {
            $input['allocateItemToSegment'] = 1;
        }

        $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
        $input['purchaseRequestCode'] = $input['companyID'] . '\\' . $input['departmentID'] . '\\' . $input['serviceLineCode'] . '\\' . $input['documentID'] . $code;

        $purchaseRequests = $this->purchaseRequestRepository->create($input);

        $items = $request->items;
        $companySystemID = $input['companySystemID'];

        $errors = array();
        $insertedItems = array();
        $i = 0;
        foreach ($items as $itemPurchase) {

            $i++;
            $input = $itemPurchase;
            $input = $this->convertArrayToValue($input);


            $allowItemToTypePolicy = false;
            $itemNotound = false;
            $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
                ->where('companySystemID', $companySystemID)
                ->first();

            if ($allowItemToType) {
                if ($allowItemToType->isYesNO) {
                    $allowItemToTypePolicy = true;
                }
            }


            if ($allowItemToTypePolicy) {
                $input['itemCode'] = isset($input['itemCode']['id']) ? $input['itemCode']['id'] : $input['itemCode'];
            } else {
                if (isset($input['itemCode']['id'])) {
                    $input['itemCode'] = $input['itemCode']['id'];
                }
            }

            $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
                ->where('companySystemID', $companySystemID)
                ->first();

            if (empty($item)) {
                if (!$allowItemToTypePolicy) {
                    $errors[$i]["itemNotFound"] = $input['itemCode'];
                    continue;
                } else {
                    $itemNotound = true;
                }
            }


            $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $purchaseRequests['purchaseRequestID'])
                ->first();


            $input['budgetYear'] = $purchaseRequest->budgetYear;
            $input['itemPrimaryCode'] = (!$itemNotound) ? $item->itemPrimaryCode : null;
            $input['itemDescription'] = (!$itemNotound) ? $item->itemDescription : $input['itemCode'];
            $input['partNumber'] = (!$itemNotound) ? $item->secondaryItemCode : null;
            $input['itemFinanceCategoryID'] = (!$itemNotound) ? $item->financeCategoryMaster : null;
            $input['itemFinanceCategorySubID'] = (!$itemNotound) ? $item->financeCategorySub : null;
            //$input['estimatedCost'] = $item->wacValueLocal;

            if (!$itemNotound) {
                $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);
                $input['estimatedCost'];
                $input['altUnitValue'] = $input['quantityRequested'];
                $input['altUnit'];
                $input['totalCost'] = $input['altUnitValue'] * $input['estimatedCost'];
                $input['companySystemID'] = $item->companySystemID;
                $input['companyID'] = $item->companyID;
                $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
                $input['maxQty'] = $item->maximunQty;
                $input['minQty'] = $item->minimumQty;
                $input['purchaseRequestID'] = $purchaseRequests['purchaseRequestID'];


                $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                    ->where('mainItemCategoryID', $item->financeCategoryMaster)
                    ->where('itemCategorySubID', $item->financeCategorySub)
                    ->first();

                if (empty($financeItemCategorySubAssigned)) {
                    $errors[$i] = $input['itemCode']." - Finance category not assigned for the selected item";
                    continue;
                }

                if ($item->financeCategoryMaster == 1) {

                    $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $purchaseRequests['purchaseRequestID'])
                        ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                        })
                        ->first();
                    if ($alreadyAdded) {
                        $errors[$i] = $input['itemCode']." - Selected item is already added. Please check again";

                        continue;
                    }
                }

                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;

                if ($item->financeCategoryMaster == 3) {
                    $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                    if (!$assetCategory) {
                        $errors[$i] = $input['itemCode']." - Asset category not assigned for the selected item";
                        continue;
                    }
                    $input['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
                    $input['financeGLcodePL'] = $assetCategory->COSTGLCODE;
                } else {
                    $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                    $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                }

                $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;

                $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                    ->where('companySystemID', $purchaseRequest->companySystemID)
                    ->first();

                if ($allowFinanceCategory) {
                    $policy = $allowFinanceCategory->isYesNO;

                    if ($policy == 0) {
                        if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                            $errors[$i] = $input['itemCode']." - Category is not found";
                            continue;
                        }

                        //checking if item category is same or not
                        $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                            ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                            ->first();

                        if ($pRDetailExistSameItem) {
                            if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                                $errors[$i] = $input['itemCode']." - You cannot add different category item";
                                continue;
                            }
                        }
                    }
                }

                $group_companies = Helper::getSimilarGroupCompanies($companySystemID);
                $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
                    $query->whereIn('companySystemID', $group_companies)
                        ->where('approved', -1)
                        ->where('poType_N', '!=', 5)// poType_N = 5 =>work order
                        ->where('poCancelledYN', 0)
                        ->where('manuallyClosed', 0);
                })
                    ->where('itemCode', $input['itemCode'])
                    ->where('manuallyClosed', 0)
                    ->groupBy('erp_purchaseorderdetails.itemCode')
                    ->select(
                        [
                            'erp_purchaseorderdetails.companySystemID',
                            'erp_purchaseorderdetails.itemCode',
                            'erp_purchaseorderdetails.itemPrimaryCode'
                        ]
                    )
                    ->sum('noQty');

                $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
                    ->where('companySystemID', $companySystemID)
                    ->groupBy('itemSystemCode')
                    ->sum('inOutQty');

                $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
                    $query->whereIn('companySystemID', $group_companies)
                        ->where('grvTypeID', 2)
                        ->where('approved', -1)
                        ->groupBy('erp_grvmaster.companySystemID');
                })->whereHas('po_detail', function ($query) {
                    $query->where('manuallyClosed', 0)
                        ->whereHas('order', function ($query) {
                            $query->where('manuallyClosed', 0);
                        });
                })
                    ->where('itemCode', $input['itemCode'])
                    ->groupBy('erp_grvdetails.itemCode')
                    ->select(
                        [
                            'erp_grvdetails.companySystemID',
                            'erp_grvdetails.itemCode'
                        ])
                    ->sum('noQty');

                $quantityOnOrder = $poQty - $grvQty;
                $input['poQuantity'] = $poQty;
                $input['quantityOnOrder'] = $quantityOnOrder;
                $input['quantityInHand'] = $quantityInHand;


            } else {
                $input['purchaseRequestID'] = $purchaseRequests['purchaseRequestID'];
                $input['estimatedCost'];
                $input['altUnitValue'] = $input['quantityRequested'];
                $input['altUnit'];
                $input['totalCost'] = $input['altUnitValue'] * $input['estimatedCost'];
                $input['companySystemID'] = $companySystemID;
                $input['companyID'] = $purchaseRequest->companyID;
                $input['unitOfMeasure'] = null;
                $input['maxQty'] = 0;
                $input['minQty'] = 0;
                $input['poQuantity'] = 0;
                $input['quantityOnOrder'] = 0;
                $input['quantityInHand'] = 0;
                $input['itemCode'] = null;
            }

            $input['itemCategoryID'] = 0;

            $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->create($input);

            if($purchaseRequestDetails['itemCode'] != null) {
                array_push($insertedItems, $purchaseRequestDetails['itemCode']);
            }
        }

        $x = count($insertedItems);
        if($x == 0){
            return $this->sendError("No Items were added");
        }
        DB::commit();

        return $this->sendResponse($errors, 'Purchase Request saved successfully');

    }
        catch (\Exception $exception) {
        DB::rollBack();
        return $this->sendError($exception->getMessage());
        }
    }


    /**
     * Display the specified PurchaseRequest Audit.
     * GET|HEAD /purchaseRequestAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function purchaseRequestAudit(Request $request)
    {
        $id = $request->get('id');
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by',
            'cancelled_by', 'manually_closed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->whereIn('documentSystemID', [1, 50, 51]);
            },'audit_trial.modified_by'])->findWithoutFail($id);

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
            'priority_pdf', 'location_pdf', 'details', 'company', 'approved_by',
            'PRConfirmedBy', 'PRConfirmedByEmpName','currency_by',
            'PRConfirmedBySystemID', 'PRConfirmedDate', 'segment','requestedby']);
        $input = $this->convertArrayToValue($input);

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError('This Purchase Request closed. You cannot edit.', 500);
        }

        if ($purchaseRequest->approved == 1) {
            return $this->sendError('This Purchase Request fully approved. You cannot edit.', 500);
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }


        if($input['serviceLineSystemID'] != $purchaseRequest->serviceLineSystemID){

            $documentCodeTransaction = DocumentCodeTransaction::where('document_system_id',$purchaseRequest->documentSystemID)
            ->where('company_id', $purchaseRequest->companySystemID)
            ->first();
            $isSegmentFormat =0;
            if ($documentCodeTransaction) {
                $transactionID = $documentCodeTransaction->id;
                $documentCodeMaster = DocumentCodeMaster::where('document_transaction_id', $transactionID)
                    ->where('company_id', $purchaseRequest->companySystemID)
                    ->first();

                $codeSetup = DocCodeSetupCommon::where('document_transaction_id', $transactionID)
                                    ->where('company_id', $purchaseRequest->companySystemID)
                                    ->first();
                if ($codeSetup) {
                    // Iterate over all the 'format' fields dynamically
                    for ($i = 1; $i <= 12; $i++) {
                        $field = 'format' . $i;
                        if ($codeSetup->$field == 3) {
                            $isSegmentFormat = 1;
                            break; // Stop checking after the first match
                        }
                    }
                }
                
                if($isSegmentFormat == 1){
                    if ($documentCodeMaster) {
                        $documentCodeMasterID = $documentCodeMaster->id;
                        $purchaseRequestCode = $this->documentCodeConfigurationService->getDocumentCodeConfiguration($purchaseRequest->documentSystemID, $purchaseRequest->companySystemID, $input, 0, $documentCodeMasterID, $input['serviceLineCode'], $purchaseRequest->serialNumber);
                    }
                }

            }
            
            if($isSegmentFormat == 1){
                if($purchaseRequestCode && $purchaseRequestCode['status'] == true){
                    $input['purchaseRequestCode'] = $purchaseRequestCode['documentCode'];
                } else {
                    $code = str_pad($purchaseRequest->serialNumber, 6, '0', STR_PAD_LEFT);
                    $input['purchaseRequestCode'] = $purchaseRequest->companyID . '\\' . $purchaseRequest->departmentID . '\\' . $input['serviceLineCode'] . '\\' . $purchaseRequest->documentID . $code;
                }
            }
        }

        if (!empty($input['buyerEmpSystemID'])) {
            $buyerInfo = Employee::find($input['buyerEmpSystemID']);
            if ($buyerInfo) {
                $input['buyerEmpID'] = $buyerInfo->empID;
                $input['buyerEmpName'] = $buyerInfo->empName;
                $input['buyerEmpEmail'] = $buyerInfo->empEmail;
            }
        }

        if (!empty($input['internalNotes']) && strlen($input['internalNotes']) > 250) {
            return $this->sendError('Internal notes should be less than or equal to 250 characters', 500);
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        $input['modifiedUserSystemID'] = $user->employee['employeeSystemID'];


        if (isset($input['budgetYearID']) && $input['budgetYearID'] > 0) {
            $checkCompanyFinanceYear = CompanyFinanceYear::find($input['budgetYearID']);
            if ($checkCompanyFinanceYear) {
                $input['prBelongsYearID'] = $input['budgetYearID'];
                $input['budgetYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                $input['prBelongsYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
            }
        }

        if ($purchaseRequest->PRConfirmedYN == 0 && $input['PRConfirmedYN'] == 1) {

            if ($purchaseRequest->comments == null || $purchaseRequest->comments == "") {
                return $this->sendError('Comment cannot be empty.', 500);
            }

            if ($purchaseRequest->location == null || $purchaseRequest->location == 0) {
                return $this->sendError('Location cannot be empty.', 500);
            }

            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $purchaseRequest->companySystemID)
                ->first();

            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;

                if ($policy == 0) {
                    if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                        return $this->sendError('Category is not found.', 500);
                    }

                    //checking if item category is same or not
                    $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                        ->get();

                    if (sizeof($pRDetailExistSameItem) > 1) {
                        return $this->sendError('You cannot add different category item', 500);
                    }
                }
            }

            $checkItems = PurchaseRequestDetails::where('purchaseRequestID', $id)
                ->count();

            if ($checkItems == 0) {
                return $this->sendError('Every request should have at least one item', 500);
            }

            $checkQuantity = PurchaseRequestDetails::where('purchaseRequestID', $id)
                ->where('quantityRequested', '<=', 0)
                ->count();


            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }

            $checkAltUnit = PurchaseRequestDetails::where('purchaseRequestID', $id)->where('altUnit','!=',0)->whereNull('altUnitValue')->count();

            $allAltUOM = CompanyPolicyMaster::where('companyPolicyCategoryID', 60)
            ->where('companySystemID',  $purchaseRequest->companySystemID)
            ->first();

      
            if ($checkAltUnit > 0 && $allAltUOM->isYesNO) {
                return $this->sendError('Every Alternative UOM should have Alternative UOM Qty', 500);
            }

            $validateAllocatedQuantity = $this->segmentAllocatedItemRepository->validatePurchaseRequestAllocatedQuantity($id);
            if (!$validateAllocatedQuantity['status']) {
                return $this->sendError($validateAllocatedQuantity['message'], 500);
            }

            $amount = PurchaseRequestDetails::where('purchaseRequestID', $id)
                ->sum('totalCost');

            $params = array('autoID' => $id,
                'company' => $purchaseRequest->companySystemID,
                'document' => $purchaseRequest->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => $input['financeCategory'],
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            $datas =  PulledItemFromMR::where('purcahseRequestID',$id)->where('pr_qnty',0)->get();

            if(isset($datas)) {
             foreach($datas as $data) {
                 $request = MaterielRequest::where('RequestID',$data->RequestID)->first();
                if($request->isSelectedToPR && count($request) == 1) {
                    $request->isSelectedToPR = false;
                    $request->save();
                }
             }
             PulledItemFromMR::where('purcahseRequestID',$id)->where('pr_qnty',0)->delete();
            }

            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            } else {
                $input['budgetBlockYN'] = 0;
            }
        }

        $purchaseRequest = $this->purchaseRequestRepository->update($input, $id);

        return $this->sendReponseWithDetails($purchaseRequest->toArray(), 'PurchaseRequest updated successfully',1,$confirm['data'] ?? null);
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
        if($request->input('documentSystemID') && ($request->input('documentSystemID') == 117 || $request->input('documentSystemID') == 118) ){ 
            $id = $request->input('documentSystemCode');
            $documentModifyRequestRepo = app(DocumentModifyRequestRepository::class); 
            $controller = new DocumentModifyRequestAPIController($documentModifyRequestRepo); 

            $documentApprovedRepo = app(DocumentApprovedRepository::class); 
            $documentApprovedController = new DocumentApprovedAPIController($documentApprovedRepo);
            
            $tenderData = $documentApprovedController->getTenderData($id); 

            $requestData = $request->all();  
            $requestData['reference_document_id'] = 108;
            $requestData['bid_submission_opening_date'] = $tenderData->tenderMaster->bid_submission_opening_date; 
            $requestData['id'] = $tenderData->tenderMaster->id; 
            $request->merge($requestData);  
            $result = $controller->approveEditDocument($request);
            return $result;
        }else { 
            $approve = \Helper::approveDocument($request);
            if (!$approve["success"]) {
                return $this->sendError($approve["message"]);
            } else {
                $more_data = ( array_key_exists('data', $approve) )? $approve['data']: [];
                return $this->sendResponse($more_data, $approve["message"]);
            }
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
        $apiKey = $request->input('api_key');
        $request->except('api_key');

        if ($request->input('documentSystemID') && ($request->input('documentSystemID') == 107 ))
        {
            $documentApprovedRepo = app(DocumentApprovedRepository::class);
            $controller = new DocumentApprovedAPIController($documentApprovedRepo);
            $controllerApprovalStatus =  $controller->getController();
            $requestData['id'] =$request->input('documentSystemCode');
            $requestData['api_key'] =$apiKey;
            $requestData['uuid'] = $controller->getSupplierUUID($requestData['id']);
            $request->merge($requestData);
            $result = $controllerApprovalStatus->rejectSupplierKYC($request);
            return $result;
        }else {
            $reject = \Helper::rejectDocument($request);
            if (!$reject["success"]) {
                return $this->sendError($reject["message"]);
            } else {
                return $this->sendResponse(array(), $reject["message"]);
            }
        }
    }

    /**
     * Cancel Purchase Request pre check
     * Post /cancelPurchaseRequest
     *
     * @param $request
     *
     * @return Response
     */
    public function cancelPurchaseRequestPreCheck(Request $request)
    {

        $input = $request->all();
        $purchaseRequest = PurchaseRequest::find($input['purchaseRequestID']);
        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError('You cannot cancel this request as it is already cancelled');
        }

        if ($purchaseRequest->manuallyClosed == 1) {
            return $this->sendError('You cannot cancel this request as it is closed manually');
        }

        $checkPo = PurchaseOrderDetails::where('purchaseRequestID', $input['purchaseRequestID'])->count();

        if ($checkPo > 0) {
            return $this->sendError('Cannot cancel. Order is created for this request');
        }

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully canceled');

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

        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError('You cannot cancel this request as it is already cancelled');
        }

        if ($purchaseRequest->manuallyClosed == 1) {
            return $this->sendError('You cannot cancel this request as it is closed manually');
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

        AuditTrial::createAuditTrial($purchaseRequest->documentSystemID,$input['purchaseRequestID'],$input['cancelledComments'],'cancelled');

        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseRequest->purchaseRequestCode;

        $body = '<p>' . $cancelDocNameBody . ' is cancelled by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['cancelledComments'] . '</p>';
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
            return $this->sendError($sendEmail["message"], 500);
        }
        
        CancelDocument::sendEmail($input);

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully canceled');

    }

    /**
     * Return to amend Purchase Request pre check
     * Post /returnPurchaseRequest
     *
     * @param $request
     *
     * @return Response
     */
    public function returnPurchaseRequestPreCheck(Request $request)
    {
        $input = $request->all();
        $purchaseRequest = PurchaseRequest::with(['confirmed_by'])->find($input['purchaseRequestID']);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->manuallyClosed == 1) {
            return $this->sendError('You cannot revert back this request as it is closed manually');
        }

        $checkPo = PurchaseOrderDetails::where('purchaseRequestID', $input['purchaseRequestID'])->count();

        if ($checkPo > 0) {
            return $this->sendError('Cannot return back to amend. Order is created for this request');
        }

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully return back to amend');
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

        if ($purchaseRequest->manuallyClosed == 1) {
            return $this->sendError('You cannot revert back this request as it is closed manually');
        }

        $checkPo = PurchaseOrderDetails::where('purchaseRequestID', $input['purchaseRequestID'])->count();

        if ($checkPo > 0) {
            return $this->sendError('Cannot return back to amend. Order is created for this request');
        }

        $employee = \Helper::getEmployeeInfo();

        $emails = array();
        $ids_to_delete = array();

        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseRequest->purchaseRequestCode;

        $body = '<p>' . $cancelDocNameBody . ' is return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['ammendComments'] . '</p>';
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
        $purchaseRequest->PRConfirmedBy = NULL;
        $purchaseRequest->PRConfirmedByEmpName = NULL;
        $purchaseRequest->PRConfirmedBySystemID = NULL;
        $purchaseRequest->PRConfirmedDate = NULL;
        $purchaseRequest->approved = 0;
        $purchaseRequest->approvedDate = NULL;
        $purchaseRequest->approvedByUserID = NULL;
        $purchaseRequest->approvedByUserSystemID = NULL;
        $purchaseRequest->RollLevForApp_curr = 1;
        $purchaseRequest->save();

        AuditTrial::createAuditTrial($purchaseRequest->documentSystemID,$input['purchaseRequestID'],$input['ammendComments'],'returned back to amend');

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {

            if ($da->approvedYN == -1) {
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
            return $this->sendError($sendEmail["message"], 500);
        }

        DocumentApproved::destroy($ids_to_delete);

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully return back to amend');
    }

    /**
     * manual Close Purchase Request pre check
     * Post /manualClosePurchaseRequestPreCheck
     *
     * @param $request
     *
     * @return Response
     */
    public function manualClosePurchaseRequestPreCheck(Request $request)
    {

        $input = $request->all();
        $purchaseRequest = PurchaseRequest::with(['confirmed_by', 'details'])->find($input['purchaseRequestID']);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->manuallyClosed == 1) {
            return $this->sendError('This request already manually closed');
        }

        if ($purchaseRequest->selectedForPO != 0 || $purchaseRequest->supplyChainOnGoing != 0 || $purchaseRequest->prClosedYN != 0) {
            return $this->sendError('You cannot close this, request is currently processing');
        }

        if ($purchaseRequest->approved != -1 || $purchaseRequest->cancelledYN == -1) {
            return $this->sendError('You can only close approved request');
        }

        return $this->sendResponse($purchaseRequest, 'Purchase Request successfully closed');
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
        $purchaseRequest = PurchaseRequest::with(['confirmed_by', 'details'])->find($input['purchaseRequestID']);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->manuallyClosed == 1) {
            return $this->sendError('This request already closed');
        }

        if ($purchaseRequest->selectedForPO != 0 || $purchaseRequest->supplyChainOnGoing != 0 || $purchaseRequest->prClosedYN != 0) {
            return $this->sendError('You cannot close this, request is currently processing');
        }

        if ($purchaseRequest->approved != -1 || $purchaseRequest->cancelledYN == -1) {
            return $this->sendError('You can only close approved request');
        }

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

        $purchaseDetails = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
            ->where('selectedForPO', 0)
            ->where('fullyOrdered', '!=', 2)
            ->get();

        foreach ($purchaseDetails as $det) {

            $detail = PurchaseRequestDetails::where('purchaseRequestDetailsID', $det['purchaseRequestDetailsID'])->first();

            if ($detail) {
                if ($detail->selectedForPO == 0 and $detail->fullyOrdered != 2) {
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

        AuditTrial::createAuditTrial($purchaseRequest->documentSystemID,$input['purchaseRequestID'],$input['manuallyClosedComment'],'manually closed');

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {
            if ($da->approvedYN == -1) {
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
            return $this->sendError($sendEmail["message"], 500);
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

        $isFromPortal = $request->get('isFromPortal', 0);


        /** @var PurchaseRequest $purchaseRequest */
        
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by','segment','requestedby',
            'priority_pdf', 'location', 'details.uom','details.altUom', 'company','currency_by','buyer', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('rejectedYN', 0)
                    ->whereIn('documentSystemID', [1, 50, 51]);
            }
        ])->findWithoutFail($id);


        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $checkAltUOM = CompanyPolicyMaster::where('companyPolicyCategoryID', 60)
        ->where('companySystemID', $purchaseRequest->companySystemID)
        ->first();

        $purchaseRequest['allowAltUom'] = ($checkAltUOM) ? $checkAltUOM->isYesNO : false;

        $array = array('request' => $purchaseRequest);

        if($isFromPortal){
            return $this->sendResponse($array, 'Purchase Request print data');
        }

        $time = strtotime("now");
        $fileName = 'purchase_request_' . $id . '_' . $time . '.pdf';

        $html = view('print.purchase_request', $array);
        $htmlFooter = view('print.purchase_request_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    /**
     * Display the specified PurchaseRequest print.
     * POST|HEAD /getReportOpenRequest
     *
     * @param  int $request
     *
     * @return Response
     */
    public function getReportOpenRequest(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'cancelledYN', 'PRConfirmedYN', 'approved', 'month', 'year','financeCategory','reportType'));


        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $messages = ['toDate.after_or_equal' => 'To Date must be greater than or equal to From Date.'];
        $validator = \Validator::make($request->all(), [
            'fromDate' => 'nullable|date',
            'toDate' => 'nullable|date|after_or_equal:fromDate',
        ], $messages);


        $fromDate = !empty($input['fromDate']) ? Carbon::parse($input['fromDate'])->format('Y-m-d') : null;
        $toDate = !empty($input['toDate']) ? Carbon::parse($input['toDate'])->format('Y-m-d') : null;

        if ($validator->fails()) {
            return $this->sendError($validator->messages(),422);
        }

        $purchaseRequests = $this->getDetails($subCompanies,$input,$request, $serviceLineSystemID, $fromDate, $toDate, $sort);

        $purchaseRequests = collect($purchaseRequests);
        return \DataTables::collection($purchaseRequests)
            ->addColumn('Actions', 'Actions', "Actions")
             ->filter(function ($instance) {  
             })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    /**
     * report Open Request Export.
     * get|HEAD /reportOpenRequestExport
     *
     * @param  int $request
     *
     * @return Response
     */
    public function exportReportOpenRequest(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'selectedForPO','reportType'));
        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $fromDate = !empty($input['fromDate']) ? Carbon::parse($input['fromDate'])->format('Y-m-d') : null;
        $toDate = !empty($input['toDate']) ? Carbon::parse($input['toDate'])->format('Y-m-d') : null;
        $type = $input['type'];
        $purchaseRequests = $this->getDetails($subCompanies,$input,$request, $serviceLineSystemID, $fromDate, $toDate, $sort);


        $data = array();
        foreach ($purchaseRequests as $val) {

            $location = "";
            $priority = "";
            $createdBy = "";
            $serviceLineDes = "";

            if (!empty($val->location_pdf)) {
                $location = $val->location_pdf['locationName'];
            }

            if (!empty($val->priority_pdf)) {
                $priority = $val->priority_pdf['priorityDescription'];
            }

            if (!empty($val->created_by)) {
                $createdBy = $val->created_by->empName;
            }

            if (!empty($val->segment)) {
                $serviceLineDes = $val->segment->ServiceLineDes;
            }

            if($input['reportType'] == 1)
            {
                $data[] = array(
                    'PR Number' => $val->purchaseRequestCode,
                    'PR Requested Date' => \Helper::dateFormat($val->createdDateTime),
                    'Department' => $serviceLineDes,
                    'Narration' => $val->comments,
                    'Location' => $location,
                    'Priority' => $priority,
                    'Created By' => $createdBy,
                    'Confirmed Date' => \Helper::dateFormat($val->PRConfirmedDate),
                    'Approved Date' => \Helper::dateFormat($val->approvedDate),
                );
            }
            else
            {
            $data[] = array(
                'PR Number' => $val->purchaseRequestCode,
                'PR Requested Date' => \Helper::dateFormat($val->createdDateTime),
                'Department' => $serviceLineDes,

                'Item Code' => '',
                'Part No' => '',
                'Item Description' => '',
                'Req Qty' => '',


                'Narration' => $val->comments,
                'Location' => $location,
                'Priority' => $priority,
                'Created By' => $createdBy,
                'Confirmed Date' => \Helper::dateFormat($val->PRConfirmedDate),
                'Approved Date' => \Helper::dateFormat($val->approvedDate),
            );

            if (!empty($val->details)) {
                foreach ($val->details as $detail) {
                    $data[count($data)-1]['details'][] = [
                        'Item Code' => $detail['itemPrimaryCode'],
                        'Part No / Ref.Number' => $detail['partNumber'],
                        'Item Description' => $detail['itemDescription'],
                        'Req Qty' => $detail['quantityRequested']
                    ];
                }
            }

    
            }
        }

        $companyMaster = Company::find(isset($request['companySystemID'][0])?$request['companySystemID'][0]:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $doc_name = 'open_requests';
        $path = 'procurement/open_requests/excel/';


        if(isset($input['reportType']) && $input['reportType'] == 2) {

               $db = $input['db'] ?? "";
               $userId = Helper::getEmployeeSystemID();
               ExportDetailedORList::dispatch($db, $data,$companyCode, $userId);
               return $this->sendResponse('', 'Open Request Detailed report Export in progress, you will be notified once ready !!');
        }

        $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }



    }

    public function getPurchaseRequestReopen(Request $request)
    {
        $input = $request->all();
        $add = app()->make(PurcahseRequestDetail::class);
        $purchaseRequestReopen = $add->purchaseRequestReopen($input);
        
        if($purchaseRequestReopen['status'] = false){
            return $this->sendError($purchaseRequestReopen['message']);
        }else {
        return $this->sendResponse($purchaseRequestReopen, 'Purchase Request reopened successfully');
        }

    }

    public function getPurchaseRequestReferBack(Request $request)
    {
        $input = $request->all();

        $purchaseRequestId = $input['purchaseRequestId'];

        $purchaseRequest = PurchaseRequest::find($purchaseRequestId);
        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this request');
        }

        $purchaseRequestArray = $purchaseRequest->toArray();


        $storePORequestHistory = PurchaseRequestReferred::insert($purchaseRequestArray);

        $fetchPurchaseRequestDetails = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequestId)
            ->get();

        if (!empty($fetchPurchaseRequestDetails)) {
            foreach ($fetchPurchaseRequestDetails as $prDetail) {
                $prDetail['timesReffered'] = $purchaseRequest->timesReferred;
            }
        }

        $purchaseRequestDetailArray = $fetchPurchaseRequestDetails->toArray();


        
        $storePRDetailHistory = PrDetailsReferedHistory::insert($purchaseRequestDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $purchaseRequestId)
            ->where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $purchaseRequest->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $DocumentApprovedArray = Arr::except($DocumentApprovedArray, ['status']);
        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $purchaseRequestId)
            ->where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $purchaseRequest->refferedBackYN = 0;
            $purchaseRequest->PRConfirmedYN = 0;
            $purchaseRequest->PRConfirmedBySystemID = null;
            $purchaseRequest->PRConfirmedBy = null;
            $purchaseRequest->PRConfirmedByEmpName = null;
            $purchaseRequest->PRConfirmedDate = null;
            $purchaseRequest->RollLevForApp_curr = 1;
            $purchaseRequest->save();
        }

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request Amend successfully');
    }

    public function amendPurchaseRequest(Request $request)
    {
        $input = $request->all();

        $purchaseRequestId = isset($input['purchaseRequestID'])?$input['purchaseRequestID']:0;

        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($purchaseRequestId);
        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->checkBudgetYN == 0) {
            return $this->sendError('Budget check is removed for the selected document.');
        }

        $this->purchaseRequestRepository->update(['checkBudgetYN' => 0],$purchaseRequestId);

        AuditTrial::createAuditTrial($purchaseRequest->documentSystemID,$input['purchaseRequestID'],'','removed budget check');

        return $this->sendResponse($purchaseRequest->toArray(), 'Request budget check removed successfully');
    }

    /**
     * get Approval Details
     * GET /getCancelledDetails
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getCancelledDetails(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];
        $type = $input['type'];
        $cancelList = [];
        if($type == 'PR'){
            $cancelledDetails = PurchaseRequest::where('purchaseRequestID', $id)
                ->with(['cancelled_by'])
                ->first();
            $cancelList = $cancelledDetails;
        }elseif ($type == 'PO'){
            $cancelledDetails = ProcumentOrder::where('purchaseOrderID', $id)
                ->with(['cancelled_by'])
                ->first();
            $cancelList = [
                'cancelledYN'=>$cancelledDetails->poCancelledYN,
                'cancelledDate'=>$cancelledDetails->poCancelledDate,
                'cancelledComments'=>$cancelledDetails->cancelledComments,
                'cancelled_by'=>$cancelledDetails->cancelled_by,
            ];
        }elseif ($type == 'GRV'){
            $cancelledDetails = GRVMaster::where('grvAutoID', $id)
                ->with(['cancelled_by'])
                ->first();
            $cancelList = [
                'cancelledYN'=>$cancelledDetails->grvCancelledYN,
                'cancelledDate'=>$cancelledDetails->grvCancelledDate,
                'cancelledComments'=>'',
                'cancelled_by'=>$cancelledDetails->cancelled_by,
            ];
        }
        return $this->sendResponse($cancelList, 'Record retrieved successfully');
    }

    /**
     * get Approval Details
     * GET /getClosedDetails
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClosedDetails(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];
        $type = $input['type'];
        $closedDetails = [];
        if($type == 'PR'){
            $closedDetails = PurchaseRequestDetails::where('purchaseRequestDetailsID', $id)
                ->with(['closed_by'])
                ->first();
        }elseif ($type == 'PO'){
            $closedDetails = PurchaseOrderDetails::where('purchaseOrderDetailsID', $id)
                ->with(['closed_by'])
                ->first();
        }
        return $this->sendResponse($closedDetails, 'Record retrieved successfully');
    }

    /*
     * when hovering document code, show document details
     * */
    public function getDocumentDetails(Request $request){
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $documentSystemCode = $input['documentSystemCode'];
        $documentSystemID = $input['documentSystemID'];
        $matchingDoc = isset($input['matchingDoc'])?$input['matchingDoc']:0;

        $result = Helper::getDocumentDetails($companySystemID,$documentSystemID,$documentSystemCode,$matchingDoc);
        $output['data'] = count($result) > 0?$result->take(10):[];
        $output['is_limit'] =  count($result) > 10?true:false;
        $output['count'] =  count($result);

        return $this->sendResponse($output,'Success');
    }

    public function getPurchaseRequestTotal(Request $request)
    {
        $input = $request->all();

        $purchaseRequestID = $input['purchaseRequestID'];

        $totalAmount = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequestID)
            ->sum('totalCost');

        $purchaseArray = (['totalAmount' => $totalAmount]);

        return $this->sendResponse($purchaseArray, 'Data retrieved successfully');
    }


        public function downloadPrItemUploadTemplate(Request $request)
    {
        $input = $request->all();
        $disk =Helper::policyWiseDisk($input['companySystemID'], 'public');
        if ($exists = Storage::disk($disk)->exists('purchase_request_item_upload_template/purchase_request_item_upload_template.xlsx')) {
            return Storage::disk($disk)->download('purchase_request_item_upload_template/purchase_request_item_upload_template.xlsx', 'purchase_request_item_upload_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    public function checkProductExistInIssues($itemCode,$companySystemID) {
        
        $fetchDetails = PurchaseRequestDetails::whereHas('purchase_request', function($q)
        {
            $q->where('approved', 0);
        })->where('itemCode', $itemCode)->get();

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
        ->where('companySystemID', $companySystemID)
        ->first();

        $checkPOPending = ProcumentOrderDetail::whereHas('productmentOrder', function($q)
        {
            $q->where('approved', 0);
        })->where('itemCode', $itemCode)->get();
        


        if($allowPendingApproval->isYesNO != 0) {
            $data = [
                "status" => false,
                "data" => [],
                "policy" => false
            ];
            return $this->sendResponse($data, 'Data not found!');
        }else {
            if(count($fetchDetails) > 0) {
                $data = [
                    "status" => true,
                    "data" => $fetchDetails,
                    "policy" => true,
                    "message" =>  "PR / PO available for these items"
                ];
                return $this->sendResponse($data, 'Data retreived successfully');
            }else {
                if(count($checkPOPending) > 0) {
                    $data = [
                        "status" => true,
                        "policy" => true,
                        "po" => $checkPOPending,
                        "data" => $fetchDetails,
                        "message" => "PR / PO available for these items"
                    ];
                    return $this->sendResponse($data, 'Data retreived successfully');
    
                }else {
                    $data = [
                        "status" => false,
                        "policy" => true,
                        "data" => []
                    ];
                    return $this->sendResponse($data, 'Data not found!');
                }
            }
        }

    }

    public function pullMrDetails(Request $request) {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved','cancelledYN'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $dataArray = [];
        $search = $request->input('search.value');
        $slectedMaterialRequestArray = PulledItemFromMR::where('purcahseRequestID',$input['purcahseRequestID'])->pluck('RequestID')->toArray();
        $materielRequests = MaterielRequest::where('approved',$input['approved'])
        ->where('erp_request.cancelledYN',$input['cancelledYN'])
        ->where('erp_request.serviceLineSystemID',$input['serviceLineSystemID'])
        ->where('erp_request.companySystemID',$input['companySystemID'])
        ->where('erp_request.isSelectedToPR',0)
        ->with('details')->get();


        $slectedMaterialRequest = MaterielRequest::whereIn('RequestID',$slectedMaterialRequestArray)->with('details')->get();
        foreach($materielRequests as $ms) {
            array_push($dataArray,$ms);
        }

        foreach($slectedMaterialRequest as $sm) {
            array_push($dataArray,$sm);
        }

      
        $mrDatas =  collect($dataArray);
        $sorted_mrDatas =  $mrDatas->sortBy('RequestID');

        $filtered = $sorted_mrDatas->filter(function ($value, $key) {
         return $value->materialIssueStatusValue == "Pending";
        });

        $filtered->all();

        foreach($filtered as $materielRequest) {
            $details = $materielRequest->details;
            foreach($details as $detail) {
                if($detail->item_by) {
                    $pulledItem = PulledItemFromMR::where('RequestID',$materielRequest->RequestID)
                    ->where('itemPrimaryCode', $detail->item_by->primaryItemCode)->first();
                    $detail['itemPrimaryCode'] = $detail->item_by->primaryItemCode;
                    if($pulledItem) {
                        if($detail->item_by->itemCodeSystem == $pulledItem->itemCodeSystem) {
                            $detail['prRqQnty'] = $pulledItem->pr_qnty;
                            $detail['isChecked'] = true;
                        }else {
                            $detail['isChecked'] = false;
                        }
                        $materielRequest['isChecked'] = true;

                    }else {
                        $detail['isChecked'] = false;
                    }
                }
            }
        }

        $dataArray = [];
        foreach($filtered as $filterData) {
            array_push($dataArray,$filterData);
        }
        return $this->sendResponse($dataArray, 'Data retreived Successfully!');
    }

    public function confirmDocument(Request $request)
    {
        $input = $request->all();

        if (!isset($input['autoID'])) {
            return ['success' => false, 'message' => 'Parameter documentSystemID is missing'];
        }

        if (!isset($input['company'])) {
            return ['success' => false, 'message' => 'Parameter company is missing'];
        }

        if (!isset($input['document'])) {
            return ['success' => false, 'message' => 'Parameter document is missing'];
        }

        $params =  array(
            'autoID' => $input['autoID'],
            'company' =>  $input['company'],
            'document' => $input['document'],
            'segment' => '',
            'category' => '',
            'amount' => $input['amount'],
        );

        $approve = \Helper::confirmDocument($params);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            $more_data = ( array_key_exists('data', $approve) )? $approve['data']: [];
            return $this->sendResponse($more_data, $approve["message"]);
        }
    }


    public function getItemQntyByPR(Request $request) {
        $input = $request->input();
        $item = $input['item'];
        $purchase_id = $input['id'];
        $requests = PulledItemFromMR::where('purcahseRequestID',$purchase_id)->where('itemCodeSystem',$item['itemCode'])->groupBy('itemCodeSystem')->selectRaw('sum(pr_qnty) as sum')->first();
        $total_requested_qnty =  PulledItemFromMR::where('purcahseRequestID',$purchase_id)->where('itemCodeSystem',$item['itemCode'])->groupBy('itemCodeSystem')->selectRaw('sum(mr_qnty) as sum')->first();
        $requestedQnty = $item['quantityRequested'];

        if(isset($total_requested_qnty)) {
            if($requestedQnty >  $total_requested_qnty->sum) {
                return  $this->sendError('Requested Quantity can not be greater than materiel requested Quantity');
            }
        }

        if(isset($requests)) {
                        if($requestedQnty > $requests->sum) {
                            $pulledDetails = PulledItemFromMR::where('purcahseRequestID',$purchase_id)->where('itemCodeSystem',$item['itemCode'])->orderBy('RequestID', 'ASC')->get();
                            $qntyToUpdateOnNextItem = (($requestedQnty) - $requests->sum);
                            foreach($pulledDetails as $pulledDetail) {
                                $mrQnty = $pulledDetail->mr_qnty;
                                if($mrQnty != $pulledDetail->pr_qnty) {
                                    if($qntyToUpdateOnNextItem > 0 ) {
                                        if($qntyToUpdateOnNextItem > $mrQnty) {
                                            $maxQntyToUpdate = $mrQnty;
                                            if($mrQnty >=($pulledDetail->pr_qnty+$qntyToUpdateOnNextItem)) {
                                                $pulledDetail->pr_qnty += $mrQnty - ($pulledDetail->pr_qnty+$qntyToUpdateOnNextItem) ;
                                                $qntyToUpdateOnNextItem = 0;
                                                $pulledDetail->save();
                                            }
                                        }else {   
                                            if($pulledDetail->pr_qnt != $mrQnty) {
                                                if($pulledDetail->pr_qnty != 0) {
                                                    $diff = $qntyToUpdateOnNextItem;
                                                    if($mrQnty >= ($diff + $pulledDetail->pr_qnty)) {
                                                        $pulledDetail->pr_qnty += $diff;
                                                        $qntyToUpdateOnNextItem -= $diff;
                                                        $pulledDetail->save();
                                                    }else {
                                                        $qntyToUpdateOnNextItem =  $diff - ($mrQnty - $pulledDetail->pr_qnty);
                                                        $pulledDetail->pr_qnty = $mrQnty;
                                                        $pulledDetail->save();
                                                    }
                                                }else {
                                                   
                                                    $pulledDetail->pr_qnty += $qntyToUpdateOnNextItem;
                                                    $qntyToUpdateOnNextItem = 0;
                                                    $pulledDetail->save();
    
                                                }
                                            }     
                                        }
                                    }else {
                                        $pulledDetail->pr_qnty = 0;
                                    }
                                }
                            }
            
                        }else {
                            $pulledDetails = PulledItemFromMR::where('purcahseRequestID',$purchase_id)->where('itemCodeSystem',$item['itemCode'])->orderBy('RequestID', 'DESC')->get();
                          
                            $qntyToUpdateOnNextItem = $requestedQnty;
                            $qntyToUpdate = null;
                            $difference = ($requests->sum - ($requestedQnty));
                            foreach($pulledDetails as $pulledDetail) {
                                if($difference > 0){
                                    if(($pulledDetail->pr_qnty) != 0 ) {
                                        if($difference > $pulledDetail->pr_qnty) {
                                            if($qntyToUpdate == null) {
                                                $qntyToUpdate = $pulledDetail->pr_qnty;
                                            }
                                            $pulledDetail->pr_qnty -= $qntyToUpdate;
                                            $qntyToUpdate = $difference - $qntyToUpdate; 
                                            $difference = $qntyToUpdate;
                                            $pulledDetail->save();
                                        }else {
                                            if(($pulledDetail->pr_qnty  - $difference) > 0) {
                                                $pulledDetail->pr_qnty =  $pulledDetail->pr_qnty  - $difference;
                                                $difference = 0;
                                                $pulledDetail->save();
                                            }else {
                                                $difference -=  $pulledDetail->pr_qnty;
                                                $qntyToUpdate = $difference;
                                                $pulledDetail->pr_qnty = 0;
                                                $pulledDetail->save();
                                            }
                                        }  
                                    }

                                }
                            }
                        }

           
            return $this->sendResponse($requests, 'Quantity updated successfully!');
        }else {
            return $this->sendResponse($requests, 'Data not found!');
        }            
    }

    public function isPulledFromMR(Request $request) {
        $input = $request->all();
        $pulledDetails = PulledItemFromMR::where('purcahseRequestID',$input['purcahseRequestID'])->get();
        if(count($pulledDetails) > 0) {
            return $this->sendResponse(true, 'Data found!');
        }else {
            return $this->sendResponse(false, 'Data not found!');
        }
    }

    public function delteItemQntyPR(Request $request) {
        $input = $request->all();
        $item = $input['item'];
        $datas = PulledItemFromMR::where('purcahseRequestID',$input['id'])->where('itemCodeSystem',$item['itemCode'])->get();
        if(isset($datas)) {
            foreach($datas as $data) {
                $id =$data->RequestID;
                $request = MaterielRequest::find($id);
                $request->isSelectedToPR =  false;
                $request->save();
            }
        }
        PulledItemFromMR::where('purcahseRequestID',$input['id'])->where('itemCodeSystem',$item['itemCode'])->delete();
    }

    public function validateItem(Request $request) {
        $input = $request->all();
        $add = app()->make(PurcahseRequestDetail::class);
        $purchaseRequestDetailsValidation = $add->validateItemOnly($input);
        return $purchaseRequestDetailsValidation;
    }


     /**
     * get UOM Option For PurchaseRequest
     * get /get-all-uom-options
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllUomOptions(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $policy = 1;

        $financeCategoryId = 0;

        $allAltUOM = CompanyPolicyMaster::where('companyPolicyCategoryID', 60)
            ->where('companySystemID', $companyId)
            ->first();

        if ($allAltUOM) {
            $policy = $allAltUOM->isYesNO;

            if ($policy == 0) {
                return $this->sendError('Policy not found');
            }
        }

        $units = Unit::all();

        return $this->sendResponse($units->toArray(), 'Data retrieved successfully');
    }


    public function getItemsForOpenRequest(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];
        $category = $input['category'] ?? null;

        $items = ItemAssigned::where('companySystemID', $companyId)
        ->where('isActive', 1)
        ->where('isAssigned', -1)
        ->whereHas('pr_detail', function ($query) {
            $query->whereHas('purchase_request', function ($query) {
                $query->where('approved', -1)
                ->where('manuallyClosed', 0)
                ->where('cancelledYN', 0)
                ->where('selectedForPO', 0)
                ->where('prClosedYN', 0);
            });
        })
        ->when($category, function ($query) use ($category) {
            return $query->where('financeCategoryMaster', $category);
        })
        ->groupBy('itemCodeSystem')->get();

        return $this->sendResponse($items, 'Data retrieved successfully');
    }


    public function getDetails($subCompanies,$input,$request, $serviceLineSystemID, $fromDate, $toDate, $sort)
    {
        $purchaseRequests = PurchaseRequest::whereIn('companySystemID', $subCompanies)
            ->where('approved', -1)
            ->where('manuallyClosed', 0)
            ->where('cancelledYN', 0)
            ->where('prClosedYN', 0)
            ->with(['created_by', 'priority', 'location', 'segment'])
            ->orderBy('purchaseRequestID', $sort);

        if (array_key_exists('selectedForPO', $input)) {
            if ($input['selectedForPO'] && !is_null($input['selectedForPO'])) {
                if ($input['selectedForPO'] == 1) {
                    $purchaseRequests = $purchaseRequests->whereDoesntHave('po_details');
                } elseif ($input['selectedForPO'] == 2) {
                    $purchaseRequests = $purchaseRequests->whereHas('po_details');
                }
            }
        }

        $search = $request->input('search.value');
        $items = [];

        $items = [];
        if (array_key_exists('item', $input)) {
            $items = collect((array)$input['item'])->pluck('id')->toArray();
        }
   


        if ($input['reportType'] == 2 ) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where(function ($query) use ($items, $search) {
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('purchaseRequestCode', 'like', "%{$search}%")
                        ->orWhere('comments', 'like', "%{$search}%");
                    });
                }

                $query->orWhereHas('details', function ($q) use ($items, $search) {
                    if (!empty($items)) {
                        $q->whereIn('itemCode', $items);
                    }

                    if (!empty($search)) {
                        $q->where(function ($sq) use ($search) {
                            $sq->where('itemPrimaryCode', 'like', "%{$search}%")
                            ->orWhere('itemDescription', 'like', "%{$search}%")
                            ->orWhere('partNumber', 'like', "%{$search}%");
                        });
                    }
                });
            });

            $purchaseRequests = $purchaseRequests->with(['details' => function ($query) use ($items, $search) {
                           $query->with(['podetail' => function ($q) {
                                $q->with(['order']);
                            }]);
                        if (!empty($items)) {
                            $query->whereIn('itemCode', $items);
                        }

                        if (!empty($search)) {
                            $query->where(function ($q) use ($search) {
                                $q->where('itemPrimaryCode', 'like', "%{$search}%")
                                ->orWhere('itemDescription', 'like', "%{$search}%")
                                ->orWhere('partNumber', 'like', "%{$search}%");
                            });
                        }
                    }]);
        } else {
              $search = str_replace("\\", "\\\\", $search);
           $purchaseRequests = $purchaseRequests->with(['details' => function ($query) use ($items, $search) {
                           $query->with(['podetail' => function ($q) {
                                $q->with(['order']);
                            }]);
                    }])
                    ->when(!empty($search), function ($query) use ($search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('purchaseRequestCode', 'like', "%{$search}%")
                            ->orWhere('comments', 'like', "%{$search}%");
                        });
                    });

        }
  
        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseRequests = $purchaseRequests->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if ($fromDate && $toDate) {
            $purchaseRequests = $purchaseRequests->whereDate('createdDateTime', '>=', $fromDate)->whereDate('createdDateTime', '<=', $toDate);;
        } elseif ($fromDate) {
            $purchaseRequests = $purchaseRequests->whereDate('createdDateTime', '>=', $fromDate);
        } elseif ($toDate) {
            $purchaseRequests = $purchaseRequests->whereDate('createdDateTime', '<=', $toDate);
        }

    
        if (array_key_exists('financeCategory', $input)) {
            if(is_array($input['financeCategory']))
            {
              $fin_cat = $input['financeCategory'][0];
            }
            else
            {
               $fin_cat = $input['financeCategory'];
            }
       
           if ($fin_cat && !is_null($fin_cat)) {
          
               $purchaseRequests = $purchaseRequests->where('financeCategory', $input['financeCategory']);
           }
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
                'erp_purchaserequest.manuallyClosed',
                'erp_purchaserequest.PRConfirmedDate',
                'erp_purchaserequest.approvedDate',
            ]);

          
            $purchaseRequests=  $purchaseRequests->get();
            
            $result = $this->filterPurchaseRequest($purchaseRequests);
       

            return $result;
    }

    public function filterPurchaseRequest($purchaseRequests)
    {
        foreach ($purchaseRequests as $prIndex => $pr) {
            $newDetails = [];

            foreach ($pr->details as $index=>$key) {
                $poQtySum = 0;
                $balance = 0;
                if (!empty($key['podetail'])) {
                    foreach ($key['podetail'] as $poDetail) {
                        if (
                            !empty($poDetail['order']) &&
                            isset($poDetail['order']['approved']) &&
                            $poDetail['order']['approved'] == -1
                        ) {
                            $poQtySum += floatval($poDetail['noQty']);
                        }
                    }
                }

                $balance = floatval($key['quantityRequested']) - $poQtySum;
                $pr->details[$index]['quantityRequested'] = $balance;
            

                if ($balance != 0) {
                    $key['quantityRequested'] = $balance;
                    $newDetails[] = $key;
                }

            }
                $pr->setRelation('details', collect($newDetails));
            }

            return $purchaseRequests;
    }
}
