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
use App\Http\Requests\API\CreatePurchaseRequestAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use Illuminate\Support\Facades\Storage;
use App\Models\EmployeesDepartment;
use App\Models\FinanceItemCategoryMaster;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemAssigned;
use App\Models\Location;
use App\Models\Months;
use App\Models\PrDetailsReferedHistory;
use App\Models\Priority;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequestReferred;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\UserRepository;
use App\Traits\AuditTrial;
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

        $segments = SegmentMaster::whereIn("companySystemID", $childCompanies);

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

        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
            ->where('companySystemID', $companyId)
            ->first();


        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0, 'allowItemToType' => 0);

        if ($checkBudget) {
            $conditions['checkBudget'] = $checkBudget->isYesNO;
        }

        if ($allowFinanceCategory) {
            $conditions['allowFinanceCategory'] = $allowFinanceCategory->isYesNO;
        }

        if ($allowItemToType) {
            $conditions['allowItemToType'] = $allowItemToType->isYesNO;
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

                    $prd->whereHas('podetail', function ($pod) use ($from, $to, $documentSearch) {
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
                    })
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

                    $q->whereHas('podetail', function ($q) use ($from, $to, $documentSearch) {

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
                    });

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
                $data[$x]['Service Line'] = $value->serviceLineCode;
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
                            $data[$x]['Service Line'] = '';
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
                        $data[$x]['Part Number'] = $item->partNumber;
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
                                    $data[$x]['Service Line'] = '';
                                    $data[$x]['PR Number'] = '';
                                    $data[$x]['Processed By'] = '';
                                    $data[$x]['PR Date'] = '';
                                    $data[$x]['PR Comment'] = '';
                                    $data[$x]['PR Approved'] = '';
                                    $data[$x]['PR Status'] = '';
                                    $data[$x]['Item Code'] = '';
                                    $data[$x]['Item Description'] = '';
                                    $data[$x]['Part Number'] = '';
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
                                            $data[$x]['Service Line'] = '';
                                            $data[$x]['PR Number'] = '';
                                            $data[$x]['Processed By'] = '';
                                            $data[$x]['PR Date'] = '';
                                            $data[$x]['PR Comment'] = '';
                                            $data[$x]['PR Approved'] = '';
                                            $data[$x]['PR Status'] = '';
                                            $data[$x]['Item Code'] = '';
                                            $data[$x]['Item Description'] = '';
                                            $data[$x]['Part Number'] = '';
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
                    $data[$x]['Part Number'] = '';
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

         \Excel::create('pr_to_grv', function ($excel) use ($data) {
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
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'cancelledYN', 'PRConfirmedYN', 'approved', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $purchaseRequests = $this->purchaseRequestRepository->purchaseRequestListQuery($request, $input, $search);

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
            ->where('erp_documentapproved.approvedYN', 0)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('financeitemcategorymaster', 'financeCategory', 'financeitemcategorymaster.itemCategoryID')
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

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
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

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $input['PRRequestedDate'] = now();

        $input['departmentID'] = 'PROC';

        $lastSerial = PurchaseRequest::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->orderBy('purchaseRequestID', 'desc')
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
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by','currency_by',
            'priority_pdf', 'location_pdf', 'details.uom', 'company', 'segment', 'approved_by' => function ($query) {
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
            'PRConfirmedBySystemID', 'PRConfirmedDate', 'segment']);
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
            $code = str_pad($purchaseRequest->serialNumber, 6, '0', STR_PAD_LEFT);
            $input['purchaseRequestCode'] = $purchaseRequest->companyID . '\\' . $purchaseRequest->departmentID . '\\' . $input['serviceLineCode'] . '\\' . $purchaseRequest->documentID . $code;
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        $input['modifiedUserSystemID'] = $user->employee['employeeSystemID'];

        if ($purchaseRequest->PRConfirmedYN == 0 && $input['PRConfirmedYN'] == 1) {
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
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            } else {
                $input['budgetBlockYN'] = 0;
            }
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
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by',
            'priority_pdf', 'location', 'details.uom', 'company', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('rejectedYN', 0)
                    ->whereIn('documentSystemID', [1, 50, 51]);
            }
        ])->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        $array = array('request' => $purchaseRequest);
        $time = strtotime("now");
        $fileName = 'purchase_request_' . $id . '_' . $time . '.pdf';

        $html = view('print.purchase_request', $array);

        //return $html;
        //return $this->sendResponse($html->render(), 'Purchase Request retrieved successfully');
        //return \PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->download($fileName);

        // die();

        //  $pdf = \PDF::loadView('print.purchase_request', $array);
        //  return $pdf->download('purchase_request_'.$id.'.pdf');

        $pdf = \App::make('dompdf.wrapper');
        //$pdf->setWatermarkText('example', '150px');

        $text = 'watermark';
        $opacity = 0.9;
        $size = '100px';

        //$pdf->setWatermarkText($text, $size,$opacity, $rotate = '10deg', $top = '30%');
        //$pdf->getDomPDF()->set_option("enable_php", true);

        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request retrieved successfully');
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
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'cancelledYN', 'PRConfirmedYN', 'approved', 'month', 'year','financeCategory'));

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


        $purchaseRequests = PurchaseRequest::whereIn('companySystemID', $subCompanies)
            ->where('approved', -1)
            ->where('manuallyClosed', 0)
            ->where('cancelledYN', 0)
            ->where('selectedForPO', 0)
            ->where('prClosedYN', 0)
            ->with(['created_by', 'priority', 'location', 'segment']);

        if (array_key_exists('selectedForPO', $input)) {
            if ($input['selectedForPO'] && !is_null($input['selectedForPO'])) {
                if ($input['selectedForPO'] == 1) {
                    $purchaseRequests = $purchaseRequests->whereDoesntHave('po_details');
                } elseif ($input['selectedForPO'] == 2) {
                    $purchaseRequests = $purchaseRequests->whereHas('po_details', function ($q) {
                    });
                }
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseRequests = $purchaseRequests->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('financeCategory', $input)) {
            if ($input['financeCategory'] && !is_null($input['financeCategory'])) {
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

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where(function ($query) use ($search) {
                $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
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
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'selectedForPO'));

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


        $type = $input['type'];
        $purchaseRequests = PurchaseRequest::whereIn('companySystemID', $subCompanies)
            ->where('approved', -1)
            ->where('manuallyClosed', 0)
            ->where('cancelledYN', 0)
            ->where('selectedForPO', 0)
            ->where('prClosedYN', 0)
            ->with(['created_by', 'priority_pdf', 'location_pdf', 'segment']);

        if (array_key_exists('selectedForPO', $input)) {
            if ($input['selectedForPO'] && !is_null($input['selectedForPO'])) {
                if ($input['selectedForPO'] == 1) {
                    $purchaseRequests = $purchaseRequests->whereDoesntHave('po_details');
                } elseif ($input['selectedForPO'] == 2) {
                    $purchaseRequests = $purchaseRequests->whereHas('po_details', function ($q) {
                    });
                }
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseRequests = $purchaseRequests->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('financeCategory', $input)) {
            if ($input['financeCategory'] && !is_null($input['financeCategory'])) {
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
            ])->orderBy('purchaseRequestID', 'asc');

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where(function ($query) use ($search) {
                $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        $purchaseRequests = $purchaseRequests->get();

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

         \Excel::create('open_requests', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);
        return $this->sendResponse(array(), 'successfully export');

    }

    public function getPurchaseRequestReopen(Request $request)
    {
        $input = $request->all();

        $purchaseRequestId = $input['purchaseRequestId'];

        $purchaseRequest = PurchaseRequest::find($purchaseRequestId);
        $emails = array();
        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }

        if ($purchaseRequest->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Request it is already partially approved');
        }

        if ($purchaseRequest->approved == -1) {
            return $this->sendError('You cannot reopen this Request it is already fully approved');
        }

        if ($purchaseRequest->PRConfirmedYN == 0) {
            return $this->sendError('You cannot reopen this Request, it is not confirmed');
        }

        // updating fields
        $purchaseRequest->PRConfirmedYN = 0;
        $purchaseRequest->PRConfirmedBySystemID = null;
        $purchaseRequest->PRConfirmedBy = null;
        $purchaseRequest->PRConfirmedByEmpName = null;
        $purchaseRequest->PRConfirmedDate = null;
        $purchaseRequest->RollLevForApp_curr = 1;
        $purchaseRequest->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $purchaseRequest->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseRequest->purchaseRequestCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseRequest->purchaseRequestCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemCode', $purchaseRequest->purchaseRequestID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $purchaseRequest->companySystemID)
                    ->where('documentSystemID', $purchaseRequest->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $purchaseRequest->purchaseRequestID)
            ->where('companySystemID', $purchaseRequest->companySystemID)
            ->where('documentSystemID', $purchaseRequest->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($purchaseRequest->documentSystemID,$purchaseRequest->purchaseRequestID,$input['reopenComments'],'Reopened');

        return $this->sendResponse($purchaseRequest->toArray(), 'Purchase Request reopened successfully');
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

        $output = Helper::getDocumentDetails($companySystemID,$documentSystemID,$documentSystemCode,$matchingDoc);

        return $this->sendResponse($output,'Success');
    }


     public function downloadPrItemUploadTemplate(Request $request)
    {
        $input = $request->all();
        $disk = (isset($input['companySystemID'])) ?  Helper::policyWiseDisk($input['companySystemID'], 'public') : 'public';
        if ($exists = Storage::disk($disk)->exists('purchase_request_item_upload_template/purchase_request_item_upload_template.xlsx')) {
            return Storage::disk($disk)->download('purchase_request_item_upload_template/purchase_request_item_upload_template.xlsx', 'purchase_request_item_upload_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

}
